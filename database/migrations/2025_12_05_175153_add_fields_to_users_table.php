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
        Schema::table('users', function (Blueprint $table) {
            // 'admin', 'teacher', 'student'
        $table->string('role')->default('student'); 
        $table->string('student_code')->nullable()->unique(); // Mã học sinh/GV
        $table->string('class_name')->nullable(); // Lớp (VD: 12A1)
        $table->string('phone')->nullable();
        $table->string('avatar')->nullable();
        $table->boolean('is_active')->default(true); // Trạng thái khóa/mở
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
