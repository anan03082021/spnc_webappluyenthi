<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'exam_id', 
        'score', 
        'completion_time', 
        'student_answers',  // Cột mới
        'selected_answers', // Cột cũ
        'submitted_at'
    ];

    protected $casts = [
        'student_answers' => 'array',
        'selected_answers' => 'array', // Cast cả 2 cột sang mảng
        'submitted_at' => 'datetime',
        'score' => 'float',
    ];

    public function exam() {
        return $this->belongsTo(Exam::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}