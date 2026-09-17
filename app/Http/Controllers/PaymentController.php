<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\CourseClass;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

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

    public function create()
    {
        $students = Student::orderBy('name')->get();
        $courseClasses = CourseClass::with('course')->get();
        return view('payments.create', compact('students', 'courseClasses'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'student_id'      => 'required|exists:students,id',
            'course_class_id' => 'nullable|exists:course_classes,id',
            'amount'          => 'required|numeric|min:0.01',
            'payment_date'    => 'required|date',
            'payment_method'  => 'required|string',
            'receipt_number'  => 'nullable|string|max:50',
            'notes'           => 'nullable|string',
        ]);

        $payment = Payment::create($validatedData);

        // 🔔 إشعارات إنشاء دفعة مالية مع حماية النظام وتسجيل الأخطاء
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'سند قبض جديد',
                    'تم تسجيل دفعة مالية بقيمة (' . $payment->amount . '$) للطالب/ة: ' . ($payment->student->name ?? '')
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Payment Store: ' . $e->getMessage());
            }
        }

        return redirect()->route('payments.index')->with('success', 'تم تسجيل السند بنجاح وإرسال الإشعار!');
    }

    public function edit(Payment $payment)
    {
        $students = Student::orderBy('name')->get();
        $courseClasses = CourseClass::with('course')->get();
        return view('payments.edit', compact('payment', 'students', 'courseClasses'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validatedData = $request->validate([
            'student_id'      => 'required|exists:students,id',
            'course_class_id' => 'nullable|exists:course_classes,id',
            'amount'          => 'required|numeric|min:0.01',
            'payment_date'    => 'required|date',
            'payment_method'  => 'required|string',
            'receipt_number'  => 'nullable|string|max:50',
            'notes'           => 'nullable|string',
        ]);

        $payment->update($validatedData);

        // 🔔 إشعار تعديل بيانات السند مع حماية النظام وتسجيل الأخطاء
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'تعديل سند قبض',
                    'تم تعديل بيانات السند رقم (' . ($payment->receipt_number ?? $payment->id) . ') بقيمة (' . $payment->amount . '$).'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Payment Update: ' . $e->getMessage());
            }
        }

        return redirect()->route('payments.index')->with('success', 'تم تعديل السند بنجاح!');
    }

    /**
     * نقل سند القبض للأرشيف (Soft Delete)
     */
    public function destroy(Payment $payment)
    {
        $receiptNumber = $payment->receipt_number ?? $payment->id;
        $amount = $payment->amount;
        
        $payment->delete(); // تنفيذ Soft Delete تلقائياً

        // 🔔 إشعار أرشفة سند قبض
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'أرشفة سند قبض',
                    'تم نقل سند القبض رقم (' . $receiptNumber . ') بقيمة (' . $amount . '$) إلى الأرشيف.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Payment Archive: ' . $e->getMessage());
            }
        }

        return redirect()->route('payments.index')->with('success', 'تم نقل السند إلى الأرشيف بنجاح!');
    }

    /**
     * عرض أرشيف سندات القبض المحذوفة مؤقتاً
     */
    public function archive(Request $request)
    {
        $query = Payment::onlyTrashed()->with(['student', 'courseClass.course'])->latest('deleted_at');

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

        return view('payments.archive', compact('payments'));
    }

    /**
     * استعادة سند قبض من الأرشيف
     */
    public function restore($id)
    {
        $payment = Payment::onlyTrashed()->findOrFail($id);
        $payment->restore();

        // 🔔 إشعار استعادة السند
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'استعادة سند قبض',
                    'تمت استعادة سند القبض رقم (' . ($payment->receipt_number ?? $payment->id) . ') من الأرشيف بنجاح.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Payment Restore: ' . $e->getMessage());
            }
        }

        return redirect()->route('payments.archive')->with('success', 'تمت استعادة السند بنجاح!');
    }

    /**
     * الحذف النهائي لسند القبض من قاعدة البيانات (Shift + Delete)
     */
    public function forceDelete($id)
    {
        $payment = Payment::onlyTrashed()->findOrFail($id);
        $receiptNumber = $payment->receipt_number ?? $payment->id;
        
        $payment->forceDelete(); // حذف نهائي من قاعدة البيانات

        // 🔔 إشعار الحذف النهائي
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'حذف نهائي لسند قبض',
                    'تم حذف سند القبض رقم (' . $receiptNumber . ') نهائياً من النظام.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Payment ForceDelete: ' . $e->getMessage());
            }
        }

        return redirect()->route('payments.archive')->with('success', 'تم حذف السند نهائياً من النظام!');
    }

    /**
     * حذف جميع السندات المحذوفة من الأرشيف نهائياً (تفريغ الأرشيف)
     */
    public function forceDeleteAllArchive()
    {
        $trashedPayments = Payment::onlyTrashed()->get();
        $count = $trashedPayments->count();

        if ($count === 0) {
            return redirect()->route('payments.archive')->with('error', 'الأرشيف فارغ بالفعل!');
        }

        // حذف كافة سندات القبض المؤرشفة نهائياً
        Payment::onlyTrashed()->forceDelete();

        // 🔔 إرسال إشعار تفريغ أرشيف المدفوعات بالكامل
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'تفريغ أرشيف سندات القبض',
                    'تم تفريغ الأرشيف بحذف (' . $count . ') سند قبض بشكل نهائي من النظام.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Payment Force Delete All: ' . $e->getMessage());
            }
        }

        return redirect()->route('payments.archive')->with('success', 'تم تفريغ أرشيف السندات بالكامل بنجاح!');
    }

    public function print(Payment $payment)
    {
        $payment->load(['student', 'courseClass.course']);
        return view('payments.print', compact('payment'));
    }
}