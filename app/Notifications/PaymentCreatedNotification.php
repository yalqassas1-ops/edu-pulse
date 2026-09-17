<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentCreatedNotification extends Notification
{
    use Queueable;

    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * القناة المستخدمة للإشعار (قاعدة البيانات)
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
        // تحميل علاقة الطالب لضمان قراءة الاسم
        $this->payment->load('student');
        
        $studentName = $this->payment->student ? $this->payment->student->name : 'طالب';
        $amount = number_format($this->payment->amount, 2);

        return [
            'title'      => 'تسجيل دفعة جديدة 💵',
            'message'    => "تم تسجيل دفعة بقيمة " . $amount . "$ للطالب: " . $studentName . ".",
            'payment_id' => $this->payment->id,
            'url'        => route('payments.index'),
        ];
    }
}