<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'teacher_id',
        'class_room_id',
        'class_number',
        'start_date',
        'end_date',
        'days',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'days' => 'array',
    ];

    // --- العلاقات ---

    // 1. الدورة التدريبية
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // 2. المحاضر
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // 3. القاعة الدراسية
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    // 4. تسجيلات الطلاب
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}