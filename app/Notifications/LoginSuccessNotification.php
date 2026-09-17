<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginSuccessNotification extends Notification
{
    use Queueable;

    protected $loginTime;
    protected $ipAddress;

    public function __construct($loginTime, $ipAddress)
    {
        $this->loginTime = $loginTime;
        $this->ipAddress = $ipAddress;
    }

    /**
     * تحديد القنوات المستخدمة للإشعار (تمت إضافة database)
     */
    public function via(object $notifiable): array
    {
        return ['database']; // أضف 'mail' للمصفوفة إذا أردت إرسال إيميل أيضاً
    }

    /**
     * نص البريد الإلكتروني (في حال تفعيل قناة mail)
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('إشعار أمان: تم تسجيل الدخول إلى حسابك')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم تسجيل الدخول بنجاح إلى لوحة التحكم الخاصة بك في منصة EduPulse.')
            ->line('تاريخ ووقت الدخول: ' . $this->loginTime)
            ->line('عنوان الـ IP: ' . $this->ipAddress)
            ->line('إذا لم تقم بهذه العملية بنفسك، يرجى تغيير كلمة المرور فوراً.')
            ->action('الذهاب للوحة التحكم', url('/dashboard'));
    }

    /**
     * البيانات التي تُحفظ في قاعدة البيانات وتظهر في الواجهة (الزر/الهيدر)
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'      => 'تسجيل دخول جديد',
            'message'    => 'تم تسجيل دخول Admin بنجاح من IP: ' . $this->ipAddress,
            'login_time' => $this->loginTime,
            'ip'         => $this->ipAddress,
            'url'        => url('/dashboard'),
        ];
    }
}