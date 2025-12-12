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
        // scope: 'public' (chung) hoặc 'teacher' (nội bộ)
        $table->string('scope')->default('public')->after('title'); 
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            //
        });
    }
};
