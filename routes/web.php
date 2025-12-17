<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. TRANG CHỦ
Route::get('/', function () {
    return view('welcome');
});

// 2. CHUYỂN HƯỚNG DASHBOARD
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role == 'admin') return redirect()->route('admin.dashboard');
    if ($user->role == 'teacher') return redirect()->route('teacher.dashboard');
    return redirect()->route('student.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. PROFILE
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// 4. DIỄN ĐÀN CHUNG (Dành cho cả GV & HS)
Route::middleware(['auth'])->prefix('forum')->name('forum.')->group(function () {
    Route::get('/', [App\Http\Controllers\ForumController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ForumController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\ForumController::class, 'store'])->name('store');
    
    // Xem chi tiết & Bình luận
    Route::get('/post/{id}', [App\Http\Controllers\ForumController::class, 'show'])->name('show');
    Route::post('/post/{id}/reply', [App\Http\Controllers\ForumController::class, 'reply'])->name('reply');

    // Sửa & Xóa bài viết
    Route::get('/post/{id}/edit', [App\Http\Controllers\ForumController::class, 'edit'])->name('edit');
    Route::put('/post/{id}', [App\Http\Controllers\ForumController::class, 'update'])->name('update');
    Route::delete('/post/{id}', [App\Http\Controllers\ForumController::class, 'destroy'])->name('destroy');
});

// ==========================================
// 5. HỌC SINH (Student)
// ==========================================
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\StudentExamController::class, 'index'])->name('dashboard');

    // Thi cử
    Route::get('/exam/{id}', [App\Http\Controllers\StudentExamController::class, 'show'])->name('exams.show');
    Route::post('/exam/{id}', [App\Http\Controllers\StudentExamController::class, 'store'])->name('exams.store');
    Route::get('/result/{id}', [App\Http\Controllers\StudentExamController::class, 'result'])->name('exams.result');
    Route::get('/history', [App\Http\Controllers\StudentExamController::class, 'history'])->name('exams.history');
    Route::post('/exam/{id}/bookmark', [App\Http\Controllers\StudentExamController::class, 'toggleBookmark'])->name('exams.bookmark');
    Route::get('/explore', [App\Http\Controllers\StudentExamController::class, 'explore'])->name('exams.explore');
    Route::get('/progress', [App\Http\Controllers\StudentExamController::class, 'progress'])->name('progress');

    // --- TÀI LIỆU HỌC SINH (Đã sửa để khớp với controller) ---
    Route::get('/documents', [App\Http\Controllers\DocumentController::class, 'indexStudent'])->name('documents.index');
    Route::get('/documents/download/{id}', [App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');
});

// ==========================================
// 6. GIÁO VIÊN (Teacher)
// ==========================================
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\TeacherController::class, 'dashboard'])->name('dashboard');

    // Quản lý Lớp học
    Route::post('/classes/{classId}/add-student', [App\Http\Controllers\TeacherClassController::class, 'addStudent'])->name('classes.add_student');
    Route::post('/classes/{classId}/import', [App\Http\Controllers\TeacherClassController::class, 'importStudents'])->name('classes.import');
    Route::delete('/classes/{classId}/student/{studentId}', [App\Http\Controllers\TeacherClassController::class, 'removeStudent'])->name('classes.remove_student');
    Route::resource('classes', App\Http\Controllers\TeacherClassController::class);

    // Quản lý Đề thi (Sửa lỗi trùng lặp route exams.show)
    Route::get('exams/create-quick', [App\Http\Controllers\ExamController::class, 'createQuick'])->name('exams.create_quick');
    Route::post('exams/store-quick', [App\Http\Controllers\ExamController::class, 'storeQuick'])->name('exams.store_quick');
    Route::post('exams/{id}/toggle', [App\Http\Controllers\ExamController::class, 'toggleStatus'])->name('exams.toggle');
    
    // Route resource exams phải đặt SAU các route exams/... cụ thể
    Route::resource('exams', App\Http\Controllers\ExamController::class); 

    // Quản lý Tài liệu
    Route::get('/documents', [App\Http\Controllers\DocumentController::class, 'indexTeacher'])->name('documents.index');
    Route::get('/documents/download/{id}', [App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');
    Route::resource('documents', App\Http\Controllers\DocumentController::class)->except(['show', 'edit', 'update', 'index']);

    // Diễn đàn Nội bộ
    Route::get('/professional-corner', [App\Http\Controllers\ForumController::class, 'teacherIndex'])->name('forum.internal');
    Route::post('/forum/reply/{id}/approve', [App\Http\Controllers\ForumController::class, 'approveReply'])->name('forum.approve_reply');
});

// ==========================================
// 7. ADMIN (Quản trị viên)
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');

    // Quản lý User
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'listUsers'])->name('users.index');
    Route::post('/users/{id}/status', [App\Http\Controllers\AdminController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::post('/users/{id}/role', [App\Http\Controllers\AdminController::class, 'changeRole'])->name('users.role');

    // Quản lý Đề thi
    Route::get('/exams', [App\Http\Controllers\AdminController::class, 'listExams'])->name('exams.index');
    Route::post('/exams/{id}/toggle', [App\Http\Controllers\AdminController::class, 'toggleExamStatus'])->name('exams.toggle');
    Route::delete('/exams/{id}', [App\Http\Controllers\AdminController::class, 'deleteExam'])->name('exams.delete');

    // Quản lý Diễn đàn
    Route::get('/forum', [App\Http\Controllers\AdminController::class, 'listForumPosts'])->name('forum.index');
    Route::get('/forum/{id}', [App\Http\Controllers\AdminController::class, 'showForumPost'])->name('forum.show');
    Route::delete('/forum/post/{id}', [App\Http\Controllers\AdminController::class, 'deleteForumPost'])->name('forum.delete');
    Route::delete('/forum/comment/{id}', [App\Http\Controllers\AdminController::class, 'deleteForumComment'])->name('forum.delete_comment');

    // Danh mục
    Route::resource('categories', App\Http\Controllers\CategoryController::class)->except(['create', 'edit', 'show']);
});