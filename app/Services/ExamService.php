<?php

namespace App\Services;

use App\Models\{Exam, Question, QuestionOption};
use Illuminate\Pagination\LengthAwarePaginator;

class ExamService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Exam::with(['subject'])->withCount('questions');

        if (! empty($filters['exam_type'])) {
            $query->where('exam_type', $filters['exam_type']);
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

    public function find(int $id): Exam
    {
        return Exam::with(['subject', 'questions.options'])->findOrFail($id);
    }

    public function create(array $data): Exam
    {
        return Exam::create($data);
    }

    public function update(Exam $exam, array $data): Exam
    {
        $exam->update($data);

        return $exam->fresh();
    }

    public function delete(Exam $exam): void
    {
        $exam->delete();
    }

    public function addQuestion(Exam $exam, array $questionData, array $options = []): Question
    {
        $question = $exam->questions()->create($questionData);

        foreach ($options as $option) {
            $question->options()->create($option);
        }

        $exam->increment('total_questions');

        return $question->load('options');
    }

    public function updateQuestion(Question $question, array $questionData, array $options = []): Question
    {
        $question->update($questionData);

        if (! empty($options)) {
            $question->options()->delete();
            foreach ($options as $option) {
                $question->options()->create($option);
            }
        }

        return $question->load('options');
    }

    public function deleteQuestion(Question $question): void
    {
        $question->exam->decrement('total_questions');
        $question->delete();
    }
}
