<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EnrollmentCreatedNotification extends Notification
{
    use Queueable;

    protected $enrollment;

    public function __construct(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        // تحميل علاقة الطالب والشعبة والدورة
        $this->enrollment->load(['student', 'courseClass.course']);
        
        $studentName = $this->enrollment->student ? $this->enrollment->student->name : 'طالب';
        
        // جلب اسم الدورة
        $courseName = $this->enrollment->courseClass && $this->enrollment->courseClass->course 
                        ? ($this->enrollment->courseClass->course->title ?? $this->enrollment->courseClass->course->name)
                        : 'دورة';

        // جلب رقم الشعبة من حقل class_number
        $classObj = $this->enrollment->courseClass;
        $classNumber = $classObj ? ($classObj->class_number ?? $classObj->id) : '';

        // دمج اسم الدورة مع رقم الشعبة
        $fullCourseInfo = $classNumber ? "{$courseName} (الشعبة: {$classNumber})" : $courseName;

        return [
            'title'         => 'تسجيل طالب في دورة 🎓',
            'message'       => "تم تسجيل الطالب " . $studentName . " في: " . $fullCourseInfo . ".",
            'enrollment_id' => $this->enrollment->id,
            'url'           => route('enrollments.index'),
        ];
    }
}