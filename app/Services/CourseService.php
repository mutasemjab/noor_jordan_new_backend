<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseService
{
    private string $uploadFolder = 'assets/uploads/courses';

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Course::with(['teacher', 'category', 'subject'])
            ->withCount('enrollments');

        if (! empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }
        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (! empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }
        if (isset($filters['is_published']) && $filters['is_published'] !== '') {
            $query->where('is_published', $filters['is_published']);
        }
        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(fn ($q) => $q
                ->where('title_ar', 'like', "%{$s}%")
                ->orWhere('title_en', 'like', "%{$s}%")
            );
        }

        return $query->latest()->paginate($perPage);
    }

    public function find(int $id): Course
    {
        return Course::with([
            'teacher', 'category', 'subject',
            'units.lessons', 'units.materials', 'units.exam',
        ])->withCount('enrollments')->findOrFail($id);
    }

    public function create(array $data, $thumbnail = null): Course
    {
        if ($thumbnail) {
            $data['thumbnail'] = uploadImage($this->uploadFolder, $thumbnail);
        }

        return Course::create($data);
    }

    public function update(Course $course, array $data, $thumbnail = null): Course
    {
        if ($thumbnail) {
            $this->deleteThumbnail($course->getRawOriginal('thumbnail'));
            $data['thumbnail'] = uploadImage($this->uploadFolder, $thumbnail);
        }

        $course->update($data);

        return $course->fresh();
    }

    public function delete(Course $course): void
    {
        $this->deleteThumbnail($course->getRawOriginal('thumbnail'));
        $course->delete();
    }

    private function deleteThumbnail(?string $filename): void
    {
        if ($filename) {
            $path = base_path("{$this->uploadFolder}/{$filename}");
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}
