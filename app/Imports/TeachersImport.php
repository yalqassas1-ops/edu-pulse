<?php

namespace App\Imports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TeachersImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Teacher([
            'name'           => $row['name'] ?? $row['الاسم'] ?? $row['الاسم_الكامل'],
            'email'          => $row['email'] ?? $row['البريد_الإلكتروني'] ?? $row['البريد'],
            'phone'          => $row['phone'] ?? $row['رقم_الهاتف'] ?? $row['الهاتف'] ?? null,
            'specialization' => $row['specialization'] ?? $row['التخصص'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.email' => 'nullable|email|unique:teachers,email',
        ];
    }
}