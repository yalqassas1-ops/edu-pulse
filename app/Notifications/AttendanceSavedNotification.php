<?php

namespace App\Notifications;

use App\Models\CourseClass;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceSavedNotification extends Notification
{
    use Queueable;

    protected $courseClass;
    protected $date;

    public function __construct(CourseClass $courseClass, $date)
    {
        $this->courseClass = $courseClass;
        $this->date = $date;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $this->courseClass->load('course');
        
        $courseTitle = $this->courseClass->course 
                        ? ($this->courseClass->course->title ?? $this->courseClass->course->name) 
                        : 'دورة';
        $classNumber = $this->courseClass->class_number ?? $this->courseClass->id;

        return [
            'title'           => 'تسجيل الحضور والغياب 📝',
            'message'         => "تم حفظ سجل الحضور والغياب لشعبة (رقم: {$classNumber}) - \"{$courseTitle}\" بتاريخ {$this->date}.",
            'course_class_id' => $this->courseClass->id,
            'url'             => route('attendances.index', [
                'course_class_id' => $this->courseClass->id,
                'date'            => $this->date,
            ]),
        ];
    }
}