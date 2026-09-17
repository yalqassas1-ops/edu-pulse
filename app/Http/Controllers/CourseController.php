<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['category', 'teacher'])->latest()->get();
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::all()->filter(function ($category) {
            $name = trim($category->name);
            return $name !== '' && !str_contains($name, 'اختر التصنيف') && !str_contains($name, '--');
        });

        $teachers = Teacher::all();

        return view('courses.create', compact('categories', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'teacher_id'  => 'nullable|exists:teachers,id',
            'title'       => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'total_hours' => 'required|integer|min:1',
            'avatar'      => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048', // 👈 تم التعديل إلى file لتشمل PDF
        ]);

        // معالجة المرفق/الصورة باستخدام UUID
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $validated['avatar'] = $file->storeAs('uploads/courses', $filename, 'public');
        }

        $course = Course::create($validated);

        // 🔔 إشعار إضافة دورة جديدة
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'إضافة دورة جديدة',
                    'تمت إضافة دورة تدريبية جديدة بعنوان (' . $course->title . ') بسعر: ' . $course->price . '$'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Course Store: ' . $e->getMessage());
            }
        }

        return redirect()->route('courses.index')->with('success', 'تمت إضافة الدورة بنجاح وإرسال الإشعار!');
    }

    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $categories = Category::all()->filter(function ($category) {
            $name = trim($category->name);
            return $name !== '' && !str_contains($name, 'اختر التصنيف') && !str_contains($name, '--');
        });

        $teachers = Teacher::all();

        return view('courses.edit', compact('course', 'categories', 'teachers'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'teacher_id'  => 'nullable|exists:teachers,id',
            'title'       => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'total_hours' => 'required|integer|min:1',
            'avatar'      => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048', // 👈 تم التعديل إلى file لتشمل PDF
        ]);

        // معالجة استبدال وحذف المرفق القديم إن وجد
        if ($request->hasFile('avatar')) {
            if ($course->avatar && Storage::disk('public')->exists($course->avatar)) {
                Storage::disk('public')->delete($course->avatar);
            }

            $file = $request->file('avatar');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $validated['avatar'] = $file->storeAs('uploads/courses', $filename, 'public');
        }

        $course->update($validated);

        // 🔔 إشعار تعديل الدورة
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'تعديل دورة تدريبية',
                    'تم تحديث بيانات الدورة التدريبية (' . $course->title . ') بنجاح.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Course Update: ' . $e->getMessage());
            }
        }

        return redirect()->route('courses.index')->with('success', 'تم تحديث الدورة بنجاح!');
    }

    public function destroy(Course $course)
    {
        $courseTitle = $course->title;
        $course->delete();

        // 🔔 إشعار نقل الدورة للأرشيف
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'أرشفة دورة تدريبية',
                    'تم نقل الدورة التدريبية (' . $courseTitle . ') إلى الأرشيف.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Course Delete: ' . $e->getMessage());
            }
        }

        return redirect()->route('courses.index')->with('success', 'تم نقل الدورة إلى الأرشيف بنجاح!');
    }

    // 📁 عرض الدورات المؤرشفة (Soft Deleted)
    public function archive()
    {
        $courses = Course::onlyTrashed()->with(['category', 'teacher'])->latest()->get();
        return view('courses.archive', compact('courses'));
    }

    // 🔄 استعادة دورة من الأرشيف
    public function restore($id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);
        $course->restore();

        return redirect()->route('courses.archive')->with('success', 'تمت استعادة الدورة بنجاح!');
    }

    // ❌ حذف نهائي لدورة واحدة من الأرشيف ومسح المرفق
    public function forceDelete($id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);

        if ($course->avatar && Storage::disk('public')->exists($course->avatar)) {
            Storage::disk('public')->delete($course->avatar);
        }

        $course->forceDelete();

        return redirect()->route('courses.archive')->with('success', 'تم حذف الدورة نهائياً من النظام!');
    }

    // 🗑️ تفريغ كافة الدورات المؤرشفة دفعة واحدة ومسح ملفاتها
    public function forceDeleteAllArchive()
    {
        $trashedCourses = Course::onlyTrashed()->get();

        if ($trashedCourses->isEmpty()) {
            return redirect()->route('courses.archive')->with('error', 'الأرشيف فارغ بالفعل!');
        }

        foreach ($trashedCourses as $course) {
            if ($course->avatar && Storage::disk('public')->exists($course->avatar)) {
                Storage::disk('public')->delete($course->avatar);
            }
            $course->forceDelete();
        }

        return redirect()->route('courses.archive')->with('success', 'تم تفريغ أرشيف الدورات بالكامل بنجاح!');
    }
}