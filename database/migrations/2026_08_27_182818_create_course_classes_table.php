<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_room_id')->constrained()->onDelete('cascade');
            $table->string('class_number');
            $table->date('start_date');
            $table->date('end_date');
            
            // --- التعديل هنا: تفصيل الموعد بدلاً من النص المفتوح ---
            $table->json('days')->nullable(); // لحفظ أيام الأسبوع مثل: ["Sat", "Tue"]
            $table->time('start_time')->nullable(); // وقت بداية المحاضرة مثل: 14:00
            $table->time('end_time')->nullable();   // وقت نهاية المحاضرة مثل: 16:00
            // --------------------------------------------------

            $table->enum('status', ['upcoming', 'ongoing', 'completed'])->default('upcoming');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_classes');
    }
};