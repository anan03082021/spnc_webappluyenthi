<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['title', 'duration', 'difficulty', 'created_by', 'is_published'];

public function questions() {
    return $this->hasMany(Question::class); // Một đề có nhiều câu hỏi
}

public function creator() {
    return $this->belongsTo(User::class, 'created_by'); // Người tạo đề
}
}
