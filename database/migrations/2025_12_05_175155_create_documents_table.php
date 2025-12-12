<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kiểm tra nếu bảng đã tồn tại thì xóa đi tạo lại để cập nhật cấu trúc mới
        Schema::dropIfExists('documents');

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            
            // 1. Người đăng (Giáo viên)
            // Đổi từ 'uploaded_by' thành 'user_id' để Eloquent tự hiểu quan hệ
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            $table->string('title');     // Tên hiển thị (VD: Đề cương Toán)
            $table->string('file_path'); // Đường dẫn file (documents/filename.pdf)
            
            // 2. Các cột MỚI cần thêm để phục vụ giao diện
            $table->string('file_type')->nullable(); // Lưu đuôi file (pdf, docx...) để hiện Icon
            $table->decimal('file_size', 8, 2)->nullable(); // Lưu dung lượng (MB) để tính toán
            
            // Tạm thời bỏ category_id nếu chưa dùng đến để tránh lỗi khóa ngoại
            // $table->foreignId('category_id')->nullable()->constrained('categories'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};