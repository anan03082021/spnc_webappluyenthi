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
    Schema::create('forum_posts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('title')->nullable(); // Null nếu là comment trả lời
        $table->text('content');
        // Nếu null là bài gốc, nếu có ID là comment của bài đó
        $table->foreignId('parent_id')->nullable()->constrained('forum_posts')->onDelete('cascade'); 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
    }
};
