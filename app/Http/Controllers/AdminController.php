<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Exam;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // 1. Dashboard Admin
    public function index()
    {
        $totalUsers = User::count();
        $totalExams = Exam::count();
        $pendingExams = Exam::where('is_published', 0)->count();
        
        return view('admin.dashboard', compact('totalUsers', 'totalExams', 'pendingExams'));
    }

    // --- QUẢN LÝ NGƯỜI DÙNG ---
    
    // Danh sách người dùng
    public function listUsers()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Khóa/Mở khóa tài khoản
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        // Đảo ngược trạng thái: Nếu đang mở thì khóa, đang khóa thì mở
        $user->is_active = !$user->is_active; 
        $user->save();
        
        $status = $user->is_active ? 'Đã mở khóa' : 'Đã khóa';
        return back()->with('success', "Tài khoản {$user->name} {$status}.");
    }

    // Cập nhật quyền (Ví dụ: Thăng cấp Student -> Teacher)
    public function changeRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();
        
        return back()->with('success', "Đã thay đổi quyền của {$user->name} thành {$request->role}.");
    }

    // --- QUẢN LÝ ĐỀ THI ---

    // Danh sách đề thi
    public function listExams()
    {
        // Lấy tất cả đề thi kèm thông tin người tạo
        $exams = Exam::with('creator')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.exams.index', compact('exams'));
    }

    // Duyệt/Hủy duyệt đề thi
    public function toggleExamStatus($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->is_published = !$exam->is_published;
        $exam->save();

        $status = $exam->is_published ? 'Đã duyệt' : 'Đã hủy duyệt';
        return back()->with('success', "Đề thi {$status}.");
    }
    
    // Xóa đề thi (Nếu vi phạm hoặc rác)
    public function deleteExam($id)
    {
        Exam::destroy($id);
        return back()->with('success', 'Đã xóa đề thi vĩnh viễn.');
    }
}