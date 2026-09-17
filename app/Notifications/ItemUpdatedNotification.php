<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ItemUpdatedNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;

    // نستقبل عنوان ورسالة الإشعار عند إرساله
    public function __construct($title, $message)
    {
        $this->title   = $title;
        $this->message = $message;
    }

    // نحدد أن الحفظ سيكون داخل قاعدة البيانات لظهر في الـ Header
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // شكل البيانات التي ستتخزن في الجدول
    public function toArray(object $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => url('/dashboard'),
        ];
    }
}