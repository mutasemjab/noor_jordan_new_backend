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
            $national_id    = trim($row['الرقم الوطني']   ?? $row['national_id']    ?? '');
            $phone    = trim($row['الهاتف']              ?? $row['phone']    ?? '');
            $password = trim($row['كلمة_المرور']         ?? $row['password'] ?? 'Pass@1234');

            if (empty($name)) {
                $this->skipped++;
                continue;
            }

            if ($national_id && Teacher::where('national_id', $national_id)->exists()) {
                $this->errors[] = "صف {$rowNum}: الرقم «{$national_id}» موجود مسبقاً — تم التخطي";
                $this->skipped++;
                continue;
            }

            Teacher::create([
                'name'      => $name,
                'national_id'     => $national_id ?: null,
                'phone'     => $phone ?: null,
                'password'  => $password,
                'is_active' => true,
            ]);

            $this->imported++;
        }
    }
}
