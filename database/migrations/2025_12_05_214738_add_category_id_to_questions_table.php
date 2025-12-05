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
    Schema::table('questions', function (Blueprint $table) {
        // Liên kết câu hỏi với chủ đề (Nullable vì các câu hỏi cũ có thể chưa có)
        $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            //
        });
    }
};
