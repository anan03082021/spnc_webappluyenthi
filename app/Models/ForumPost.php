<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'content', 'parent_id'];

    // Người đăng bài
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Các bình luận con (Replies)
    public function replies()
    {
        return $this->hasMany(ForumPost::class, 'parent_id')->orderBy('created_at', 'asc'); // Cũ nhất xếp trên
    }
}