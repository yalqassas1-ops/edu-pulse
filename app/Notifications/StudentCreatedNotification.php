<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentCreatedNotification extends Notification
{
    use Queueable;

    protected $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    /**
     * القناة المستخدمة للإشعار (قاعدة البيانات لتظهر في الجرس)
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * البيانات المخزنة داخل جدول notifications
     */
    public function toArray($notifiable)
    {
        return [
            'title'      => 'إضافة طالب جديد',
            'message'    => 'تم تسجيل الطالب ' . $this->student->name . ' بنجاح في المنصة.',
            'student_id' => $this->student->id,
            'url'        => route('students.index'), // أو route('students.show', $this->student->id)
        ];
    }
}