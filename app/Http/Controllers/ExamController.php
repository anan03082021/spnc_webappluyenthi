<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    // Hiển thị danh sách đề thi
    public function index()
    {
        $exams = Exam::where('created_by', Auth::id())->orderBy('created_at', 'desc')->get();
        return view('teacher.exams.index', compact('exams'));
    }

// Sửa hàm create
    public function create()
    {
        // Lấy danh sách chủ đề để gán cho câu hỏi
        $categories = \App\Models\Category::all(); 
        return view('teacher.exams.create', compact('categories'));
    }

    // Sửa hàm store (Lưu câu hỏi kèm category_id)
    public function store(Request $request)
    {
        // ... (Validate giữ nguyên) ...

        $exam = Exam::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'difficulty' => $request->difficulty,
            'total_questions' => count($request->questions),
            'created_by' => Auth::id(),
            'is_published' => 0
        ]);

        foreach ($request->questions as $q) {
            \App\Models\Question::create([
                'exam_id' => $exam->id,
                'content' => $q['content'],
                'option_a' => $q['option_a'],
                'option_b' => $q['option_b'],
                'option_c' => $q['option_c'],
                'option_d' => $q['option_d'],
                'correct_answer' => $q['correct_answer'],
                'explanation' => $q['explanation'] ?? null,
                'category_id' => $q['category_id'] ?? null, // <--- LƯU QUAN TRỌNG
            ]);
        }

        return redirect()->route('teacher.exams.index')->with('success', 'Tạo đề thi thành công!');
    }

    // 4. Hiển thị form chỉnh sửa
    public function edit($id)
    {
        // Lấy đề thi kèm câu hỏi, chỉ cho phép sửa đề của chính mình tạo
        $exam = Exam::with('questions')->where('created_by', Auth::id())->findOrFail($id);
        return view('teacher.exams.edit', compact('exam'));
    }

    // 5. Xử lý cập nhật
    public function update(Request $request, $id)
    {
        $exam = Exam::where('created_by', Auth::id())->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:5',
            'questions' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Cập nhật thông tin chung
            $exam->update([
                'title' => $request->title,
                'duration' => $request->duration,
                'difficulty' => $request->difficulty,
                'total_questions' => count($request->questions),
                // Khi sửa xong, đề sẽ chuyển về trạng thái Chờ duyệt để Admin kiểm tra lại
                'is_published' => 0 
            ]);

            // Xóa câu hỏi cũ
            $exam->questions()->delete();

            // Tạo lại câu hỏi mới
            foreach ($request->questions as $q) {
                Question::create([
                    'exam_id' => $exam->id,
                    'content' => $q['content'],
                    'option_a' => $q['option_a'],
                    'option_b' => $q['option_b'],
                    'option_c' => $q['option_c'],
                    'option_d' => $q['option_d'],
                    'correct_answer' => $q['correct_answer'],
                    'explanation' => $q['explanation'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('teacher.exams.index')->with('success', 'Cập nhật đề thi thành công! Vui lòng chờ Admin duyệt lại.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // 6. Xóa đề thi
    public function destroy($id)
    {
        $exam = Exam::where('created_by', Auth::id())->findOrFail($id);
        
        // Xóa đề (Các câu hỏi và kết quả thi liên quan sẽ tự xóa nhờ onCascadeDelete trong Migration)
        $exam->delete();

        return back()->with('success', 'Đã xóa đề thi.');
    }
}