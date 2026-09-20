<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// مسارات النظام المجهزة بـ Auth Middleware
Route::middleware(['auth'])->group(function () {

    // دالة مجمعة لجلب بيانات الإحصائيات مع دالة مساعدة لتفادي أخطاء الأعمدة المفقودة
    $getDashboardView = function () {
        // دالة مساعدة لحساب العدد بأمان حتى لو لم تكن تهيئة SoftDeletes مكتملة
        $safeCount = function ($table) {
            if (!Schema::hasTable($table)) {
                return 0;
            }
            $query = DB::table($table);
            if (Schema::hasColumn($table, 'deleted_at')) {
                $query->whereNull('deleted_at');
            }
            return $query->count();
        };

        $stats = [
            'studentsCount'      => $safeCount('students'),
            'coursesCount'       => $safeCount('courses'),
            'teachersCount'      => $safeCount('teachers'),
            'categoriesCount'    => $safeCount('categories'),
            'classRoomsCount'    => $safeCount('class_rooms'),
            'courseClassesCount' => $safeCount('course_classes'),
            'enrollmentsCount'   => $safeCount('enrollments'),
            'attendancesCount'   => $safeCount('attendances'),
            'totalPayments'      => Schema::hasTable('payments') ? DB::table('payments')->whereNull('deleted_at')->sum('amount') : 0,
        ];

        return view('dashboard', compact('stats'));
    };

    // الصفحة الرئيسية ولوحة التحكم
    Route::get('/', $getDashboardView);
    Route::get('/dashboard', $getDashboardView)->name('dashboard');

    // 📁 مسارات الأرشيف والحذف النهائي والاستعادة الخاصة بالدورات (Soft Delete)
    Route::get('/courses/archive', [CourseController::class, 'archive'])->name('courses.archive');
    Route::delete('/courses/archive/force-delete-all', [CourseController::class, 'forceDeleteAllArchive'])->name('courses.archive.force-delete-all');
    Route::patch('/courses/{id}/restore', [CourseController::class, 'restore'])->name('courses.restore');
    Route::delete('/courses/{id}/force-delete', [CourseController::class, 'forceDelete'])->name('courses.forceDelete');

    // 📁 مسارات الأرشيف والحذف النهائي والاستعادة الخاصة بالتصنيفات (Soft Delete)
    Route::get('/categories/archive', [CategoryController::class, 'archive'])->name('categories.archive');
    Route::delete('/categories/archive/force-delete-all', [CategoryController::class, 'forceDeleteAllArchive'])->name('categories.forceDeleteAllArchive');
    Route::post('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('/categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.forceDelete');

    // 📁 مسارات الأرشيف والحذف النهائي والاستعادة الخاصة بالمحاضرين (Soft Delete)
    Route::get('/teachers/archive', [TeacherController::class, 'archive'])->name('teachers.archive');
    Route::delete('/teachers/archive/force-delete-all', [TeacherController::class, 'forceDeleteAllArchive'])->name('teachers.archive.force-delete-all');
    Route::patch('/teachers/{id}/restore', [TeacherController::class, 'restore'])->name('teachers.restore');
    Route::delete('/teachers/{id}/force-delete', [TeacherController::class, 'forceDelete'])->name('teachers.forceDelete');

    // 📁 مسارات الأرشيف والحذف النهائي والاستعادة الخاصة بالطلاب (Soft Delete)
    Route::get('/students/archive', [StudentController::class, 'archive'])->name('students.archive');
    Route::delete('/students/archive/force-delete-all', [StudentController::class, 'forceDeleteAllArchive'])->name('students.archive.force-delete-all');
    Route::patch('/students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
    Route::delete('/students/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('students.forceDelete');

    // 📁 مسارات الأرشيف والحذف النهائي والاستعادة الخاصة بالتسجيلات (Soft Delete)
    Route::get('/enrollments/archive', [EnrollmentController::class, 'archive'])->name('enrollments.archive');
    Route::delete('/enrollments/archive/force-delete-all', [EnrollmentController::class, 'forceDeleteAllArchive'])->name('enrollments.archive.force-delete-all');
    Route::patch('/enrollments/{id}/restore', [EnrollmentController::class, 'restore'])->name('enrollments.restore');
    Route::delete('/enrollments/{id}/force-delete', [EnrollmentController::class, 'forceDelete'])->name('enrollments.forceDelete');

    // 📁 مسارات الأرشيف والحذف النهائي والاستعادة الخاصة بالشعب الدراسية (Soft Delete)
    Route::get('/course-classes/archive', [CourseClassController::class, 'archive'])->name('course-classes.archive');
    Route::delete('/course-classes/archive/force-delete-all', [CourseClassController::class, 'forceDeleteAllArchive'])->name('course-classes.archive.force-delete-all');
    Route::patch('/course-classes/{id}/restore', [CourseClassController::class, 'restore'])->name('course-classes.restore');
    Route::delete('/course-classes/{id}/force-delete', [CourseClassController::class, 'forceDelete'])->name('course-classes.forceDelete');

    // 📁 مسارات الأرشيف والحذف النهائي والاستعادة الخاصة بالقاعات الدراسية (Soft Delete)
    Route::get('/class-rooms/archive', [ClassRoomController::class, 'archive'])->name('class-rooms.archive');
    Route::delete('/class-rooms/archive/force-delete-all', [ClassRoomController::class, 'forceDeleteAllArchive'])->name('class-rooms.forceDeleteAllArchive');
    Route::post('/class-rooms/{id}/restore', [ClassRoomController::class, 'restore'])->name('class-rooms.restore');
    Route::delete('/class-rooms/{id}/force-delete', [ClassRoomController::class, 'forceDelete'])->name('class-rooms.forceDelete');

    // 📁 مسارات الأرشيف والحذف النهائي والاستعادة الخاصة بالمدفوعات والسندات (Soft Delete)
    Route::get('/payments/archive', [PaymentController::class, 'archive'])->name('payments.archive');
    Route::delete('/payments/archive/force-delete-all', [PaymentController::class, 'forceDeleteAllArchive'])->name('payments.archive.force-delete-all');
    Route::patch('/payments/{id}/restore', [PaymentController::class, 'restore'])->name('payments.restore');
    Route::delete('/payments/{id}/force-delete', [PaymentController::class, 'forceDelete'])->name('payments.forceDelete');

    // 🟢 مسارات استيراد وتصدير الطلاب Excel
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');

    // 🟢 مسارات استيراد وتصدير المحاضرين Excel (يجب وضعها قبل الـ resource)
    Route::get('/teachers/export', [TeacherController::class, 'export'])->name('teachers.export');
    Route::post('/teachers/import', [TeacherController::class, 'import'])->name('teachers.import');

    // مسارات إدارة النظام
    Route::resource('courses', CourseController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('students', StudentController::class);
    Route::resource('enrollments', EnrollmentController::class);
    Route::resource('course-classes', CourseClassController::class);
    Route::resource('class-rooms', ClassRoomController::class);

    // مسارات الحضور والغياب
    Route::get('/attendances/report', [AttendanceController::class, 'report'])->name('attendances.report');
    Route::resource('attendances', AttendanceController::class);

    // مسارات المدفوعات والأقساط
    Route::get('/payments/print/{payment}', [PaymentController::class, 'print'])->name('payments.print');
    Route::resource('payments', PaymentController::class);

    // مسارات الإشعارات
    Route::post('/notifications/{id}/mark-as-read', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back();
    })->name('notifications.markAsRead');

    Route::post('/notifications/mark-all-as-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAllAsRead');

});

// ----------------------------------------------------
// مسارات المصادقة وتسجيل الدخول وإنشاء الحساب (Auth Routes)
// ----------------------------------------------------

// مسار عرض صفحة تسجيل الدخول
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// مسار معالجة بيانات الدخول
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// مسار عرض صفحة إنشاء حساب جديد للزبائن
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

// مسار معالجة بيانات إنشاء الحساب الجديد
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// مسار تسجيل الخروج
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');