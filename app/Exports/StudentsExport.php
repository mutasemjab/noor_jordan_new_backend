<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        return Student::with('schoolClass')
            ->when($this->filters['search'] ?? null, fn ($q, $s) => $q
                ->where('name', 'like', "%{$s}%")
                ->orWhere('national_id', 'like', "%{$s}%")
            )
            ->when(isset($this->filters['is_active']) && $this->filters['is_active'] !== '',
                fn ($q) => $q->where('is_active', (bool) $this->filters['is_active'])
            )
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return ['#', 'الاسم', 'الرقم الوطني', 'البريد الإلكتروني', 'الهاتف', 'الجنس', 'الصف', 'الحالة', 'تاريخ التسجيل'];
    }

    public function map($student): array
    {
        return [
            $student->id,
            $student->name,
            $student->national_id ?? '',
            $student->email ?? '',
            $student->phone ?? '',
            $student->gender === 'male' ? 'ذكر' : ($student->gender === 'female' ? 'أنثى' : ''),
            $student->schoolClass?->name ?? '',
            $student->is_active ? 'نشط' : 'موقوف',
            $student->created_at?->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e293b']], 'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}
