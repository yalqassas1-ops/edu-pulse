<?php

namespace App\Notifications;

use App\Models\CourseClass;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CourseClassCreatedNotification extends Notification
{
    use Queueable;

    protected $courseClass;

    public function __construct(CourseClass $courseClass)
    {
        $this->courseClass = $courseClass;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        // تحميل العلاقات لضمان قراءة اسم الدورة والقاعة والمحاضر
        $this->courseClass->load(['course', 'teacher', 'classRoom']);
        
        $courseTitle = $this->courseClass->course 
                        ? ($this->courseClass->course->title ?? $this->courseClass->course->name) 
                        : 'دورة';
        
        $classNumber = $this->courseClass->class_number ?? $this->courseClass->id;

        return [
            'title'           => 'إضافة شعبة دراسية جديدة 🏫',
            'message'         => "تمت إضافة شعبة جديدة (رقم: {$classNumber}) لدورة \"{$courseTitle}\".",
            'course_class_id' => $this->courseClass->id,
            'url'             => route('course-classes.index'),
        ];
    }
}