<?php

namespace App\Services;

use App\Http\Controllers\Admin\FCMController;
use App\Models\Student;
use App\Models\StudentNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Single entry point for "something new was added for students" notifications.
 *
 * Every call stores a row in student_notifications (so it shows in the app's
 * notifications list) and queues an FCM push. The stored row and the push
 * payload have exactly the same shape the announcement notifications always
 * had (type "general", no data, push data = screen only), so the mobile app
 * needs no changes. Pushes are sent once the response has been handed back,
 * so saving content for a whole class doesn't wait on Firebase.
 */
class StudentNotifier
{
    /** @var array<int, array{title:string, body:string, token:string, screen:string}> */
    protected static array $pendingPushes = [];

    protected static bool $flushRegistered = false;

    /**
     * Notify every active student in a class. A null class means the item is
     * general (not tied to one class), so every active student is notified.
     */
    public static function toClass(?int $classId, string $title, string $body, string $screen = 'home'): void
    {
        $students = Student::where('is_active', true)
            ->when($classId, fn ($q) => $q->where('class_id', $classId))
            ->get(['id', 'fcm_token']);

        self::send($students, $title, $body, $screen);
    }

    /** Notify several classes at once; an empty list means every active student. */
    public static function toClasses(array $classIds, string $title, string $body, string $screen = 'home'): void
    {
        $students = Student::where('is_active', true)
            ->when(! empty($classIds), fn ($q) => $q->whereIn('class_id', $classIds))
            ->get(['id', 'fcm_token']);

        self::send($students, $title, $body, $screen);
    }

    /** Notify one student (grades, attendance, ...). */
    public static function toStudent(int $studentId, string $title, string $body, string $screen = 'home'): void
    {
        $students = Student::where('is_active', true)
            ->where('id', $studentId)
            ->get(['id', 'fcm_token']);

        self::send($students, $title, $body, $screen);
    }

    protected static function send(Collection $students, string $title, string $body, string $screen): void
    {
        if ($students->isEmpty()) {
            return;
        }

        $now = now();

        try {
            foreach ($students->chunk(500) as $chunk) {
                StudentNotification::insert($chunk->map(fn ($s) => [
                    'student_id' => $s->id,
                    'title'      => $title,
                    'body'       => $body,
                    'type'       => 'general',
                    'is_read'    => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->values()->all());
            }
        } catch (\Throwable $e) {
            // Never let a notification problem break saving the actual content.
            Log::error('StudentNotifier: failed to store notifications: ' . $e->getMessage());
        }

        foreach ($students as $student) {
            if ($student->fcm_token) {
                self::$pendingPushes[] = [
                    'title'  => $title,
                    'body'   => $body,
                    'token'  => $student->fcm_token,
                    'screen' => $screen,
                ];
            }
        }

        self::registerFlush();
    }

    protected static function registerFlush(): void
    {
        if (self::$flushRegistered) {
            return;
        }
        self::$flushRegistered = true;

        app()->terminating(function () {
            self::flush();
        });
    }

    /** Send every queued push now. */
    public static function flush(): void
    {
        $pushes = self::$pendingPushes;
        self::$pendingPushes   = [];
        self::$flushRegistered = false;

        if (empty($pushes)) {
            return;
        }

        if (function_exists('fastcgi_finish_request')) {
            @fastcgi_finish_request();
        }
        @set_time_limit(0);
        ignore_user_abort(true);

        foreach ($pushes as $push) {
            try {
                FCMController::sendToToken($push['title'], $push['body'], $push['token'], $push['screen']);
            } catch (\Throwable $e) {
                Log::error('StudentNotifier: push failed: ' . $e->getMessage());
            }
        }
    }
}
