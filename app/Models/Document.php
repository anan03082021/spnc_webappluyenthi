<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    // 1. Thêm 'category_id' vào đây để cho phép lưu dữ liệu
    protected $fillable = [
        'user_id', 
        'category_id', // <--- Bổ sung cột này
        'title', 
        'file_path', 
        'file_type', 
        'file_size'
    ];

    // 2. Quan hệ với User (Người đăng) - Chỉ giữ 1 hàm duy nhất
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 3. Quan hệ với Category (Danh mục) - Để sửa lỗi RelationNotFound
    public function category()
    {
        // Trả về quan hệ, nếu category_id null thì trả về null
        return $this->belongsTo(Category::class);
    }
    
    // 4. Hàm tiện ích lấy Icon
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