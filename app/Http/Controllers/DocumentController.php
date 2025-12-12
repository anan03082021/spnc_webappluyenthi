<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    // 1. Danh sách tài liệu
    public function indexTeacher()
    {
        $documents = Document::where('user_id', Auth::id())
                             ->orderBy('created_at', 'desc')
                             ->get(); // Lấy hết để hiển thị dạng Grid

        // Tính tổng dung lượng đã dùng (Giả lập)
        $totalUsage = $documents->sum(function($doc) {
            return (float) $doc->file_size; // Cộng dồn MB
        });

        return view('teacher.documents.index', compact('documents', 'totalUsage'));
    }

    // 2. Upload file mới
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        
        // Tính dung lượng ra MB (Làm tròn 2 số lẻ)
        $size = round($file->getSize() / 1024 / 1024, 2); 
        if ($size == 0) $size = 0.01; // Tối thiểu

        // Lưu vào folder 'documents' trong storage/app/public
        $path = $file->storeAs('documents', $filename, 'public');

        Document::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'file_path' => $path,
            'file_type' => strtolower($extension),
            'file_size' => $size,
        ]);

        return back()->with('success', 'Tải tài liệu lên thành công!');
    }

    // 3. Tải xuống
    public function download($id)
    {
        $document = Document::findOrFail($id);
        return Storage::disk('public')->download($document->file_path, $document->title . '.' . $document->file_type);
    }

    // 4. Xóa tài liệu
    public function destroy($id)
    {
        $document = Document::where('user_id', Auth::id())->findOrFail($id);
        
        // Xóa file vật lý
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();
        return back()->with('success', 'Đã xóa tài liệu.');
    }
}