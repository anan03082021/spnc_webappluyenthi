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
    Schema::table('forum_posts', function (Blueprint $table) {
        // Thêm các cột còn thiếu nếu chưa có
        if (!Schema::hasColumn('forum_posts', 'is_pinned')) {
            $table->boolean('is_pinned')->default(false);
        }
        if (!Schema::hasColumn('forum_posts', 'scope')) {
            $table->string('scope')->default('public');
        }
        if (!Schema::hasColumn('forum_posts', 'is_solved')) {
            $table->boolean('is_solved')->default(false);
        }
        if (!Schema::hasColumn('forum_posts', 'is_accepted')) {
            $table->boolean('is_accepted')->default(false);
        }
        if (!Schema::hasColumn('forum_posts', 'code_snippet')) {
            $table->text('code_snippet')->nullable();
        }
        if (!Schema::hasColumn('forum_posts', 'related_exam_id')) {
            $table->foreignId('related_exam_id')->nullable()->constrained('exams')->onDelete('set null');
        }
        if (!Schema::hasColumn('forum_posts', 'related_question_no')) {
            $table->integer('related_question_no')->nullable();
        }
        if (!Schema::hasColumn('forum_posts', 'views')) {
            $table->unsignedInteger('views')->default(0);
        }
    });
}
    public function down(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            //
        });
    }
};
