<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Trang chủ chung
Route::get('/', function () {
    return view('welcome');
});

// KHU VỰC DIỄN ĐÀN (Dùng chung cho cả Teacher & Student)
Route::middleware(['auth'])->prefix('forum')->name('forum.')->group(function () {
    Route::get('/', [App\Http\Controllers\ForumController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ForumController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\ForumController::class, 'store'])->name('store');
    Route::get('/post/{id}', [App\Http\Controllers\ForumController::class, 'show'])->name('show');
    Route::post('/post/{id}/reply', [App\Http\Controllers\ForumController::class, 'reply'])->name('reply');
});

// HỌC SINH (Student)
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    // Dashboard: Danh sách đề thi
    Route::get('/dashboard', [App\Http\Controllers\StudentExamController::class, 'index'])->name('dashboard');
    
    // Làm bài thi
    Route::get('/exam/{id}', [App\Http\Controllers\StudentExamController::class, 'show'])->name('exams.show');
    Route::post('/exam/{id}', [App\Http\Controllers\StudentExamController::class, 'store'])->name('exams.store');
    
    // Xem kết quả
    Route::get('/result/{id}', [App\Http\Controllers\StudentExamController::class, 'result'])->name('exams.result');

    // Xem tài liệu
    Route::get('/documents', [App\Http\Controllers\DocumentController::class, 'indexStudent'])->name('documents.index');
});

// GIÁO VIÊN (Teacher)
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('teacher.dashboard');
    })->name('dashboard');

    // Quản lý đề thi
    Route::get('/exams', [App\Http\Controllers\ExamController::class, 'index'])->name('exams.index'); // Danh sách
    Route::get('/exams/create', [App\Http\Controllers\ExamController::class, 'create'])->name('exams.create'); // Form tạo mới
    Route::post('/exams', [App\Http\Controllers\ExamController::class, 'store'])->name('exams.store'); // Lưu dữ liệu

    // Quản lý tài liệu
    Route::get('/documents', [App\Http\Controllers\DocumentController::class, 'indexTeacher'])->name('documents.index');
    Route::get('/documents/create', [App\Http\Controllers\DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [App\Http\Controllers\DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{id}', [App\Http\Controllers\DocumentController::class, 'destroy'])->name('documents.destroy');

    // BỔ SUNG CÁC ROUTE SAU:
    Route::get('/exams/{id}/edit', [App\Http\Controllers\ExamController::class, 'edit'])->name('exams.edit'); // Form sửa
    Route::put('/exams/{id}', [App\Http\Controllers\ExamController::class, 'update'])->name('exams.update'); // Lưu sửa
    Route::delete('/exams/{id}', [App\Http\Controllers\ExamController::class, 'destroy'])->name('exams.destroy'); // Xóa
});

// ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');

    // Quản lý Users
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'listUsers'])->name('users.index');
    Route::post('/users/{id}/status', [App\Http\Controllers\AdminController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::post('/users/{id}/role', [App\Http\Controllers\AdminController::class, 'changeRole'])->name('users.role');

    // Quản lý Đề thi (Ngân hàng đề)
    Route::get('/exams', [App\Http\Controllers\AdminController::class, 'listExams'])->name('exams.index');
    Route::post('/exams/{id}/toggle', [App\Http\Controllers\AdminController::class, 'toggleExamStatus'])->name('exams.toggle');
    Route::delete('/exams/{id}', [App\Http\Controllers\AdminController::class, 'deleteExam'])->name('exams.delete');

    // QUẢN LÝ DIỄN ĐÀN
    Route::get('/forum', [App\Http\Controllers\AdminController::class, 'listForumPosts'])->name('forum.index');
    Route::delete('/forum/{id}', [App\Http\Controllers\AdminController::class, 'deleteForumPost'])->name('forum.delete');

    // SỬA/THÊM ROUTE DIỄN ĐÀN:
    Route::get('/forum', [App\Http\Controllers\AdminController::class, 'listForumPosts'])->name('forum.index');
    Route::delete('/forum/post/{id}', [App\Http\Controllers\AdminController::class, 'deleteForumPost'])->name('forum.delete'); // Xóa bài gốc
    
    // MỚI: Xem chi tiết & Xóa comment
    Route::get('/forum/{id}', [App\Http\Controllers\AdminController::class, 'showForumPost'])->name('forum.show');
    Route::delete('/forum/comment/{id}', [App\Http\Controllers\AdminController::class, 'deleteForumComment'])->name('forum.delete_comment');
});

// Chuyển hướng sau khi đăng nhập (Logic tùy chỉnh)
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role == 'admin') return redirect()->route('admin.dashboard');
    if ($user->role == 'teacher') return redirect()->route('teacher.dashboard');
    return redirect()->route('student.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
