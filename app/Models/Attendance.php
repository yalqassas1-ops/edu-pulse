<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $guarded = [];

    // علاقة السجل بالطالب
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // علاقة السجل بالشعبة الدراسية
    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class, 'course_class_id');
    }
}