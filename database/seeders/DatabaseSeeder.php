<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\CourseClass;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::create(['name' => 'برمجة وتطوير', 'description' => 'دورات البرمجة']);
        
        $teacher = Teacher::create([
            'name' => 'أحمد محمود',
            'email' => 'teacher@test.com',
            'phone' => '0599000000',
            'specialization' => 'Laravel Developer'
        ]);

        $student = Student::create([
            'name' => 'محمد علي',
            'email' => 'student@test.com',
            'phone' => '0599111111',
            'birth_date' => '2000-01-01'
        ]);

        $room = ClassRoom::create(['name' => 'مختبر 1', 'capacity' => 20]);

        $course = Course::create([
            'category_id' => $category->id,
            'title' => 'دورة Laravel الاحترافية',
            'description' => 'تعلم لارافيل من الصفر',
            'price' => 150.00,
            'total_hours' => 40
        ]);

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