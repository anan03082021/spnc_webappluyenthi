<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() :void
{
    Schema::create('exams', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->integer('duration'); // Thời gian làm bài (phút)
        $table->integer('total_questions')->default(0);
        // Dễ, Trung bình, Khó
        $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium'); 
        // Liên kết với bảng users (người tạo)
        $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); 
        $table->boolean('is_published')->default(false); // Trạng thái duyệt
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
