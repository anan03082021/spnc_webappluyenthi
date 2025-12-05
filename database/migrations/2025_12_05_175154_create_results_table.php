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
        Schema::create('results', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
        $table->float('score'); // Điểm số
        $table->integer('completion_time'); // Thời gian thực tế đã làm (giây/phút)
        $table->json('selected_answers')->nullable(); // Lưu đáp án HS đã chọn dạng JSON { "1":"A", "2":"C" }
        $table->timestamp('submitted_at');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
