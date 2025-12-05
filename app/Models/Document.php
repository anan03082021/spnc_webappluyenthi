<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    // QUAN TRỌNG: Phải khai báo các cột được phép thêm dữ liệu
    protected $fillable = [
        'title', 
        'file_path', 
        'category_id', 
        'uploaded_by'
    ];

    // Quan hệ với bảng Categories
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Quan hệ với User (người upload)
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}