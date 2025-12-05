<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Tạo Admin
        User::create([
            'name' => 'Quản trị viên',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 2. Tạo Giáo viên
        User::create([
            'name' => 'Cô Giáo Thảo',
            'email' => 'teacher@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'teacher',
            'phone' => '0909000111',
            'is_active' => true,
        ]);

        // 3. Tạo Học sinh
        User::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'student@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'student',
            'class_name' => '12A1',
            'is_active' => true,
        ]);
    }
}