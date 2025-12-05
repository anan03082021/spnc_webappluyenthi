<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    // 1. Danh sách các chủ đề thảo luận
    public function index()
    {
        // Chỉ lấy các bài viết gốc (parent_id = null), kèm thông tin người đăng và đếm số trả lời
        $posts = ForumPost::whereNull('parent_id')
                    ->with('user')
                    ->withCount('replies')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10); // Phân trang 10 bài/trang

        return view('forum.index', compact('posts'));
    }

    // 2. Form tạo câu hỏi mới
    public function create()
    {
        return view('forum.create');
    }

    // 3. Lưu câu hỏi mới
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        ForumPost::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'parent_id' => null // Là bài gốc
        ]);

        return redirect()->route('forum.index')->with('success', 'Đã đăng câu hỏi thành công!');
    }

    // 4. Xem chi tiết bài viết và các bình luận
    public function show($id)
    {
        // Lấy bài viết kèm user, kèm các bình luận (replies) và user của bình luận đó
        $post = ForumPost::with(['user', 'replies.user'])->findOrFail($id);
        return view('forum.show', compact('post'));
    }

    // 5. Gửi bình luận trả lời
    public function reply(Request $request, $id)
    {
        $request->validate(['content' => 'required']);

        ForumPost::create([
            'user_id' => Auth::id(),
            'title' => null, // Bình luận không cần tiêu đề
            'content' => $request->content,
            'parent_id' => $id // Gắn ID cha là bài viết đang xem
        ]);

        return back()->with('success', 'Đã gửi bình luận.');
    }
}