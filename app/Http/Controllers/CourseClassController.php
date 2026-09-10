<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class CourseClassController extends Controller
{
    public function index()
    {
        $classes = CourseClass::with(['course', 'teacher', 'classRoom'])->get();
        return view('course_classes.index', compact('classes'));
    }

    public function create()
    {
        $courses = Course::all();
        $teachers = Teacher::all();
        $classRooms = ClassRoom::all();
        
        return view('course_classes.create', compact('courses', 'teachers', 'classRooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'class_number' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|array',
            'days.*' => 'string',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'in:upcoming,ongoing,completed',
        ]);

        CourseClass::create($validated);

        return redirect()->route('course-classes.index')->with('success', 'تم إضافة الشعبة بنجاح');
    }

    public function show(CourseClass $courseClass)
    {
        $courseClass->load(['course', 'teacher', 'classRoom', 'enrollments.student']);
        return view('course_classes.show', compact('courseClass'));
    }

    public function edit(CourseClass $courseClass)
    {
        $courses = Course::all();
        $teachers = Teacher::all();
        $classRooms = ClassRoom::all();

        return view('course_classes.edit', compact('courseClass', 'courses', 'teachers', 'classRooms'));
    }

    public function update(Request $request, CourseClass $courseClass)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'class_number' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|array',
            'days.*' => 'string',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'in:upcoming,ongoing,completed',
        ]);

        $courseClass->update($validated);

        return redirect()->route('course-classes.index')->with('success', 'تم تحديث بيانات الشعبة بنجاح');
    }

    public function destroy(CourseClass $courseClass)
    {
        $courseClass->delete();
        return redirect()->route('course-classes.index')->with('success', 'تم حذف الشعبة بنجاح');
    }
}