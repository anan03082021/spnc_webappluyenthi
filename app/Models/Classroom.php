<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'academic_year', 'teacher_id'
    ];

    // 1. Quan hệ: Lớp thuộc về 1 Giáo viên
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // 2. Quan hệ: Lớp có nhiều Học sinh (qua bảng trung gian)
    public function students()
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }
    
    // Quan hệ: Lấy các yêu cầu đang chờ duyệt
    public function pendingStudents()
    {
        return $this->belongsToMany(User::class, 'classroom_user')
                    ->wherePivot('status', 'pending');
    }

    // --- ACCESSORS (Tạo thuộc tính ảo để khớp với View cũ) ---

    // Thuộc tính: $class->students_count
    public function getStudentsCountAttribute()
    {
        return $this->students()->count();
    }

    // Thuộc tính: $class->pending_requests
    public function getPendingRequestsAttribute()
    {
        return $this->pendingStudents()->count();
    }

    // Thuộc tính: $class->active_exams (Tạm thời trả về 0 nếu chưa có bảng ExamClass)
    public function getActiveExamsAttribute()
    {
        // Sau này bạn sẽ query bảng exam_classroom tại đây
        return 0; 
    }

    // Thuộc tính: $class->total_attempts (Tổng lượt làm bài của lớp này)
    public function getTotalAttemptsAttribute()
    {
        // Logic đếm tổng lượt thi của các học sinh trong lớp này
        return 0; // Tạm thời để 0
    }
}