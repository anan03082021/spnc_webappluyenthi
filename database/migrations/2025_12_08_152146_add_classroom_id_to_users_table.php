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
    Schema::table('users', function (Blueprint $table) {
        // Thêm khóa ngoại trỏ về bảng classrooms
        // nullable() vì Admin và Teacher không thuộc lớp nào
        $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['classroom_id']);
        $table->dropColumn('classroom_id');
    });
}
};
