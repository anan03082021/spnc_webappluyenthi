<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category; // Giả sử đã có Model Category cho Chương/Bài
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    // --- PHẦN DÀNH CHO GIÁO VIÊN ---

    // 1. Form upload tài liệu
    public function create()
    {
        // Lấy danh sách danh mục (Chương 1, Chương 2...) để chọn
        $categories = Category::all(); 
        return view('teacher.documents.create', compact('categories'));
    }

    // 2. Xử lý lưu file
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240', // Max 10MB
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Xử lý upload file vào thư mục 'public/documents'
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('documents', 'public');

            Document::create([
                'title' => $request->title,
                'file_path' => $filePath,
                'category_id' => $request->category_id,
                'uploaded_by' => Auth::id(),
            ]);

            return redirect()->route('teacher.documents.index')->with('success', 'Upload tài liệu thành công!');
        }

        return back()->with('error', 'Vui lòng chọn file.');
    }

    // 3. Danh sách tài liệu (Quản lý)
    public function indexTeacher()
    {
        $documents = Document::where('uploaded_by', Auth::id())->latest()->get();
        return view('teacher.documents.index', compact('documents'));
    }

    // 4. Xóa tài liệu
    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        
        // Xóa file vật lý trong storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();
        return back()->with('success', 'Đã xóa tài liệu.');
    }

    // --- PHẦN DÀNH CHO HỌC SINH ---

    // 5. Xem danh sách tài liệu
    public function indexStudent(Request $request)
    {
        // Có thể lọc theo category_id nếu muốn
        $query = Document::with('category')->latest();
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $documents = $query->get();
        $categories = Category::all(); // Để làm bộ lọc

        return view('student.documents.index', compact('documents', 'categories'));
    }
}