<?php

namespace App\Http\Controllers;

use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use App\Models\Student;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    /**
     * عرض قائمة الطلاب النشطين
     */
    public function index()
    {
        $students = Student::latest()->get();
        return view('students.index', compact('students'));
    }

    /**
     * عرض أرشيف الطلاب المحذوفين
     */
    public function archive()
    {
        $trashedStudents = Student::onlyTrashed()->latest()->get();
        return view('students.archive', compact('trashedStudents'));
    }

    /**
     * عرض صفحة إنشاء طالب جديد
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * حفظ البيانات عند إضافة طالب جديد وإرسال الإشعارات مع رفع المرفق
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:students,email',
            'phone'      => 'nullable|string|max:20',
            'gender'     => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        // معالجة المرفق/الصورة باستخدام UUID
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $validated['avatar'] = $file->storeAs('uploads/students', $filename, 'public');
        }

        $student = Student::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? null,
            'gender'     => $validated['gender'] ?? 'male',
            'birth_date' => $validated['birth_date'] ?? null,
            'avatar'     => $validated['avatar'] ?? null,
        ]);

        // 🔔 إرسال إشعار إضافة طالب جديد
        $users = User::all();
        if ($users->count() > 0) {
            try {
                Notification::send($users, new SystemNotification(
                    'إضافة طالب جديد',
                    'تمت إضافة الطالب/ة (' . $student->name . ') بنجاح إلى النظام.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Student Create: ' . $e->getMessage());
            }
        }

        return redirect()->route('students.index')->with('success', 'تم إضافة الطالب بنجاح وإرسال الإشعار!');
    }

    /**
     * عرض تفاصيل طالب معين
     */
    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    /**
     * عرض صفحة تعديل بيانات الطالب
     */
    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    /**
     * تحديث بيانات الطالب واستبدال المرفق القديم إن وجد
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:students,email,' . $student->id,
            'phone'      => 'nullable|string|max:20',
            'gender'     => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        // معالجة استبدال وحذف المرفق القديم
        if ($request->hasFile('avatar')) {
            if ($student->avatar && Storage::disk('public')->exists($student->avatar)) {
                Storage::disk('public')->delete($student->avatar);
            }

            $file = $request->file('avatar');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $validated['avatar'] = $file->storeAs('uploads/students', $filename, 'public');
        }

        $student->update([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? $student->phone,
            'gender'     => $validated['gender'] ?? $student->gender,
            'birth_date' => $validated['birth_date'] ?? $student->birth_date,
            'avatar'     => $validated['avatar'] ?? $student->avatar,
        ]);

        // 🔔 إرسال إشعار تعديل بيانات الطالب
        $users = User::all();
        if ($users->count() > 0) {
            try {
                Notification::send($users, new SystemNotification(
                    'تعديل بيانات طالب',
                    'تم تحديث بيانات الطالب/ة (' . $student->name . ') بنجاح.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Student Update: ' . $e->getMessage());
            }
        }

        return redirect()->route('students.index')->with('success', 'تم تحديث بيانات الطالب بنجاح!');
    }

    /**
     * نقل الطالب إلى الأرشيف (Soft Delete)
     */
    public function destroy(Student $student)
    {
        $studentName = $student->name;
        $student->delete();

        // 🔔 إرسال إشعار نقل الطالب للأرشيف
        $users = User::all();
        if ($users->count() > 0) {
            try {
                Notification::send($users, new SystemNotification(
                    'أرشفة طالب',
                    'تم نقل الطالب/ة (' . $studentName . ') إلى الأرشيف.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Student Soft Delete: ' . $e->getMessage());
            }
        }

        return redirect()->route('students.index')->with('success', 'تم نقل الطالب إلى الأرشيف بنجاح!');
    }

    /**
     * استعادة الطالب من الأرشيف
     */
    public function restore($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $student->restore();

        // 🔔 إرسال إشعار استعادة الطالب
        $users = User::all();
        if ($users->count() > 0) {
            try {
                Notification::send($users, new SystemNotification(
                    'استعادة طالب',
                    'تمت استعادة الطالب/ة (' . $student->name . ') من الأرشيف بنجاح.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Student Restore: ' . $e->getMessage());
            }
        }

        return redirect()->route('students.archive')->with('success', 'تمت استعادة الطالب بنجاح!');
    }

    /**
     * حذف الطالب بشكل نهائي ومسح المرفق من التخزين
     */
    public function forceDelete($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $studentName = $student->name;

        // تنظيف الملف من التخزين
        if ($student->avatar && Storage::disk('public')->exists($student->avatar)) {
            Storage::disk('public')->delete($student->avatar);
        }

        $student->forceDelete();

        // 🔔 إرسال إشعار الحذف النهائي
        $users = User::all();
        if ($users->count() > 0) {
            try {
                Notification::send($users, new SystemNotification(
                    'حذف نهائي لطالب',
                    'تم حذف الطالب/ة (' . $studentName . ') بشكل نهائي من النظام.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Student Force Delete: ' . $e->getMessage());
            }
        }

        return redirect()->route('students.archive')->with('success', 'تم حذف الطالب نهائياً من النظام!');
    }

    /**
     * تفريغ الأرشيف ومسح جميع الملفات المرفقة للطلاب المؤرشفين
     */
    public function forceDeleteAllArchive()
    {
        $trashedStudents = Student::onlyTrashed()->get();
        $count = $trashedStudents->count();

        if ($count === 0) {
            return redirect()->route('students.archive')->with('error', 'الأرشيف فارغ بالفعل!');
        }

        // مسح كافة ملفات الطلاب المؤرشفين قبل الحذف النهائي
        foreach ($trashedStudents as $student) {
            if ($student->avatar && Storage::disk('public')->exists($student->avatar)) {
                Storage::disk('public')->delete($student->avatar);
            }
            $student->forceDelete();
        }

        // 🔔 إرسال إشعار تفريغ أرشيف الطلاب
        $users = User::all();
        if ($users->count() > 0) {
            try {
                Notification::send($users, new SystemNotification(
                    'تفريغ أرشيف الطلاب',
                    'تم تفريغ الأرشيف بحذف (' . $count . ') طالب/ة بشكل نهائي من النظام.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Student Force Delete All: ' . $e->getMessage());
            }
        }

        return redirect()->route('students.archive')->with('success', 'تم تفريغ أرشيف الطلاب بالكامل بنجاح!');
    }

    /**
     * 🟢 تصدير قائمة الطلاب إلى ملف Excel / CSV
     */
    public function export(Request $request)
    {
        return Excel::download(new StudentsExport($request->search), 'students_list.xlsx');
    }

    /**
     * 🟢 استيراد ملف Excel وإدخال الطلاب دفعة واحدة مع إرسال الإشعارات والـ Logs
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        $import = new StudentsImport();
        Excel::import($import, $request->file('file'));

        // 🔔 إرسال إشعار للآدمنز بـ عدد السجلات الناجحة
        $users = User::all();
        if ($users->count() > 0) {
            try {
                Notification::send($users, new SystemNotification(
                    'استيراد بيانات الطلاب',
                    'تم استيراد [' . $import->successCount . '] طالب بنجاح من ملف Excel.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Student Import: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', "تم استيراد {$import->successCount} طالب بنجاح! إذا كانت هناك أسطر تحتوي أخطاء فقد تم تخطيها وتسجيلها في الـ Logs.");
    }
}