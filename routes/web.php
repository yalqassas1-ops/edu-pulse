<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// الصفحة الرئيسية ولوحة التحكم
Route::get('/', function () {
    return view('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

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
Route::resource('payments', PaymentController::class)->except(['create', 'edit', 'show', 'update']);