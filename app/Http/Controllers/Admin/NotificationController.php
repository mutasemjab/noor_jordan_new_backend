<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function sendForm()
    {
        $classes  = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $students = Student::where('is_active', true)->orderBy('name')->get(['id', 'name', 'national_id']);

        return view('admin.notifications.send', compact('classes', 'students'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'body'       => 'required|string',
            'target'     => 'required|in:all,class,student',
            'class_id'   => 'required_if:target,class|nullable|exists:classes,id',
            'student_id' => 'required_if:target,student|nullable|exists:students,id',
        ]);

        $target = match ($request->target) {
            'class'   => (int) $request->class_id,
            'student' => 'student:' . $request->student_id,
            default   => null,
        };

        $result = FCMController::sendToStudents(
            $request->title,
            $request->body,
            $target
        );

        $message = "تم الإرسال بنجاح. المستقبلون: {$result['total']}، وصل للتطبيق: {$result['sent']}.";

        return back()->with('success', $message);
    }
}
