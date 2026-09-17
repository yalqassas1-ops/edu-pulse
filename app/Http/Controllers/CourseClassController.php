<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

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
            'course_id'     => 'required|exists:courses,id',
            'teacher_id'    => 'required|exists:teachers,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'class_number'  => 'required|string|max:255',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'days'          => 'required|array',
            'days.*'        => 'string',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'status'        => 'in:upcoming,ongoing,completed',
        ]);

        $courseClass = CourseClass::create($validated);
        $courseClass->load('course');

        // 🔔 إشعار إضافة شعبة جديدة
        $users = User::all();
        if ($users->isNotEmpty()) {
            $courseTitle = $courseClass->course->title ?? '';
            $className = "شعبة (" . $courseClass->class_number . ")" . ($courseTitle ? " - " . $courseTitle : "");

            try {
                Notification::send($users, new SystemNotification(
                    'إضافة شعبة جديدة',
                    'تمت إضافة ' . $className . ' بنجاح إلى النظام.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on CourseClass Store: ' . $e->getMessage());
            }
        }

        return redirect()->route('course-classes.index')->with('success', 'تم إضافة الشعبة بنجاح وإرسال الإشعار');
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
            'course_id'     => 'required|exists:courses,id',
            'teacher_id'    => 'required|exists:teachers,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'class_number'  => 'required|string|max:255',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'days'          => 'required|array',
            'days.*'        => 'string',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'status'        => 'in:upcoming,ongoing,completed',
        ]);

        $courseClass->update($validated);
        $courseClass->load('course');

        // 🔔 إشعار تعديل الشعبة
        $users = User::all();
        if ($users->isNotEmpty()) {
            $courseTitle = $courseClass->course->title ?? '';
            $className = "شعبة (" . $courseClass->class_number . ")" . ($courseTitle ? " - " . $courseTitle : "");

            try {
                Notification::send($users, new SystemNotification(
                    'تعديل بيانات شعبة',
                    'تم تحديث بيانات ' . $className . ' بنجاح.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on CourseClass Update: ' . $e->getMessage());
            }
        }

        return redirect()->route('course-classes.index')->with('success', 'تم تحديث بيانات الشعبة بنجاح');
    }

    // 📦 1. أرشفة الشعبة (Soft Delete)
    public function destroy(CourseClass $courseClass)
    {
        $courseClass->load('course');
        $courseTitle = $courseClass->course->title ?? '';
        $className = "شعبة (" . $courseClass->class_number . ")" . ($courseTitle ? " - " . $courseTitle : "");

        $courseClass->delete();

        // 🔔 إشعار أرشفة الشعبة
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'أرشفة شعبة دراسية',
                    'تم نقل ' . $className . ' إلى الأرشيف.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on CourseClass Delete: ' . $e->getMessage());
            }
        }

        return redirect()->route('course-classes.index')->with('success', 'تم نقل الشعبة إلى الأرشيف بنجاح');
    }

    // 📦 2. عرض الأرشيف (الشعب المؤرشفة فقط)
    public function archive()
    {
        $classes = CourseClass::onlyTrashed()->with(['course', 'teacher', 'classRoom'])->get();
        return view('course_classes.archive', compact('classes'));
    }

    // 🔄 3. استعادة شعبة من الأرشيف (Restore)
    public function restore($id)
    {
        $courseClass = CourseClass::onlyTrashed()->findOrFail($id);
        $courseClass->restore();

        $courseClass->load('course');
        $courseTitle = $courseClass->course->title ?? '';
        $className = "شعبة (" . $courseClass->class_number . ")" . ($courseTitle ? " - " . $courseTitle : "");

        // 🔔 إشعار استعادة الشعبة
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'استعادة شعبة دراسية',
                    'تمت استعادة ' . $className . ' من الأرشيف.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on CourseClass Restore: ' . $e->getMessage());
            }
        }

        return redirect()->route('course-classes.archive')->with('success', 'تمت استعادة الشعبة بنجاح');
    }

    // ❌ 4. الحذف النهائي من قاعدة البيانات (Force Delete)
    public function forceDelete($id)
    {
        $courseClass = CourseClass::onlyTrashed()->findOrFail($id);
        $courseClass->forceDelete();

        return redirect()->route('course-classes.archive')->with('success', 'تم حذف الشعبة نهائياً من النظام');
    }

    // 🗑️ 5. تفريغ كافة الشعب الدراسية المؤرشفة دفعة واحدة (حذف نهائي)
    public function forceDeleteAllArchive()
    {
        $trashedClasses = CourseClass::onlyTrashed()->get();

        if ($trashedClasses->isEmpty()) {
            return redirect()->route('course-classes.archive')->with('error', 'الأرشيف فارغ بالفعل!');
        }

        foreach ($trashedClasses as $class) {
            $class->forceDelete();
        }

        return redirect()->route('course-classes.archive')->with('success', 'تم تفريغ أرشيف الشعب الدراسية بالكامل بنجاح!');
    }
}