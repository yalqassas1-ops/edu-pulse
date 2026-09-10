<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\CourseClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EnrollmentController extends Controller
{
    /**
     * عرض قائمة التسجيلات
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
     * حفظ البيانات في قاعدة البيانات
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

        return redirect('/enrollments')->with('success', 'تم حفظ التسجيل بنجاح!');
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
     * تحديث بيانات التسجيل في قاعدة البيانات
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

        return redirect('/enrollments')->with('success', 'تم تحديث التسجيل بنجاح.');
    }

    /**
     * حذف تسجيل
     */
    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return redirect('/enrollments')->with('success', 'تم حذف التسجيل بنجاح.');
    }
}