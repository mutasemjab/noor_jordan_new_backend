<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Talks to Firebase Auth (custom tokens) and Firestore (REST) using the same
 * service-account credentials file already used by FCMController for push.
 */
class FirebaseAdminService
{
    protected static function credentials(): array
    {
        $path = base_path(env('FIREBASE_CREDENTIALS_PATH', ''));

        if (! $path || ! file_exists($path)) {
            throw new \RuntimeException('Firebase credentials file not found.');
        }

        return json_decode(file_get_contents($path), true);
    }

    /**
     * Mint a Firebase custom token for the given uid (e.g. "student_482").
     */
    public static function createCustomToken(string $uid, array $claims = []): string
    {
        $creds = self::credentials();
        $now   = time();

        $payload = [
            'iss' => $creds['client_email'],
            'sub' => $creds['client_email'],
            'aud' => 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit',
            'iat' => $now,
            'exp' => $now + 3600,
            'uid' => $uid,
        ];

        if (! empty($claims)) {
            $payload['claims'] = $claims;
        }

        return JWT::encode($payload, $creds['private_key'], 'RS256');
    }

    /**
     * OAuth2 access token for calling Google REST APIs (Firestore, etc.) with
     * the given scope, cached until shortly before it expires.
     */
    protected static function getAccessToken(string $scope): string
    {
        return Cache::remember('firebase_access_token_' . md5($scope), 3000, function () use ($scope) {
            $client = new GoogleClient();
            $client->setAuthConfig(base_path(env('FIREBASE_CREDENTIALS_PATH', '')));
            $client->addScope($scope);
            $client->fetchAccessTokenWithAssertion();

            return $client->getAccessToken()['access_token'];
        });
    }

    protected static function firestoreBaseUrl(): string
    {
        $projectId = env('FIREBASE_PROJECT_ID');

        return "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents";
    }

    /**
     * Read a Firestore document. Returns decoded fields, or null if it doesn't exist.
     */
    public static function firestoreGet(string $path): ?array
    {
        $token = self::getAccessToken('https://www.googleapis.com/auth/datastore');

        $response = Http::withToken($token)->get(self::firestoreBaseUrl() . '/' . $path);

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            Log::error('Firestore GET failed: ' . $response->body());
            return null;
        }

        return self::decodeFields($response->json('fields') ?? []);
    }

    /**
     * Fully write (create or overwrite) a Firestore document with the given fields.
     */
    public static function firestoreSet(string $path, array $fields): void
    {
        $token = self::getAccessToken('https://www.googleapis.com/auth/datastore');

        $response = Http::withToken($token)
            ->patch(self::firestoreBaseUrl() . '/' . $path, [
                'fields' => self::encodeFields($fields),
            ]);

        if ($response->failed()) {
            Log::error('Firestore SET failed: ' . $response->body());
        }
    }

    /**
     * Create a new document with an auto-generated ID inside the given collection.
     * Returns the new document's ID.
     */
    public static function firestoreCreate(string $collectionPath, array $fields): ?string
    {
        $token = self::getAccessToken('https://www.googleapis.com/auth/datastore');

        $response = Http::withToken($token)
            ->post(self::firestoreBaseUrl() . '/' . $collectionPath, [
                'fields' => self::encodeFields($fields),
            ]);

        if ($response->failed()) {
            Log::error('Firestore CREATE failed: ' . $response->body());
            return null;
        }

        $name = $response->json('name'); // full resource path, id is the last segment
        return $name ? basename($name) : null;
    }

    // ── Firestore typed-value encoding/decoding ─────────────────────────────

    protected static function encodeFields(array $assoc): array
    {
        $out = [];
        foreach ($assoc as $key => $value) {
            $out[$key] = self::encodeValue($value);
        }
        return $out;
    }

    protected static function encodeValue($value): array
    {
        if (is_null($value)) {
            return ['nullValue' => null];
        }
        if (is_bool($value)) {
            return ['booleanValue' => $value];
        }
        if (is_int($value)) {
            return ['integerValue' => (string) $value];
        }
        if (is_float($value)) {
            return ['doubleValue' => $value];
        }
        if (is_array($value)) {
            if (self::isList($value)) {
                return ['arrayValue' => ['values' => array_map(fn ($v) => self::encodeValue($v), $value)]];
            }
            return ['mapValue' => ['fields' => self::encodeFields($value)]];
        }

        // string (includes ISO-8601 timestamps passed in as strings)
        return ['stringValue' => (string) $value];
    }

    protected static function decodeFields(array $fields): array
    {
        $out = [];
        foreach ($fields as $key => $value) {
            $out[$key] = self::decodeValue($value);
        }
        return $out;
    }

    protected static function decodeValue(array $value)
    {
        if (array_key_exists('nullValue', $value))      return null;
        if (array_key_exists('booleanValue', $value))   return $value['booleanValue'];
        if (array_key_exists('integerValue', $value))   return (int) $value['integerValue'];
        if (array_key_exists('doubleValue', $value))    return (float) $value['doubleValue'];
        if (array_key_exists('stringValue', $value))    return $value['stringValue'];
        if (array_key_exists('timestampValue', $value)) return $value['timestampValue'];
        if (array_key_exists('arrayValue', $value)) {
            return array_map(fn ($v) => self::decodeValue($v), $value['arrayValue']['values'] ?? []);
        }
        if (array_key_exists('mapValue', $value)) {
            return self::decodeFields($value['mapValue']['fields'] ?? []);
        }

        return null;
    }

    /** array_is_list() polyfill — this codebase targets PHP ^8.0.2 (pre-8.1). */
    protected static function isList(array $arr): bool
    {
        return $arr === [] || array_keys($arr) === range(0, count($arr) - 1);
    }
}
