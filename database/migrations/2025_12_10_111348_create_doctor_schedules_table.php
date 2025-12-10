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
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            // ربط بالدكتور
            $table->foreignId('doctor_profile_id')
                ->constrained('doctor_profiles')
                ->cascadeOnDelete();

            // أيام الأسبوع (0 = الأحد ... 6 = السبت)
            // بدل عمود date اللي كان عندك
            $table->unsignedTinyInteger('day_of_week');

            // وقت بداية ونهاية الشفت
            $table->time('start_time');
            $table->time('end_time');

            // مدة الكشف (عشان تعرف تقسم الوقت لشرائح)
            // وممكن تشيله من هنا لو انت حاطه في بروفايل الدكتور (حسب تفضيلك)
            $table->integer('avg_consultation_time')->default(30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctodoctor_schedules');
    }
};
