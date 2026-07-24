<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ChatMedia;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    use ApiResponse;

    private const IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // Recorded voice notes (m4a/AAC-LC) get sniffed inconsistently across
    // PHP/Symfony versions, and the Flutter FormData part often has no
    // reliable filename extension for Laravel's extension-based `mimes` rule
    // to key off — so we check the mime directly instead of relying on it.
    private const VOICE_MIMES = [
        'audio/mp4', 'audio/x-m4a', 'audio/m4a', 'audio/aac',
        'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-wav', 'audio/ogg',
    ];

    // POST /media — upload a chat image or voice note, get back its public URL
    public function store(Request $request): JsonResponse
    {
        $user         = $request->user();
        $uploaderType = $user instanceof Teacher ? 'teacher' : 'student';
        $type         = $request->input('type');

        $validator = Validator::make($request->all(), [
            'type'             => ['required', 'in:image,voice'],
            'duration_seconds' => ['required_if:type,voice', 'nullable', 'integer', 'min:1', 'max:600'],
            'file'             => [
                'required',
                'file',
                'max:' . ($type === 'voice' ? 15360 : 8192),
                function ($attribute, $value, $fail) use ($type) {
                    $allowed      = $type === 'voice' ? self::VOICE_MIMES : self::IMAGE_MIMES;
                    $sniffed      = $value->getMimeType();
                    $clientClaim  = $value->getClientMimeType();
                    $mime         = $sniffed ?: $clientClaim;

                    Log::info('chat/media mime check', [
                        'type'          => $type,
                        'sniffed_mime'  => $sniffed,
                        'client_mime'   => $clientClaim,
                        'orig_filename' => $value->getClientOriginalName(),
                        'extension'     => $value->getClientOriginalExtension(),
                    ]);

                    if (! in_array($mime, $allowed, true)) {
                        // TEMP diagnostic: surface the detected mime so we can
                        // fix the allow-list precisely instead of guessing again.
                        $fail("صيغة الملف غير مدعومة (detected: sniffed={$sniffed}, client={$clientClaim}).");
                    }
                },
            ],
        ], [
            'file.max'                     => 'حجم الملف أكبر من المسموح.',
            'duration_seconds.max'         => 'أقصى مدة للتسجيل الصوتي 10 دقائق.',
            'duration_seconds.required_if' => 'يرجى إرسال مدة التسجيل الصوتي.',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422, $validator->errors());
        }

        $data = $validator->validated();
        $file = $request->file('file');

        // Must read the size before uploadImage() moves the file — the temp
        // path backing this UploadedFile instance is gone once it's moved.
        $sizeBytes = $file->getSize();
        $filename  = uploadImage('assets/uploads/chat', $file);

        $media = ChatMedia::create([
            'uploader_type'    => $uploaderType,
            'uploader_id'      => $user->id,
            'path'             => $filename,
            'type'             => $data['type'],
            'duration_seconds' => $data['duration_seconds'] ?? null,
            'size_bytes'       => $sizeBytes,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'OK',
            'data'    => [
                'id'               => $media->id,
                'url'              => $media->url,
                'type'             => $media->type,
                'duration_seconds' => $media->duration_seconds,
                'size_bytes'       => $media->size_bytes,
            ],
        ], 201);
    }
}
