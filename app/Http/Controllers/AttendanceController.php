<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;

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
                $enrollments = Enrollment::where('course_id', $class->course_id)
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
                // جلب جميع الطلاب المسجلين بهذه الدورة
                $enrollments = Enrollment::where('course_id', $class->course_id)
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

            return redirect()->route('attendances.index', [
                'course_class_id' => $request->course_class_id,
                'date'            => $request->date,
            ])->with('success', 'تم حفظ سجل الحضور بنجاح!');
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
        }

        return redirect()->route('attendances.index', [
            'course_class_id' => $classId,
            'date'            => $date,
        ])->with('success', 'تم حفظ سجل الحضور والغياب بنجاح!');
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

        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'status' => $request->status,
            'date'   => $request->date,
            'notes'  => $request->notes ?? $attendance->notes,
        ]);

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
        $attendance = Attendance::findOrFail($id);
        $classId = $attendance->course_class_id;
        $date = $attendance->date;

        $attendance->delete();

        return redirect()->route('attendances.index', [
            'course_class_id' => $classId,
            'date'            => $date,
        ])->with('success', 'تم حذف سجل الحضور بنجاح!');
    }
}