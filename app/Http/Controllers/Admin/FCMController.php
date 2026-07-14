<?php

namespace App\Http\Controllers\Admin;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentNotification;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;

class FCMController
{
    /**
     * Send FCM push notification to a single device token.
     */
    public static function sendToToken(string $title, string $body, string $fcmToken, string $screen = 'home'): bool
    {
        $credentialsPath = base_path(env('FIREBASE_CREDENTIALS_PATH', ''));
        if (! $credentialsPath || ! file_exists($credentialsPath)) {
            Log::warning('FCM: credentials file not found');
            return false;
        }

        try {
            $client = new GoogleClient();
            $client->setAuthConfig($credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->fetchAccessTokenWithAssertion();
            $tokenResponse  = $client->getAccessToken();
            $accessToken    = $tokenResponse['access_token'];

            $payload = json_encode([
                'message' => [
                    'token'        => $fcmToken,
                    'notification' => ['title' => $title, 'body' => $body],
                    'data'         => ['screen' => $screen, 'click_action' => 'FLUTTER_NOTIFICATION_CLICK'],
                    'android'      => ['priority' => 'high'],
                    'apns'         => [
                        'headers' => ['apns-priority' => '10'],
                        'payload' => ['aps' => ['sound' => 'default', 'badge' => 1]],
                    ],
                ],
            ]);

            $projectId = env('FIREBASE_PROJECT_ID');
            $ch = curl_init("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ["Authorization: Bearer {$accessToken}", 'Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS     => $payload,
            ]);

            $result = curl_exec($ch);
            $err    = curl_error($ch);
            curl_close($ch);

            if ($err) {
                Log::error("FCM cURL error: {$err}");
                return false;
            }

            $response = json_decode($result, true);

            if (isset($response['name'])) {
                return true;
            }

            // Clear invalid token
            if (($response['error']['details'][0]['errorCode'] ?? '') === 'UNREGISTERED') {
                Student::where('fcm_token', $fcmToken)->update(['fcm_token' => null]);
            }

            Log::error('FCM send failed: ' . json_encode($response));
            return false;

        } catch (\Throwable $e) {
            Log::error('FCM exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to multiple students and store in DB.
     *
     * @param  array|int|null  $target  null=all, int=class_id, 'student:N'=specific student
     */
    public static function sendToStudents(string $title, string $body, $target = null, string $screen = 'home'): array
    {
        $query = Student::where('is_active', true);

        if (is_int($target)) {
            $query->where('class_id', $target);
        } elseif (is_string($target) && str_starts_with($target, 'student:')) {
            $studentId = (int) str_replace('student:', '', $target);
            $query->where('id', $studentId);
        }
        // null → all students

        $students = $query->get();

        $sent = 0;
        $failed = 0;

        foreach ($students as $student) {
            // Store in DB
            StudentNotification::create([
                'student_id' => $student->id,
                'title'      => $title,
                'body'       => $body,
                'type'       => 'general',
            ]);

            // Push via FCM if token exists
            if ($student->fcm_token) {
                self::sendToToken($title, $body, $student->fcm_token, $screen) ? $sent++ : $failed++;
            }
        }

        return [
            'total'  => $students->count(),
            'sent'   => $sent,
            'failed' => $failed,
        ];
    }
}
