<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'file_path', 'file_type', 'file_size'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Hàm tiện ích để lấy Icon dựa trên đuôi file
    public function getIconAttribute()
    {
        $icons = [
            'pdf' => 'fa-file-pdf text-danger',
            'doc' => 'fa-file-word text-primary',
            'docx' => 'fa-file-word text-primary',
            'xls' => 'fa-file-excel text-success',
            'xlsx' => 'fa-file-excel text-success',
            'ppt' => 'fa-file-powerpoint text-warning',
            'pptx' => 'fa-file-powerpoint text-warning',
            'zip' => 'fa-file-zipper text-secondary',
            'rar' => 'fa-file-zipper text-secondary',
        ];

        return $icons[$this->file_type] ?? 'fa-file text-secondary';
    }
}