<?php

namespace App\Notifications;

use App\Models\ClassRoom;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClassRoomCreatedNotification extends Notification
{
    use Queueable;

    protected $classRoom;

    public function __construct(ClassRoom $classRoom)
    {
        $this->classRoom = $classRoom;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $roomName = $this->classRoom->name ?? 'قاعة جديدة';
        $capacity = $this->classRoom->capacity ?? 0;

        return [
            'title'         => 'إضافة قاعة دراسية جديدة 🏛️',
            'message'       => "تمت إضافة القاعة الدراسية \"{$roomName}\" بالسعة الاستيعابية: {$capacity} طالب.",
            'class_room_id' => $this->classRoom->id,
            'url'           => route('class-rooms.index'),
        ];
    }
}