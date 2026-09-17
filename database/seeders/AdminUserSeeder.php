<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate تضمن عدم تكرار الحساب إذا كان موجوداً مسبقاً
        User::updateOrCreate(
            ['email' => 'yousef@you.com'], // البريد الإلكتروني الخاص بك
            [
                'name' => 'yousef_alqassas',          // الاسم
                'password' => Hash::make('123456789'), // كلمة المرور الخاصة بك
            ]
        );
    }
}