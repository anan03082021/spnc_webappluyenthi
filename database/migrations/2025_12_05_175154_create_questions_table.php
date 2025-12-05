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
        Schema::create('questions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
        $table->text('content'); // Nội dung câu hỏi
        $table->string('option_a');
        $table->string('option_b');
        $table->string('option_c');
        $table->string('option_d');
        $table->string('correct_answer'); // Giá trị: 'A', 'B', 'C', hoặc 'D'
        $table->text('explanation')->nullable(); // Giải thích chi tiết
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
