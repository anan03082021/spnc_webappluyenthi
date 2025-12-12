<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Tạo Admin (Nếu chưa có thì tạo, có rồi thì bỏ qua)
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'], // Điều kiện tìm
            [
                'name' => 'Quản Trị Viên',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'is_active' => 1
            ]
        );

        // 2. Tạo Giáo viên (Để đảm bảo luôn có ít nhất 1 GV cho bước tạo lớp học)
        User::firstOrCreate(
            ['email' => 'teacher@gmail.com'],
            [
                'name' => 'Cô Giáo Thảo',
                'password' => Hash::make('12345678'),
                'role' => 'teacher',
                'is_active' => 1
            ]
        );

        // 3. Tạo Học sinh mẫu
        User::firstOrCreate(
            ['email' => 'student@gmail.com'],
            [
                'name' => 'Nguyễn Văn Học',
                'password' => Hash::make('12345678'),
                'role' => 'student',
                'is_active' => 1
            ]
        );
    }
}