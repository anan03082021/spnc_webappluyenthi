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

    // --- QUẢN LÝ DIỄN ĐÀN ---

    // 1. Danh sách bài viết (Chỉ lấy bài gốc, không lấy comment)
    public function listForumPosts()
    {
        $posts = \App\Models\ForumPost::whereNull('parent_id')
                    ->with(['user'])
                    ->withCount('replies') // Đếm số bình luận
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                    
        return view('admin.forum.index', compact('posts'));
    }

    // 2. Xóa bài viết (Sẽ tự động xóa hết bình luận con nhờ onDelete cascade trong database)
    public function deleteForumPost($id)
    {
        \App\Models\ForumPost::destroy($id);
        return back()->with('success', 'Đã xóa bài viết và các thảo luận liên quan.');
    }
    // 3. Xem chi tiết bài viết và danh sách bình luận (Giao diện Admin)
    public function showForumPost($id)
    {
        $post = \App\Models\ForumPost::with(['user', 'replies.user'])->findOrFail($id);
        return view('admin.forum.show', compact('post'));
    }

    // 4. Xóa một bình luận cụ thể
    public function deleteForumComment($id)
    {
        $comment = \App\Models\ForumPost::findOrFail($id);
        
        // Kiểm tra logic: Đảm bảo đây là comment (có parent_id) chứ không phải bài gốc
        if($comment->parent_id) {
            $comment->delete();
            return back()->with('success', 'Đã xóa bình luận.');
        }
        
        return back()->with('error', 'Không thể xóa bài gốc tại đây.');
    }
}