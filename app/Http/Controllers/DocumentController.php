<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Category; // Thêm dòng này để dùng danh mục
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    // =========================================================
    // 1. PHẦN DÀNH CHO GIÁO VIÊN (Quản lý tài liệu)
    // =========================================================
    
    public function indexTeacher(Request $request)
    {
        // Khởi tạo query lấy tài liệu của chính giáo viên này
        $query = Document::where('user_id', Auth::id());

        // Thêm chức năng tìm kiếm (nếu có nhập từ khóa)
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', "%{$request->search}%");
        }

        // Lấy dữ liệu (Sắp xếp mới nhất trước)
        $documents = $query->orderBy('created_at', 'desc')->get();

        // Tính tổng dung lượng đã dùng (MB)
        $totalUsage = $documents->sum('file_size');

        // Lấy danh sách danh mục để hiển thị trong Modal Upload (nếu cần)
        $categories = Category::all();

        return view('teacher.documents.index', compact('documents', 'totalUsage', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // Max 10MB
            'category_id' => 'nullable|exists:categories,id', // Validate danh mục
        ], [
            'file.max' => 'File quá lớn (Tối đa 10MB).',
            'file.required' => 'Vui lòng chọn file.',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        
        // Tính dung lượng ra MB
        $size = round($file->getSize() / 1024 / 1024, 2); 
        if ($size == 0) $size = 0.01;

        // Lưu file
        $path = $file->storeAs('documents', $filename, 'public');

        Document::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id, // Lưu category_id nếu có
            'title' => $request->title,
            'file_path' => $path,
            'file_type' => strtolower($extension),
            'file_size' => $size,
        ]);

        return back()->with('success', 'Tải tài liệu lên thành công!');
    }

    public function destroy($id)
    {
        // Chỉ cho phép xóa tài liệu của chính mình
        $document = Document::where('user_id', Auth::id())->findOrFail($id);
        
        // Xóa file vật lý
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();
        return back()->with('success', 'Đã xóa tài liệu.');
    }

    // =========================================================
    // 2. PHẦN DÀNH CHO HỌC SINH (Xem & Tải) - KHẮC PHỤC LỖI
    // =========================================================

    public function indexStudent(Request $request)
    {
        // Học sinh được xem tài liệu của TẤT CẢ giáo viên (hoặc lọc sau này)
        $query = Document::with(['user', 'category']); // Eager load để lấy tên GV và Danh mục

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', "%{$request->search}%");
        }

        // Lọc theo danh mục (nếu có)
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category_id', $request->category);
        }

        // Phân trang 12 tài liệu mỗi trang
        $documents = $query->orderBy('created_at', 'desc')->paginate(12);
        
        // Lấy danh mục để hiển thị bộ lọc
        $categories = Category::all();

        return view('student.documents.index', compact('documents', 'categories'));
    }

    // =========================================================
    // 3. CHỨC NĂNG CHUNG (Tải xuống)
    // =========================================================

    public function download($id)
    {
        // Tìm tài liệu (Không cần check user_id để HS cũng tải được)
        $document = Document::findOrFail($id);
        
        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download($document->file_path, $document->title . '.' . $document->file_type);
        } else {
            return back()->with('error', 'File không tồn tại hoặc đã bị xóa.');
        }
    }
}