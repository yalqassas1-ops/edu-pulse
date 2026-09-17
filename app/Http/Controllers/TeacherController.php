<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Http\Requests\TeacherStoreRequest;
use App\Notifications\SystemNotification;
use App\Exports\TeachersExport;
use App\Imports\TeachersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $teachers = Teacher::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('specialization', 'like', "%{$search}%");
        })->latest()->get();

        return view('teachers.index', compact('teachers'));
    }

    // 🟢 1. دالة تصدير المحاضرين إلى Excel
    public function export(Request $request)
    {
        return Excel::download(new TeachersExport($request->search), 'teachers_list.xlsx');
    }

    // 🟢 2. دالة استيراد المحاضرين من Excel مع الإشعارات وتسجيل الأخطاء
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new TeachersImport, $request->file('file'));

            // 🔔 إشعار الاستيراد
            $users = User::all();
            if ($users->count() > 0) {
                try {
                    Notification::send($users, new SystemNotification(
                        'استيراد محاضرين',
                        'تم استيراد قائمة المحاضرين الجدد من ملف Excel بنجاح.'
                    ));
                } catch (\Exception $e) {
                    Log::error('Broadcast Connection Failed on Teacher Import: ' . $e->getMessage());
                }
            }

            return back()->with('success', 'تم استيراد قائمة المحاضرين بنجاح!');
        } catch (\Exception $e) {
            Log::error('Teacher Import Error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء الاستيراد، يرجى التأكد من تنسيق البيانات داخل الملف.');
        }
    }

    public function create()
    {
        return view('teachers.create');
    }

    public function store(TeacherStoreRequest $request)
    {
        $data = $request->validated();

        // 🛡️ التخزين الآمن للملف برقم فريد (UUID)
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/teachers', $filename, 'public');
            $data['attachment'] = $path;
        }

        $teacher = Teacher::create($data);

        // 🔔 إشعار الإضافة وحماية النظام وتسجيل الأخطاء في Log
        $users = User::all();
        if ($users->count() > 0) {
            try {
                Notification::send($users, new SystemNotification(
                    'إضافة محاضر',
                    'تمت إضافة المحاضر/ة (' . $teacher->name . ') بالتخصص: ' . $teacher->specialization
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Teacher Create: ' . $e->getMessage());
            }
        }

        return redirect()->route('teachers.index')->with('success', 'تمت إضافة المحاضر بنجاح!');
    }

    public function edit(Teacher $teacher)
    {
        return view('teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:teachers,email,' . $teacher->id,
            'phone'          => 'nullable|string|max:20',
            'specialization' => 'required|string|max:255',
            'attachment'     => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
        ]);

        $data = $request->all();

        // 🛡️ حذف الملف القديم واستبداله بملف جديد آمن
        if ($request->hasFile('attachment')) {
            if ($teacher->attachment && Storage::disk('public')->exists($teacher->attachment)) {
                Storage::disk('public')->delete($teacher->attachment);
            }

            $file = $request->file('attachment');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/teachers', $filename, 'public');
            $data['attachment'] = $path;
        }

        $teacher->update($data);

        // 🔔 إشعار التعديل وحماية النظام وتسجيل الأخطاء في Log
        $users = User::all();
        if ($users->count() > 0) {
            try {
                Notification::send($users, new SystemNotification(
                    'تعديل بيانات محاضر',
                    'تم تحديث بيانات المحاضر/ة (' . $teacher->name . ') بنجاح.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Teacher Update: ' . $e->getMessage());
            }
        }

        return redirect()->route('teachers.index')->with('success', 'تم تعديل بيانات المحاضر بنجاح!');
    }

    // 1. الحذف المؤقت (Soft Delete)
    public function destroy(Teacher $teacher)
    {
        $teacherName = $teacher->name;
        $teacher->delete();

        // 🔔 إشعار الحذف وحماية النظام وتسجيل الأخطاء في Log
        $users = User::all();
        if ($users->count() > 0) {
            try {
                Notification::send($users, new SystemNotification(
                    'نقل محاضر للأرشيف',
                    'تم نقل المحاضر/ة (' . $teacherName . ') إلى الأرشيف.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Teacher Delete: ' . $e->getMessage());
            }
        }

        return redirect()->route('teachers.index')->with('success', 'تم نقل المحاضر إلى الأرشيف بنجاح!');
    }

    // 2. عرض واجهة الأرشيف (العناصر المحذوفة مؤقتاً فقط)
    public function archive()
    {
        $trashedTeachers = Teacher::onlyTrashed()->latest()->get();
        return view('teachers.archive', compact('trashedTeachers'));
    }

    // 3. استعادة المحاضر من الأرشيف (Restore)
    public function restore($id)
    {
        $teacher = Teacher::onlyTrashed()->findOrFail($id);
        $teacher->restore();

        // 🔔 إشعار الاستعادة
        $users = User::all();
        if ($users->count() > 0) {
            try {
                Notification::send($users, new SystemNotification(
                    'استعادة محاضر',
                    'تمت استعادة المحاضر/ة (' . $teacher->name . ') من الأرشيف.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Teacher Restore: ' . $e->getMessage());
            }
        }

        return redirect()->route('teachers.archive')->with('success', 'تمت استعادة المحاضر بنجاح!');
    }

    // 4. الحذف النهائي لعنصر واحد من قاعدة البيانات (Force Delete)
    public function forceDelete($id)
    {
        $teacher = Teacher::onlyTrashed()->findOrFail($id);
        $teacherName = $teacher->name;

        // تنظيف وحذف الملف من القرص عند الحذف النهائي
        if ($teacher->attachment && Storage::disk('public')->exists($teacher->attachment)) {
            Storage::disk('public')->delete($teacher->attachment);
        }

        $teacher->forceDelete();

        // 🔔 إشعار الحذف النهائي
        $users = User::all();
        if ($users->count() > 0) {
            try {
                Notification::send($users, new SystemNotification(
                    'حذف نهائي لمحاضر',
                    'تم حذف المحاضر/ة (' . $teacherName . ') نهائياً من النظام.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Teacher Force Delete: ' . $e->getMessage());
            }
        }

        return redirect()->route('teachers.archive')->with('success', 'تم حذف المحاضر نهائياً من قاعدة البيانات!');
    }

    // 5. حذف جميع المحاضرين المؤرشفين نهائياً (تفريغ الأرشيف)
    public function forceDeleteAllArchive()
    {
        $trashedTeachers = Teacher::onlyTrashed()->get();

        foreach ($trashedTeachers as $teacher) {
            if ($teacher->attachment && Storage::disk('public')->exists($teacher->attachment)) {
                Storage::disk('public')->delete($teacher->attachment);
            }
            $teacher->forceDelete();
        }

        // 🔔 إشعار تفريغ الأرشيف
        $users = User::all();
        if ($users->count() > 0) {
            try {
                Notification::send($users, new SystemNotification(
                    'تفريغ أرشيف المحاضرين',
                    'تم حذف جميع المحاضرين الموجودين في الأرشيف نهائياً من النظام.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Teacher Force Delete All: ' . $e->getMessage());
            }
        }

        return redirect()->route('teachers.archive')->with('success', 'تم حذف جميع المحاضرين من الأرشيف نهائياً!');
    }
}