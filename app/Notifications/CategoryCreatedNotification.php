<?php

namespace App\Notifications;

use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CategoryCreatedNotification extends Notification
{
    use Queueable;

    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $categoryName = $this->category->name ?? 'تصنيف جديد';

        return [
            'title'       => 'إضافة تصنيف جديد 🏷️',
            'message'     => "تمت إضافة تصنيف جديد للدورات باسم: \"{$categoryName}\".",
            'category_id' => $this->category->id,
            'url'         => route('categories.index'),
        ];
    }
}