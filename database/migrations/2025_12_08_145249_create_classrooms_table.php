<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('classrooms', function (Blueprint $table) {
        $table->id();
        $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade'); // Liên kết với giáo viên
        $table->string('name'); // Tên lớp (VD: 12A1)
        $table->string('code')->unique(); // Mã lớp (VD: MATH12)
        $table->string('academic_year'); // Năm học
        $table->timestamps();
    });
    
    // Tạo thêm bảng trung gian để lưu danh sách học sinh trong lớp (Quan hệ Many-to-Many)
    Schema::create('classroom_user', function (Blueprint $table) {
        $table->id();
        $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Học sinh
        $table->string('status')->default('pending'); // pending (chờ duyệt), approved (đã vào), blocked
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
