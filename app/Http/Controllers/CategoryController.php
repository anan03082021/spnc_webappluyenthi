<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // 1. Danh sách danh mục
    public function index()
    {
        // Lấy danh sách kèm số lượng câu hỏi và tài liệu liên quan
        $categories = Category::withCount(['questions', 'documents'])->get();
        return view('admin.categories.index', compact('categories'));
    }

    // 2. Lưu danh mục mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:categories'
        ], [
            'name.required' => 'Tên chủ đề không được để trống.',
            'name.unique' => 'Chủ đề này đã tồn tại.'
        ]);

        Category::create(['name' => $request->name]);

        return back()->with('success', 'Đã thêm chủ đề mới.');
    }

    // 3. Cập nhật danh mục
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,' . $id
        ]);

        $category = Category::findOrFail($id);
        $category->update(['name' => $request->name]);

        return back()->with('success', 'Cập nhật thành công.');
    }

    // 4. Xóa danh mục
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // (Tùy chọn) Kiểm tra xem có dữ liệu ràng buộc không trước khi xóa
        if ($category->questions()->count() > 0 || $category->documents()->count() > 0) {
            return back()->with('error', 'Không thể xóa chủ đề đang chứa câu hỏi hoặc tài liệu.');
        }

        $category->delete();
        return back()->with('success', 'Đã xóa chủ đề.');
    }
}
