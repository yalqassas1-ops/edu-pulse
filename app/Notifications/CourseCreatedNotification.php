<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CourseCreatedNotification extends Notification
{
    use Queueable;

    protected $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        // تحميل علاقة التصنيف والمحاضر
        $this->course->load(['category', 'teacher']);
        
        $courseTitle  = $this->course->title ?? $this->course->name ?? 'دورة جديد';
        $categoryName = $this->course->category ? $this->course->category->name : 'عام';
        $price        = number_format($this->course->price, 2);

        return [
            'title'     => 'إضافة دورة تدريبية جديدة 📚',
            'message'   => "تمت إضافة الدورة التدريبية \"{$courseTitle}\" ضمن تصنيف ({$categoryName}) بسعر " . $price . "$.",
            'course_id' => $this->course->id,
            'url'       => route('courses.index'),
        ];
    }
}