<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function mySubjects(Request $request)
    {
        $student = $request->user();
        $class   = $student->schoolClass;

        if (! $class) {
            return response()->json([
                'status'  => false,
                'message' => 'الطالب غير مسجل في أي صف.',
                'data'    => null,
            ], 404);
        }

        $classSubjects = $class->classSubjects()
            ->with(['subject', 'teacher'])
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'تم جلب مواد الصف بنجاح.',
            'data'    => [
                'class' => [
                    'id'              => $class->id,
                    'name'            => $class->name,
                    'homeroom_teacher' => $class->homeroomTeacher ? [
                        'id'     => $class->homeroomTeacher->id,
                        'name'   => $class->homeroomTeacher->name,
                        'avatar' => $class->homeroomTeacher->avatar
                            ? asset('assets/uploads/teachers/' . $class->homeroomTeacher->avatar)
                            : null,
                    ] : null,
                ],
                'subjects' => $classSubjects->map(fn ($cs) => [
                    'id'          => $cs->subject->id,
                    'name_ar'     => $cs->subject->name_ar,
                    'name_en'     => $cs->subject->name_en,
                    'icon'        => $cs->subject->icon,
                    'color_class' => $cs->subject->color_class,
                    'teacher'     => $cs->teacher ? [
                        'id'     => $cs->teacher->id,
                        'name'   => $cs->teacher->name,
                        'avatar' => $cs->teacher->avatar
                            ? asset('assets/uploads/teachers/' . $cs->teacher->avatar)
                            : null,
                        'phone'  => $cs->teacher->phone,
                    ] : null,
                ]),
            ],
        ]);
    }
}
