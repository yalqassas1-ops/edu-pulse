<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'enrollments';

    // فك الحماية عن كافة الحقول لضمان عدم تجاهل أي داتا مرسلة
    protected $guarded = [];

    /**
     * علاقة التسجيل بالطالب
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * علاقة التسجيل بالشعبة الدراسية (CourseClass)
     */
    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class, 'course_class_id')
            ->withDefault(function ($courseClass, $enrollment) {
                // إذا لم يجد course_class_id يبحث برقم course_id أو class_id لضمان الربط
                $classId = $enrollment->course_class_id ?? $enrollment->course_id ?? $enrollment->class_id;
                if ($classId) {
                    return CourseClass::find($classId);
                }
            });
    }

    /**
     * علاقة التسجيل بالدورة التدريبية مباشرة
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}