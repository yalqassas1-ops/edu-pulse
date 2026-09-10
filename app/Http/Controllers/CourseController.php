<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\Teacher;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['category', 'teacher'])->latest()->get();
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        // جلب التصنيفات واستبعاد أي تصنيف يحمل اسماً مفبركاً أو افتراضياً
        $categories = Category::all()->filter(function ($category) {
            $name = trim($category->name);
            return $name !== '' && !str_contains($name, 'اختر التصنيف') && !str_contains($name, '--');
        });

        $teachers = Teacher::all();

        return view('courses.create', compact('categories', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'teacher_id'  => 'nullable|exists:teachers,id',
            'title'       => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'total_hours' => 'required|integer|min:1',
        ]);

        Course::create($request->all());

        return redirect()->route('courses.index')->with('success', 'تمت إضافة الدورة بنجاح!');
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
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'teacher_id'  => 'nullable|exists:teachers,id',
            'title'       => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'total_hours' => 'required|integer|min:1',
        ]);

        $course->update($request->all());

        return redirect()->route('courses.index')->with('success', 'تم تحديث الدورة بنجاح!');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'تم حذف الدورة بنجاح!');
    }
}