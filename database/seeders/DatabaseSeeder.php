<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classroom;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Chạy UserSeeder trước để đảm bảo có users trong hệ thống
        $this->call([
            UserSeeder::class,
        ]);

        // 2. Tìm một tài khoản Giáo viên trong DB
        $teacher = User::where('role', 'teacher')->first();

        // Nếu tìm thấy Giáo viên thì mới tạo lớp học cho họ
        if ($teacher) {
            Classroom::factory()->count(5)->create([
                'teacher_id' => $teacher->id, // Lấy ID thật của giáo viên vừa tìm được
            ]);
            $this->command->info('Đã tạo 5 lớp học cho giáo viên: ' . $teacher->name);
        } else {
            $this->command->warn('Không tìm thấy Giáo viên nào để tạo lớp học!');
        }
    }
}