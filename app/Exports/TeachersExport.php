<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeachersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        return Teacher::withCount('courses')
            ->when($this->filters['search'] ?? null, fn ($q, $s) => $q
                ->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
            )
            ->when(isset($this->filters['is_active']) && $this->filters['is_active'] !== '',
                fn ($q) => $q->where('is_active', (bool) $this->filters['is_active'])
            )
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return ['#', 'الاسم', 'البريد الإلكتروني', 'الهاتف', 'التخصص', 'سنوات الخبرة', 'الجنس', 'الدورات', 'موثّق', 'الحالة', 'تاريخ الانضمام'];
    }

    public function map($teacher): array
    {
        return [
            $teacher->id,
            $teacher->name,
            $teacher->email ?? '',
            $teacher->phone ?? '',
            $teacher->specialization_ar ?? $teacher->specialization_en ?? '',
            $teacher->years_of_experience ?? '',
            $teacher->gender === 'male' ? 'ذكر' : ($teacher->gender === 'female' ? 'أنثى' : ''),
            $teacher->courses_count,
            $teacher->is_verified ? 'نعم' : 'لا',
            $teacher->is_active ? 'نشط' : 'موقوف',
            $teacher->created_at?->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e293b']], 'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}
