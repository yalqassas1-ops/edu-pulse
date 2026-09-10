<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // نتحقق إذا كان العمود غير موجود لنضيفه ونلغي أي تعارض
            if (!Schema::hasColumn('enrollments', 'course_id')) {
                $table->unsignedBigInteger('course_id')->nullable()->after('student_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('course_id');
        });
    }
};