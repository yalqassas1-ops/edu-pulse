<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ClassRoomController extends Controller
{
    /**
     * عرض قائمة جميع القاعات النشطة (غير المؤرشفة)
     */
    public function index()
    {
        $classRooms = ClassRoom::latest()->get();
        return view('class_rooms.index', compact('classRooms'));
    }

    /**
     * عرض قائمة القاعات المؤرشفة (Soft Deleted)
     */
    public function archive()
    {
        $classRooms = ClassRoom::onlyTrashed()->latest()->get();
        return view('class_rooms.archive', compact('classRooms'));
    }

    /**
     * عرض نموذج إضافة قاعة جديدة
     */
    public function create()
    {
        return view('class_rooms.create');
    }

    /**
     * حفظ القاعة الجديدة في قاعدة البيانات وإرسال الإشعار
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255|unique:class_rooms,name',
            'capacity' => 'required|integer|min:1',
        ]);

        $classRoom = ClassRoom::create($validatedData);

        // 🔔 إرسال إشعار إضافة قاعة جديدة
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'إضافة قاعة جديدة',
                    'تمت إضافة القاعة الدراسية (' . $classRoom->name . ') بسعة استيعابية: ' . $classRoom->capacity . ' طالب.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on ClassRoom Store: ' . $e->getMessage());
            }
        }

        return redirect()->route('class-rooms.index')
            ->with('success', 'تمت إضافة القاعة بنجاح وإرسال الإشعار');
    }

    /**
     * عرض تفاصيل قاعة محددة
     */
    public function show(ClassRoom $classRoom)
    {
        return view('class_rooms.show', compact('classRoom'));
    }

    /**
     * عرض نموذج تعديل بيانات القاعة
     */
    public function edit(ClassRoom $classRoom)
    {
        return view('class_rooms.edit', compact('classRoom'));
    }

    /**
     * تحديث بيانات القاعة في قاعدة البيانات وإرسال الإشعار
     */
    public function update(Request $request, ClassRoom $classRoom)
    {
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255|unique:class_rooms,name,' . $classRoom->id,
            'capacity' => 'required|integer|min:1',
        ]);

        $classRoom->update($validatedData);

        // 🔔 إرسال إشعار تعديل بيانات القاعة
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'تعديل بيانات قاعة',
                    'تم تحديث بيانات القاعة الدراسية (' . $classRoom->name . ') بنجاح.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on ClassRoom Update: ' . $e->getMessage());
            }
        }

        return redirect()->route('class-rooms.index')
            ->with('success', 'تم تعديل القاعة بنجاح');
    }

    /**
     * أرشفة/حذف القاعة مؤقتاً (Soft Delete) وإرسال الإشعار
     */
    public function destroy(ClassRoom $classRoom)
    {
        $roomName = $classRoom->name;
        $classRoom->delete(); // سيقوم بالحذف الخفيف بدلاً من الحذف النهائي

        // 🔔 إرسال إشعار أرشفة القاعة
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'أرشفة قاعة دراسية',
                    'تم نقل القاعة الدراسية (' . $roomName . ') إلى الأرشيف.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on ClassRoom Delete: ' . $e->getMessage());
            }
        }

        return redirect()->route('class-rooms.index')
            ->with('success', 'تم نقل القاعة إلى الأرشيف بنجاح');
    }

    /**
     * استعادة القاعة المؤرشفة من الأرشيف
     */
    public function restore($id)
    {
        $classRoom = ClassRoom::onlyTrashed()->findOrFail($id);
        $classRoom->restore();

        return redirect()->route('class-rooms.archive')
            ->with('success', 'تمت استعادة القاعة من الأرشيف بنجاح');
    }

    /**
     * الحذف النهائي للقاعة من قاعدة البيانات بشكل كلي
     */
    public function forceDelete($id)
    {
        $classRoom = ClassRoom::onlyTrashed()->findOrFail($id);
        $classRoom->forceDelete();

        return redirect()->route('class-rooms.archive')
            ->with('success', 'تم حذف القاعة نهائياً من النظام');
    }

    /**
     * تفريغ الأرشيف بالكامل (حذف نهائي لجميع القاعات المؤرشفة)
     */
    public function forceDeleteAllArchive()
    {
        $trashedClassRooms = ClassRoom::onlyTrashed()->get();
        $count = $trashedClassRooms->count();

        if ($count === 0) {
            return redirect()->route('class-rooms.archive')
                ->with('error', 'الأرشيف فارغ بالفعل، لا توجد قاعات للحذف');
        }

        // إنجاز عملية الحذف النهائي لجميع السجلات المؤرشفة
        ClassRoom::onlyTrashed()->forceDelete();

        // 🔔 إرسال إشعار تفريغ الأرشيف
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'تفريغ أرشيف القاعات',
                    'تم تفريغ أرشيف القاعات الدراسية بالكامل وحذف عدد (' . $count . ') قاعة نهائياً.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on ClassRoom Force Delete All: ' . $e->getMessage());
            }
        }

        return redirect()->route('class-rooms.archive')
            ->with('success', 'تم تفريغ أرشيف القاعات الدراسية بالكامل بحذف ' . $count . ' قاعة نهائياً');
    }
}