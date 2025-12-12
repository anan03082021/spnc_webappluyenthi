<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['exam_id',
    'content',
    'image',        // <--- Đảm bảo có cái này
    'type',         // <--- QUAN TRỌNG: Phải có dòng này mới lưu được loại câu hỏi
    'level',
    'category_id',
    'option_a',
    'option_b',
    'option_c',
    'option_d',
    'correct_answer',
    'explanation',
    'order'];
    // Thêm quan hệ với Category
public function category()
{
    return $this->belongsTo(Category::class);
}
}
