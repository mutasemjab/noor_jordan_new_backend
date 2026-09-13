<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentLoginAttempt;
use Illuminate\Http\Request;

class StudentLoginAttemptController extends Controller
{
    // Only identifiers with MORE than 3 failed attempts are worth an admin's
    // attention - fewer than that is normal typo noise.
    private const THRESHOLD = 3;

    public function index()
    {
        $rows = StudentLoginAttempt::selectRaw('national_id, COUNT(*) as attempts, MAX(created_at) as last_attempt_at')
            ->groupBy('national_id')
            ->having('attempts', '>', self::THRESHOLD)
            ->orderByDesc('last_attempt_at')
            ->get();

        $students = Student::whereIn('national_id', $rows->pluck('national_id'))
            ->get()
            ->keyBy('national_id');

        $rows = $rows->map(function ($row) use ($students) {
            $row->student = $students->get($row->national_id);
            return $row;
        });

        return view('admin.student_login_attempts.index', compact('rows'));
    }

    public function clear(string $nationalId)
    {
        StudentLoginAttempt::where('national_id', $nationalId)->delete();

        return redirect()->route('admin.student-login-attempts.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}
