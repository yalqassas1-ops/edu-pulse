<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public $successCount = 0;

    /**
     * تحويل كل سطر في ملف Excel إلى نموذج Student
     */
    public function model(array $row)
    {
        $this->successCount++;

        return new Student([
            'name'  => $row['alasm'] ?? $row['name'] ?? null,
            'email' => $row['albryd'] ?? $row['email'] ?? null,
            'phone' => $row['alhatf'] ?? $row['phone'] ?? null,
        ]);
    }

    /**
     * شروط التحقق من البيانات لمنع البيانات المفقودة أو الإيميل المكرر
     */
    public function rules(): array
    {
        return [
            '*.name'  => 'required|string|max:255',
            '*.email' => 'required|email|unique:students,email',
        ];
    }

    /**
     * عند وجود أسطر بها أخطاء: يتم تجاوز السطر وتسجيل التفاصيل في الـ Logs
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::warning("خطأ استيراد طالب - السطر رقم ({$failure->row()}): " . implode(', ', $failure->errors()), [
                'row_values' => $failure->values()
            ]);
        }
    }
}