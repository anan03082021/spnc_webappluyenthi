<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = ['user_id', 'exam_id', 'score', 'completion_time', 'selected_answers', 'submitted_at'];

protected $casts = [
    'selected_answers' => 'array', // Tự động chuyển JSON sang mảng khi lấy dữ liệu
];

public function exam() {
    return $this->belongsTo(Exam::class);
}

public function user() {
    return $this->belongsTo(User::class);
}
}
