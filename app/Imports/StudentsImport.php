<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class StudentsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public array $errors   = [];
    public int   $imported = 0;
    public int   $skipped  = 0;

    private array $classMap = [];

    public function __construct()
    {
        // Build class name → id map for fast lookup
        SchoolClass::all()->each(function ($c) {
            $this->classMap[trim($c->name)] = $c->id;
        });
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 because row 1 is headers

            $name       = trim($row['الاسم']          ?? $row['name']        ?? '');
            $nationalId = trim($row['الرقم_الوطني']   ?? $row['national_id'] ?? '');
            $email      = trim($row['البريد_الإلكتروني'] ?? $row['email']    ?? '');
            $phone      = trim($row['الهاتف']          ?? $row['phone']      ?? '');
            $password   = trim($row['كلمة_المرور']     ?? $row['password']   ?? 'Pass@1234');
            $className  = trim($row['الصف']            ?? $row['class']      ?? '');

            if (empty($name)) {
                $this->skipped++;
                continue;
            }

            // Skip duplicate national_id
            if ($nationalId && Student::where('national_id', $nationalId)->exists()) {
                $this->errors[] = "صف {$rowNum}: الرقم الوطني «{$nationalId}» موجود مسبقاً — تم التخطي";
                $this->skipped++;
                continue;
            }

            // Skip duplicate email
            if ($email && Student::where('email', $email)->exists()) {
                $this->errors[] = "صف {$rowNum}: البريد «{$email}» موجود مسبقاً — تم التخطي";
                $this->skipped++;
                continue;
            }

            $classId = $className ? ($this->classMap[$className] ?? null) : null;

            Student::create([
                'name'        => $name,
                'national_id' => $nationalId ?: null,
                'email'       => $email ?: null,
                'phone'       => $phone ?: null,
                'password'    => $password,
                'class_id'    => $classId,
                'is_active'   => true,
            ]);

            $this->imported++;
        }
    }
}
