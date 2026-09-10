<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // حذف المفتاح الأجنبي والعمود القديم نهائياً
            $table->dropForeign(['course_class_id']);
            $table->dropColumn('course_class_id');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->unsignedBigInteger('course_class_id')->nullable();
        });
    }
};