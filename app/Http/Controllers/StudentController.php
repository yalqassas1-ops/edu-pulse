<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class StudentController extends Controller
{
    /**
     * عرض قائمة الطلاب
     */
    public function index()
    {
        $students = Student::latest()->get();
        return view('students.index', compact('students'));
    }

    /**
     * عرض صفحة إنشاء طالب جديد
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * حفظ البيانات عند إضافة طالب جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:students,email',
            'phone'      => 'nullable|string|max:20',
            'gender'     => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
        ]);

        try {
            $student = Student::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'gender'     => $request->gender ?? 'male',
                'birth_date' => $request->birth_date ?? date('Y-m-d'),
            ]);

            Log::info('Student created successfully', [
                'student_id' => $student->getKey(),
                'user_id'    => auth()->id()
            ]);

            return redirect()->route('students.index')->with('success', 'تم إضافة الطالب بنجاح!');

        } catch (Throwable $e) {
            Log::error('Failed to create student', [
                'error'   => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الطالب');
        }
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
     * تحديث بيانات الطالب في قاعدة البيانات
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:students,email,' . $student->getKey(),
            'phone'      => 'nullable|string|max:20',
            'gender'     => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
        ]);

        try {
            $student->update([
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'gender'     => $request->gender ?? $student->gender,
                'birth_date' => $request->birth_date ?? $student->birth_date,
            ]);

            Log::info('Student updated successfully', [
                'student_id' => $student->getKey(),
                'user_id'    => auth()->id()
            ]);

            return redirect()->route('students.index')->with('success', 'تم تحديث بيانات الطالب بنجاح!');

        } catch (Throwable $e) {
            Log::error('Failed to update student', [
                'student_id' => $student->getKey(),
                'error'      => $e->getMessage(),
                'user_id'    => auth()->id()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث بيانات الطالب');
        }
    }

    /**
     * حذف الطالب من قاعدة البيانات
     */
    public function destroy(Student $student)
    {
        try {
            $studentId = $student->getKey();
            $student->delete();

            Log::info('Student deleted successfully', [
                'deleted_student_id' => $studentId,
                'user_id'            => auth()->id()
            ]);

            return redirect()->route('students.index')->with('success', 'تم حذف الطالب بنجاح!');

        } catch (Throwable $e) {
            Log::error('Failed to delete student', [
                'student_id' => $student->getKey(),
                'error'      => $e->getMessage(),
                'user_id'    => auth()->id()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف الطالب');
        }
    }
}