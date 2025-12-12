<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    // 1. Xem diễn đàn chung (Học sinh + GV)
    public function index(Request $request)
    {
        $query = ForumPost::where('scope', 'public')
                    ->with('user') // Lấy thông tin người đăng
                    ->withCount('replies') // Đếm số bình luận (để hiện ra View)
                    ->orderBy('is_pinned', 'desc') // Bài ghim lên đầu
                    ->orderBy('created_at', 'desc');

        // Thêm bộ lọc tìm kiếm (nếu cần)
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->paginate(10);
                    
        return view('forum.index', [
            'posts' => $posts,
            'isInternal' => false 
        ]);
    }

    // 2. Xem diễn đàn nội bộ (Góc chuyên môn - Chỉ GV)
    public function teacherIndex()
    {
        // Bảo mật lớp 2: Chặn nếu không phải giáo viên
        if (Auth::user()->role !== 'teacher' && Auth::user()->role !== 'admin') {
            abort(403, 'Khu vực cấm học sinh.');
        }

        $posts = ForumPost::where('scope', 'teacher')
                    ->with('user')
                    ->withCount('replies')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        // Bạn có thể trả về view 'forum.internal' nếu muốn giao diện màu cam riêng biệt
        // Hoặc dùng chung 'forum.index' với biến isInternal = true
        return view('forum.index', [
            'posts' => $posts,
            'isInternal' => true 
        ]);
    }

    // 3. Form tạo câu hỏi mới
    public function create()
    {
        return view('forum.create');
    }

    // 4. LƯU BÀI VIẾT (Đã cập nhật để lưu Code và Ngữ cảnh đề thi)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            // Các trường không bắt buộc
            'code_snippet' => 'nullable|string',
            'scope' => 'nullable|in:public,teacher',
        ]);

        // Xử lý Scope: Chỉ GV mới được đăng bài 'teacher'
        $scope = $request->input('scope', 'public');
        if (auth()->user()->role !== 'teacher') {
            $scope = 'public';
        }

        ForumPost::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'code_snippet' => $request->code_snippet, // [MỚI] Lưu đoạn code
            
            // [MỚI] Lưu ngữ cảnh (Nếu bấm từ trang đề thi)
            'related_exam_id' => $request->input('exam_id'), 
            'related_question_no' => $request->input('question_no'),
            
            'scope' => $scope
        ]);

        return back()->with('success', 'Đã đăng thảo luận thành công!');
    }

    // 5. Xem chi tiết bài viết (Kèm bảo mật)
    public function show($id)
    {
        $post = ForumPost::with(['user', 'exam', 'replies.user'])->findOrFail($id);

        // [BẢO MẬT] Chặn học sinh xem bài nội bộ
        if ($post->scope == 'teacher' && Auth::user()->role == 'student') {
            abort(403, 'Bạn không có quyền xem bài viết này.');
        }

        // Tăng lượt xem
        $post->increment('views');

        return view('forum.show', compact('post'));
    }

    // 6. Gửi bình luận trả lời
    public function reply(Request $request, $id)
    {
        $request->validate(['content' => 'required']);

        $parentPost = ForumPost::findOrFail($id);

        ForumPost::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $id,
            'scope' => $parentPost->scope
        ]);

        return back()->with('success', 'Đã gửi bình luận.');
    }

    // 7. [MỚI] Giáo viên duyệt đáp án đúng
    public function approveReply($replyId)
    {
        // Chỉ giáo viên mới được duyệt
        if (Auth::user()->role !== 'teacher') {
            abort(403);
        }

        $reply = ForumPost::findOrFail($replyId);
        $parentPost = $reply->parent; // Lấy bài gốc

        // Đánh dấu bài gốc là "Đã giải quyết"
        $parentPost->is_solved = true;
        $parentPost->save();

        // Đánh dấu bình luận này là "Accepted"
        // Trước tiên bỏ tick các bài cũ (nếu muốn chỉ 1 đáp án đúng)
        ForumPost::where('parent_id', $parentPost->id)->update(['is_accepted' => false]);
        
        $reply->is_accepted = true;
        $reply->save();

        return back()->with('success', 'Đã xác nhận đáp án chính xác!');
    }
    // ... (Các hàm cũ giữ nguyên) ...

    // 7. Hiển thị form chỉnh sửa
    public function edit($id)
    {
        $post = ForumPost::findOrFail($id);

        // Bảo mật: Chỉ chủ bài viết hoặc Admin mới được sửa
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền sửa bài viết này.');
        }

        return view('forum.edit', compact('post'));
    }

    // 8. Thực hiện cập nhật
    public function update(Request $request, $id)
    {
        $post = ForumPost::findOrFail($id);

        // Bảo mật
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền sửa bài viết này.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'code_snippet' => 'nullable|string',
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'code_snippet' => $request->code_snippet,
        ]);

        return redirect()->route('forum.show', $post->id)->with('success', 'Đã cập nhật bài viết.');
    }

    // 9. Xóa bài viết
    public function destroy($id)
    {
        $post = ForumPost::findOrFail($id);

        // Bảo mật
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền xóa bài viết này.');
        }

        // Kiểm tra xem bài viết thuộc khu vực nào để redirect về đúng chỗ
        $redirectRoute = ($post->scope == 'teacher') ? 'teacher.forum.internal' : 'forum.index';

        $post->delete();

        return redirect()->route($redirectRoute)->with('success', 'Đã xóa bài viết.');
    }
}