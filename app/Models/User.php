<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'student_code',
    'class_name', // Phải có
    'phone',      // Phải có
    'avatar',
    'classroom_id',     // Phải có
];
// Quan hệ: Một người dùng có nhiều kết quả thi
public function results() {
    return $this->hasMany(Result::class);
}

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // Thêm vào trong class User
    public function bookmarks()
    {
        // Quan hệ nhiều-nhiều với bảng exams thông qua bảng trung gian exam_user
        return $this->belongsToMany(Exam::class, 'exam_user', 'user_id', 'exam_id')->withTimestamps();
    }

public function classroom() {
    return $this->belongsTo(Classroom::class, 'classroom_id');
}
}
