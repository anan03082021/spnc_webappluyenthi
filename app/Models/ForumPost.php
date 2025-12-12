<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'code_snippet',        // Code Pascal/C++
        'parent_id',           // ID bài cha (nếu là comment)
        'related_exam_id',     // ID đề thi liên quan
        'related_question_no', // Câu số mấy
        'scope',               // 'public' hoặc 'teacher'
        'is_solved',           // Đã giải quyết chưa
        'is_accepted',         // Là đáp án đúng
        'is_pinned',           // Ghim
        'views'                // Lượt xem
    ];

    // 1. Quan hệ: Bài viết thuộc về 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 2. Quan hệ: Bài viết con (Trả lời)
    public function replies()
    {
        return $this->hasMany(ForumPost::class, 'parent_id');
    }

    // 3. Quan hệ: Bài viết cha (Nếu đây là câu trả lời)
    public function parent()
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    // 4. QUAN TRỌNG: Mối quan hệ với Đề thi (Sửa lỗi RelationNotFound)
    public function exam()
    {
        // Liên kết khóa ngoại 'related_exam_id' tới model Exam
        return $this->belongsTo(Exam::class, 'related_exam_id');
    }
}