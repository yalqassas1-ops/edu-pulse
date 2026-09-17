<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\CourseClass;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

class EnrollmentController extends Controller
{
    /**
     * عرض قائمة التسجيلات النشطة
     */
    public function index()
    {
        $enrollments = Enrollment::with([
            'student', 
            'courseClass.course', 
            'courseClass.teacher'
        ])->latest()->get();

        return view('enrollments.index', compact('enrollments'));
    }

    /**
     * عرض صفحة إنشاء تسجيل جديد
     */
    public function create()
    {
        $students = Student::all();
        $courseClasses = CourseClass::with(['course', 'teacher'])->get();

        return view('enrollments.create', compact('students', 'courseClasses'));
    }

    /**
     * حفظ البيانات في قاعدة البيانات وإرسال الإشعار
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id'      => 'required|exists:students,id',
            'course_class_id' => 'required',
            'status'          => 'required|in:active,completed,cancelled',
        ], [
            'student_id.required'      => 'يرجى اختيار الطالب',
            'course_class_id.required' => 'يرجى اختيار الشعبة الدراسية',
            'status.required'          => 'يرجى تحديد حالة التسجيل',
            'status.in'                => 'حالة التسجيل غير صالحة',
        ]);

        $classColumn = Schema::hasColumn('enrollments', 'course_class_id') ? 'course_class_id' : 'course_id';

        // 1. منع تكرار تسجيل الطالب
        $exists = Enrollment::where('student_id', $request->student_id)
            ->where($classColumn, $request->course_class_id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'الطالب مسجل بالفعل في هذه الشعبة!');
        }

        // 2. إعداد السجل والحفظ
        $date = $request->enrollment_date ?? $request->enrolled_at ?? date('Y-m-d');

        $enrollment = new Enrollment();
        $enrollment->student_id = $request->student_id;
        $enrollment->{$classColumn} = $request->course_class_id;
        $enrollment->status     = $request->status;

        if (Schema::hasColumn('enrollments', 'enrollment_date')) {
            $enrollment->enrollment_date = $date;
        }
        if (Schema::hasColumn('enrollments', 'enrolled_at')) {
            $enrollment->enrolled_at = $date;
        }

        $enrollment->save();
        $enrollment->load(['student', 'courseClass.course']);

        // 🔔 3. إرسال إشعار التسجيل برقم الشعبة واسمها
        $users = User::all();
        if ($users->isNotEmpty()) {
            $studentName = $enrollment->student->name ?? 'طالب';
            
            $classNumber = $enrollment->courseClass->class_number 
                        ?? $enrollment->courseClass->code 
                        ?? $enrollment->courseClass->id;

            $className = $enrollment->courseClass->name 
                      ?? $enrollment->courseClass->course->title 
                      ?? '';

            $fullClassName = "شعبة (" . $classNumber . ") - " . $className;

            try {
                Notification::send($users, new SystemNotification(
                    'تسجيل جديد في شعبة',
                    'تم تسجيل الطالب/ة (' . $studentName . ') في ' . $fullClassName
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Enrollment Store: ' . $e->getMessage());
            }
        }

        return redirect('/enrollments')->with('success', 'تم حفظ التسجيل بنجاح وإرسال الإشعار!');
    }

    /**
     * عرض صفحة تعديل التسجيل
     */
    public function edit($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $students = Student::all();
        $courseClasses = CourseClass::with(['course', 'teacher'])->get();

        return view('enrollments.edit', compact('enrollment', 'students', 'courseClasses'));
    }

    /**
     * تحديث بيانات التسجيل في قاعدة البيانات وإرسال الإشعار
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'student_id'      => 'required|exists:students,id',
            'course_class_id' => 'required',
            'status'          => 'required|in:active,completed,cancelled',
        ], [
            'student_id.required'      => 'يرجى اختيار الطالب',
            'course_class_id.required' => 'يرجى اختيار الشعبة الدراسية',
            'status.required'          => 'يرجى تحديد حالة التسجيل',
            'status.in'                => 'حالة التسجيل غير صالحة',
        ]);

        $enrollment = Enrollment::findOrFail($id);
        $classColumn = Schema::hasColumn('enrollments', 'course_class_id') ? 'course_class_id' : 'course_id';
        $date = $request->enrollment_date ?? $request->enrolled_at ?? date('Y-m-d');

        $enrollment->student_id = $request->student_id;
        $enrollment->{$classColumn} = $request->course_class_id;
        $enrollment->status     = $request->status;

        if (Schema::hasColumn('enrollments', 'enrollment_date')) {
            $enrollment->enrollment_date = $date;
        }
        if (Schema::hasColumn('enrollments', 'enrolled_at')) {
            $enrollment->enrolled_at = $date;
        }

        $enrollment->save();
        $enrollment->load(['student', 'courseClass.course']);

        // 🔔 إرسال إشعار تعديل البيانات
        $users = User::all();
        if ($users->isNotEmpty()) {
            $studentName = $enrollment->student->name ?? 'طالب';
            
            try {
                Notification::send($users, new SystemNotification(
                    'تعديل تسجيل شعبة',
                    'تم تحديث بيانات تسجيل الطالب/ة (' . $studentName . ') بنجاح.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Enrollment Update: ' . $e->getMessage());
            }
        }

        return redirect('/enrollments')->with('success', 'تم تحديث التسجيل بنجاح.');
    }

    /**
     * نقل التسجيل إلى الأرشيف (Soft Delete)
     */
    public function destroy($id)
    {
        $enrollment = Enrollment::with(['student', 'courseClass.course'])->findOrFail($id);
        
        $studentName = $enrollment->student->name ?? 'طالب';
        $classNumber = $enrollment->courseClass->class_number 
                    ?? $enrollment->courseClass->code 
                    ?? $enrollment->courseClass->id;

        $className = $enrollment->courseClass->name 
                  ?? $enrollment->courseClass->course->title 
                  ?? '';

        $fullClassName = "شعبة (" . $classNumber . ") - " . $className;

        $enrollment->delete();

        // 🔔 إرسال إشعار الأرشفة
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'أرشفة تسجيل شعبة',
                    'تم نقل تسجيل الطالب/ة (' . $studentName . ') في ' . $fullClassName . ' إلى الأرشيف.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Enrollment Soft Delete: ' . $e->getMessage());
            }
        }

        return redirect('/enrollments')->with('success', 'تم نقل التسجيل إلى الأرشيف بنجاح.');
    }

    /**
     * عرض أرشيف التسجيلات المحذوفة مؤقتاً
     */
    public function archive()
    {
        $enrollments = Enrollment::onlyTrashed()
            ->with([
                'student', 
                'courseClass.course', 
                'courseClass.teacher'
            ])
            ->latest()
            ->get();

        return view('enrollments.archive', compact('enrollments'));
    }

    /**
     * استعادة التسجيل من الأرشيف
     */
    public function restore($id)
    {
        $enrollment = Enrollment::onlyTrashed()->findOrFail($id);
        $enrollment->restore();

        return back()->with('success', 'تمت استعادة التسجيل بنجاح!');
    }

    /**
     * الحذف النهائي للتسجيل من قاعدة البيانات
     */
    public function forceDelete($id)
    {
        $enrollment = Enrollment::onlyTrashed()->findOrFail($id);
        $enrollment->forceDelete();

        return back()->with('success', 'تم حذف التسجيل نهائياً من النظام!');
    }

    /**
     * تفريغ الأرشيف وحذف جميع التسجيلات المؤرشفة نهائياً
     */
    public function forceDeleteAllArchive()
    {
        $trashedEnrollments = Enrollment::onlyTrashed()->get();

        if ($trashedEnrollments->isEmpty()) {
            return back()->with('error', 'الأرشيف فارغ بالفعل!');
        }

        foreach ($trashedEnrollments as $enrollment) {
            $enrollment->forceDelete();
        }

        return back()->with('success', 'تم تفريغ أرشيف التسجيلات وحذف جميع السجلات المؤرشفة نهائياً!');
    }
}