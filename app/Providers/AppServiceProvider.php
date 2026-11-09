<?php

namespace App\Providers;

use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::USeBootstrap();

        // Notify students whenever content is added for them (admin or teacher).
        foreach ([
            \App\Models\ExamSchedule::class,
            \App\Models\Worksheet::class,
            \App\Models\Exam::class,
            \App\Models\EducationalNote::class,
            \App\Models\ExternalLink::class,
            \App\Models\ClassSubjectVideo::class,
            \App\Models\PreviousYearExam::class,
            \App\Models\QuestionBank::class,
        ] as $model) {
            $model::observe(\App\Observers\StudentContentObserver::class);
        }

        \App\Models\StudentGrade::observe(\App\Observers\StudentRecordObserver::class);
        \App\Models\Attendance::observe(\App\Observers\StudentRecordObserver::class);
    }
}
