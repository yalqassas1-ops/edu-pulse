<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\CourseClass;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء حساب مدير النظام (Admin)
        User::updateOrCreate(
            ['email' => 'yousef@you.com'],
            [
                'name' => 'yousef_alqassas',
                'password' => Hash::make('123456789'),
            ]
        );

        // 2. إنشاء القسم
        $category = Category::firstOrCreate(
            ['name' => 'برمجة وتطوير'],
            ['description' => 'دورات البرمجة']
        );

        // 3. إنشاء المعلم
        $teacher = Teacher::firstOrCreate(
            ['email' => 'teacher@test.com'],
            [
                'name' => 'أحمد محمود',
                'phone' => '0599000000',
                'specialization' => 'Laravel Developer'
            ]
        );

        // 4. إنشاء الطالب
        $student = Student::firstOrCreate(
            ['email' => 'student@test.com'],
            [
                'name' => 'محمد علي',
                'phone' => '0599111111',
                'birth_date' => '2000-01-01'
            ]
        );

        // 5. إنشاء القاعة الدراسية
        $room = ClassRoom::firstOrCreate(
            ['name' => 'مختبر 1'],
            ['capacity' => 20]
        );

        // 6. إنشاء الدورة
        $course = Course::firstOrCreate(
            ['title' => 'دورة Laravel الاحترافية'],
            [
                'category_id' => $category->id,
                'description' => 'تعلم لارافيل من الصفر',
                'price' => 150.00,
                'total_hours' => 40
            ]
        );

        // 7. إنشاء الشعبة الدراسية بأمان
        if (!CourseClass::where('class_number', 'CS-101')->exists()) {
            CourseClass::create([
                'course_id' => $course->id,
                'teacher_id' => $teacher->id,
                'class_room_id' => $room->id,
                'class_number' => 'CS-101',
                'start_date' => '2026-09-01',
                'end_date' => '2026-10-01',
                'days' => ['السبت', 'الثلاثاء'],
                'start_time' => '16:00:00',
                'end_time' => '18:00:00',
                'status' => 'upcoming'
            ]);
        }
    }
}