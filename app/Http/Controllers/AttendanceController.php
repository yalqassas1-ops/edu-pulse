<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

class AttendanceController extends Controller
{
    /**
     * عرض صفحة تسجيل الحضور والغياب بناءً على الشعبة والتاريخ المحددين
     */
    public function index(Request $request)
    {
        $courseClasses = CourseClass::with('course')->get();
        $selectedClassId = $request->query('course_class_id');
        $selectedDate = $request->query('date', date('Y-m-d'));

        $students = collect();
        $existingAttendances = [];

        if ($selectedClassId) {
            $class = CourseClass::find($selectedClassId);

            if ($class) {
                // جلب التسجيلات بسؤال قاعدة البيانات عن الشعبة والدورة لضمان إظهار الطلاب
                $enrollments = Enrollment::where(function ($query) use ($class) {
                    if (Schema::hasColumn('enrollments', 'course_class_id')) {
                        $query->where('course_class_id', $class->id);
                    }
                    $query->orWhere('course_id', $class->id)
                          ->orWhere('course_id', $class->course_id);
                })
                ->with('student')
                ->get();

                $students = $enrollments->map(fn($e) => $e->student)->filter()->unique('id');
            }

            $existingAttendances = Attendance::where('course_class_id', $selectedClassId)
                ->where('date', $selectedDate)
                ->pluck('status', 'student_id')
                ->toArray();
        }

        return view('attendances.index', compact(
            'courseClasses',
            'selectedClassId',
            'selectedDate',
            'students',
            'existingAttendances'
        ));
    }

    /**
     * عرض تقرير الحضور والغياب الشامل للطلاب
     */
    public function report(Request $request)
    {
        $courseClasses = CourseClass::with('course')->get();
        $selectedClassId = $request->query('course_class_id');

        $reportData = collect();

        if ($selectedClassId) {
            $class = CourseClass::find($selectedClassId);

            if ($class) {
                // استعلام التقرير ليشمل الطلاب المضافين للشعبة
                $enrollments = Enrollment::where(function ($query) use ($class) {
                    if (Schema::hasColumn('enrollments', 'course_class_id')) {
                        $query->where('course_class_id', $class->id);
                    }
                    $query->orWhere('course_id', $class->id)
                          ->orWhere('course_id', $class->course_id);
                })
                ->with('student')
                ->get();

                $students = $enrollments->map(fn($e) => $e->student)->filter()->unique('id');

                // جلب كافة سجلات الحضور الخاصة بهذه الشعبة
                $attendances = Attendance::where('course_class_id', $selectedClassId)->get();

                // تجميع إحصائيات الحضور/الغياب/التأخير لكل طالب
                $reportData = $students->map(function ($student) use ($attendances) {
                    $studentAttendances = $attendances->where('student_id', $student->id);

                    return (object) [
                        'id'            => $student->id,
                        'name'          => $student->name,
                        'phone'         => $student->phone ?? 'غير متوفر',
                        'present_count' => $studentAttendances->where('status', 'present')->count(),
                        'absent_count'  => $studentAttendances->where('status', 'absent')->count(),
                        'late_count'    => $studentAttendances->where('status', 'late')->count(),
                        'total_days'    => $studentAttendances->count(),
                    ];
                });
            }
        }

        return view('attendances.report', compact('courseClasses', 'selectedClassId', 'reportData'));
    }

    /**
     * عرض صفحة إضافة سجل حضور جديد منفرد
     */
    public function create()
    {
        $courseClasses = CourseClass::with('course')->get();
        $students = Student::all();

        return view('attendances.create', compact('courseClasses', 'students'));
    }

    /**
     * حفظ أو تحديث سجلات الحضور والغياب لجميع الطلاب دفعة واحدة (أو لسجل منفرد)
     */
    public function store(Request $request)
    {
        // 1. معالجة الحفظ المنفرد (في حال تم إرسال student_id)
        if ($request->filled('student_id')) {
            $request->validate([
                'course_class_id' => 'required|exists:course_classes,id',
                'student_id'      => 'required|exists:students,id',
                'date'            => 'required|date',
                'status'          => 'required|in:present,absent,late',
            ], [
                'course_class_id.required' => 'يرجى اختيار الشعبة الدراسية',
                'student_id.required'      => 'يرجى اختيار الطالب',
                'date.required'            => 'يرجى تحديد التاريخ',
                'status.required'          => 'يرجى تحديد حالة الحضور',
            ]);

            Attendance::updateOrCreate(
                [
                    'student_id'      => $request->student_id,
                    'course_class_id' => $request->course_class_id,
                    'date'            => $request->date,
                ],
                [
                    'status' => $request->status,
                    'notes'  => $request->notes,
                ]
            );

            // 🔔 إرسال الإشعار عند الحفظ المنفرد مع حماية النظام وتجسير الأخطاء
            $class = CourseClass::with('course')->find($request->course_class_id);
            $users = User::all();
            if ($class && $users->isNotEmpty()) {
                $classNum = $class->class_number ?? $class->id;
                $courseTitle = $class->course->title ?? '';
                $className = "شعبة (" . $classNum . ")" . ($courseTitle ? " - " . $courseTitle : "");

                try {
                    Notification::send($users, new SystemNotification(
                        'تسجيل حضور طالب',
                        'تم تسجيل حضور/غياب طالب في ' . $className . ' بتاريخ: ' . $request->date
                    ));
                } catch (\Exception $e) {
                    Log::error('Broadcast Connection Failed on Single Attendance Store: ' . $e->getMessage());
                }
            }

            return redirect()->route('attendances.index', [
                'course_class_id' => $request->course_class_id,
                'date'            => $request->date,
            ])->with('success', 'تم حفظ سجل الحضور بنجاح وإرسال الإشعار!');
        }

        // 2. معالجة الحفظ الجماعي للجدول
        $request->validate([
            'course_class_id' => 'required|exists:course_classes,id',
            'date'            => 'required|date',
            'attendances'     => 'nullable|array',
        ], [
            'course_class_id.required' => 'يرجى اختيار الشعبة الدراسية',
            'date.required'            => 'يرجى تحديد التاريخ',
        ]);

        $classId = $request->course_class_id;
        $date = $request->date;
        $attendances = $request->input('attendances', []);

        if (!empty($attendances)) {
            foreach ($attendances as $studentId => $status) {
                Attendance::updateOrCreate(
                    [
                        'student_id'      => $studentId,
                        'course_class_id' => $classId,
                        'date'            => $date,
                    ],
                    [
                        'status' => $status,
                    ]
                );
            }

            // 🔔 إرسال الإشعار لمرة واحدة بعد حفظ حضور الجدول بالكامل مع حماية النظام وتجسير الأخطاء
            $class = CourseClass::with('course')->find($classId);
            $users = User::all();
            if ($class && $users->isNotEmpty()) {
                $classNum = $class->class_number ?? $class->id;
                $courseTitle = $class->course->title ?? '';
                $className = "شعبة (" . $classNum . ")" . ($courseTitle ? " - " . $courseTitle : "");

                try {
                    Notification::send($users, new SystemNotification(
                        'حفظ جدول الحضور والغياب',
                        'تم تسجيل جدول الحضور والغياب كاملاً لـ ' . $className . ' بتاريخ: ' . $date
                    ));
                } catch (\Exception $e) {
                    Log::error('Broadcast Connection Failed on Bulk Attendance Store: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('attendances.index', [
            'course_class_id' => $classId,
            'date'            => $date,
        ])->with('success', 'تم حفظ سجل الحضور والغياب بنجاح وإرسال الإشعار!');
    }

    /**
     * عرض صفحة تعديل سجل حضور طالب معين
     */
    public function edit($id)
    {
        $attendance = Attendance::with(['student', 'courseClass.course'])->findOrFail($id);

        return view('attendances.edit', compact('attendance'));
    }

    /**
     * تحديث حالة الحضور والغياب لسجل فردي
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:present,absent,late',
            'date'   => 'required|date',
        ], [
            'status.required' => 'يرجى تحديد حالة الحضور',
            'status.in'       => 'حالة الحضور غير صالحة',
            'date.required'   => 'يرجى تحديد التاريخ',
        ]);

        $attendance = Attendance::with(['student', 'courseClass.course'])->findOrFail($id);

        $attendance->update([
            'status' => $request->status,
            'date'   => $request->date,
            'notes'  => $request->notes ?? $attendance->notes,
        ]);

        // 🔔 إرسال إشعار التحديث مع حماية النظام وتجسير الأخطاء
        $users = User::all();
        if ($users->isNotEmpty()) {
            $studentName = $attendance->student->name ?? 'طالب';

            try {
                Notification::send($users, new SystemNotification(
                    'تحديث سجل حضور',
                    'تم تعديل سجل حضور الطالب/ة (' . $studentName . ') بتاريخ: ' . $attendance->date
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Attendance Update: ' . $e->getMessage());
            }
        }

        return redirect()->route('attendances.index', [
            'course_class_id' => $attendance->course_class_id,
            'date'            => $attendance->date,
        ])->with('success', 'تم تحديث سجل الحضور بنجاح!');
    }

    /**
     * حذف سجل حضور
     */
    public function destroy($id)
    {
        $attendance = Attendance::with('student')->findOrFail($id);
        $classId = $attendance->course_class_id;
        $date = $attendance->date;
        $studentName = $attendance->student->name ?? 'طالب';

        $attendance->delete();

        // 🔔 إرسال إشعار الحذف مع حماية النظام وتجسير الأخطاء
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'حذف سجل حضور',
                    'تم حذف سجل حضور الطالب/ة (' . $studentName . ') المؤرخ في: ' . $date
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Attendance Delete: ' . $e->getMessage());
            }
        }

        return redirect()->route('attendances.index', [
            'course_class_id' => $classId,
            'date'            => $date,
        ])->with('success', 'تم حذف سجل الحضور بنجاح!');
    }
}