<?php

namespace App\Observers;

use App\Models\Teacher;
use Illuminate\Support\Facades\Storage;

class TeacherObserver
{
    /**
     * تُنفّذ هذه الدالة تلقائياً قبل تحديث بيانات المحاضر
     */
    public function updating(Teacher $teacher): void
    {
        // إذا تم رفع ملف جديد وتغيرت قيمة المرفق، نحذف الملف القديم من السيرفر
        if ($teacher->isDirty('attachment') && $teacher->getOriginal('attachment')) {
            Storage::disk('public')->delete($teacher->getOriginal('attachment'));
        }
    }

    /**
     * تُنفّذ هذه الدالة تلقائياً بعد حذف المحاضر من قاعدة البيانات
     */
    public function deleted(Teacher $teacher): void
    {
        // إذا كان هناك ملف مرفق، نقتطعه ونحذفه فوراً من مجلد التخزين
        if ($teacher->attachment) {
            Storage::disk('public')->delete($teacher->attachment);
        }
    }

    /**
     * تُنفّذ في حال الحذف النهائي عند استخدام الحذف المؤقت (Soft Deletes)
     */
    public function forceDeleted(Teacher $teacher): void
    {
        if ($teacher->attachment) {
            Storage::disk('public')->delete($teacher->attachment);
        }
    }
}