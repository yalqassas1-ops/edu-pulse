<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\CourseClass;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['student', 'courseClass.course'])->latest('payment_date');

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sQuery) use ($search) {
                      $sQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(15);
        $students = Student::orderBy('name')->get();
        $courseClasses = CourseClass::with('course')->get();
        $totalAmount = Payment::sum('amount');

        return view('payments.index', compact('payments', 'students', 'courseClasses', 'totalAmount'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_class_id' => 'nullable|exists:course_classes,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'receipt_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        Payment::create($validatedData);

        return redirect()->route('payments.index')->with('success', 'تم تسجيل السند بنجاح!');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'تم حذف السند بنجاح!');
    }

    public function print(Payment $payment)
    {
        $payment->load(['student', 'courseClass.course']);
        return view('payments.print', compact('payment'));
    }
}