<?php

namespace App\Imports;

use App\Models\Teacher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class TeachersImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public array $errors   = [];
    public int   $imported = 0;
    public int   $skipped  = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            $name     = trim($row['الاسم']               ?? $row['name']     ?? '');
            $email    = trim($row['البريد_الإلكتروني']   ?? $row['email']    ?? '');
            $phone    = trim($row['الهاتف']              ?? $row['phone']    ?? '');
            $specAr   = trim($row['التخصص']             ?? $row['specialization'] ?? '');
            $password = trim($row['كلمة_المرور']         ?? $row['password'] ?? 'Pass@1234');

            if (empty($name)) {
                $this->skipped++;
                continue;
            }

            if ($email && Teacher::where('email', $email)->exists()) {
                $this->errors[] = "صف {$rowNum}: البريد «{$email}» موجود مسبقاً — تم التخطي";
                $this->skipped++;
                continue;
            }

            Teacher::create([
                'name'              => $name,
                'email'             => $email ?: null,
                'phone'             => $phone ?: null,
                'specialization_ar' => $specAr ?: null,
                'password'          => $password,
                'is_active'         => true,
                'is_verified'       => false,
            ]);

            $this->imported++;
        }
    }
}
