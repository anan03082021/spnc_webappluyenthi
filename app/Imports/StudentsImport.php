<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Để bỏ qua dòng tiêu đề

class StudentsImport implements ToModel, WithHeadingRow
{
    protected $classId;

    // Nhận ID lớp từ Controller truyền sang
    public function __construct($classId)
    {
        $this->classId = $classId;
    }

    /**
    * Map dữ liệu từng dòng Excel vào Model User
    */
    public function model(array $row)
    {

        // 1. Kiểm tra dữ liệu dòng (nếu thiếu email hoặc tên thì bỏ qua)
        if (!isset($row['email']) || !isset($row['ho_va_ten'])) {
            return null;
        }

        // 2. Mật khẩu mặc định: 12345678
        $password = Hash::make('12345678'); 

        // 3. Tạo hoặc Cập nhật User
        // Logic: Nếu Email đã có -> Cập nhật vào lớp mới. Nếu chưa có -> Tạo tài khoản mới.
        return User::updateOrCreate(
            ['email' => $row['email']], // Điều kiện tìm kiếm (Email là duy nhất)
            [
                'name' => $row['ho_va_ten'],
                'password' => $password,
                'role' => 'student', // Bắt buộc set role là học sinh
                'classroom_id' => $this->classId, // Gán vào lớp hiện tại
                'is_active' => 1
            ]
        );
    }
}