<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ChatMedia;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    use ApiResponse;

    // POST /media — upload a chat image or voice note, get back its public URL
    public function store(Request $request): JsonResponse
    {
        $user         = $request->user();
        $uploaderType = $user instanceof Teacher ? 'teacher' : 'student';

        $validator = Validator::make($request->all(), [
            'type'             => ['required', 'in:image,voice'],
            'duration_seconds' => ['required_if:type,voice', 'nullable', 'integer', 'min:1', 'max:600'],
            'file'             => [
                'required',
                'file',
                $request->input('type') === 'voice'
                    ? 'mimes:m4a,mp3,wav,aac,ogg|mimetypes:audio/mp4,audio/x-m4a,audio/aac,audio/mpeg,audio/wav,audio/ogg|max:15360'
                    : 'mimes:jpg,jpeg,png,gif,webp|max:8192',
            ],
        ], [
            'file.mimes'             => 'صيغة الملف غير مدعومة.',
            'file.max'               => 'حجم الملف أكبر من المسموح.',
            'duration_seconds.max'   => 'أقصى مدة للتسجيل الصوتي 10 دقائق.',
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
