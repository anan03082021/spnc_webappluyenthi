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

    // Hiển thị form tạo đề mới
    public function create()
    {
        return view('teacher.exams.create');
    }

    // Xử lý lưu đề thi và câu hỏi
    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:5',
            'questions' => 'required|array|min:1', // Phải có ít nhất 1 câu hỏi
            'questions.*.content' => 'required',
            'questions.*.correct_answer' => 'required|in:A,B,C,D',
        ]);

        DB::beginTransaction(); // Bắt đầu giao dịch
        try {
            // 2. Tạo Đề thi
            $exam = Exam::create([
                'title' => $request->title,
                'duration' => $request->duration,
                'difficulty' => $request->difficulty,
                'created_by' => Auth::id(),
                'total_questions' => count($request->questions),
                'is_published' => 0 // Mặc định chưa duyệt/ẩn
            ]);

            // 3. Tạo các Câu hỏi
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

            DB::commit(); // Lưu vào DB
            return redirect()->route('teacher.exams.index')->with('success', 'Tạo đề thi thành công!');

        } catch (\Exception $e) {
            DB::rollBack(); // Hoàn tác nếu lỗi
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
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