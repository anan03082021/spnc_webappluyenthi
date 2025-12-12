<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; 

class ExamController extends Controller
{
    // 1. Hiển thị danh sách đề thi (Có Tìm kiếm & Lọc)
    public function index(Request $request)
    {
        $query = Exam::where('created_by', Auth::id());

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where('title', 'like', "%{$keyword}%");
        }

        // Lọc trạng thái
        if ($request->has('status') && $request->status != 'all') {
            if ($request->status == 'published') {
                $query->where('is_published', 1);
            } elseif ($request->status == 'draft') {
                $query->where('is_published', 0);
            }
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('teacher.exams.index', compact('exams'));
    }

    // 2. Form tạo mới thủ công
    public function create()
    {
        $categories = Category::all(); 
        return view('teacher.exams.create', compact('categories'));
    }

    // 3. Xử lý tạo mới thủ công
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'duration' => 'required|integer|min:1',
        ]);

        $exam = Exam::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'difficulty' => 'mixed', // Mặc định là Tổng hợp (vì đã bỏ chọn ở giao diện)
            'total_questions' => count($request->questions ?? []),
            'created_by' => Auth::id(),
            'is_published' => 0,
        ]);

        if ($request->has('questions')) {
            foreach ($request->questions as $key => $qData) {
                $this->saveQuestion($exam->id, $qData, $key + 1, $request, "questions.$key.image");
            }
        }

        return redirect()->route('teacher.exams.show', $exam->id)
                 ->with('success', 'Đã tạo đề thi thành công! Hãy kiểm tra lại nội dung.');
    }

    // 4. Form chỉnh sửa
    public function edit($id)
    {
        $exam = Exam::with('questions')->where('created_by', Auth::id())->findOrFail($id);
        return view('teacher.exams.edit', compact('exam'));
    }

    // 5. Xử lý cập nhật
public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        // 1. Cập nhật thông tin chung
        $exam->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'difficulty' => 'mixed',
            'is_published' => 0,
        ]);

        // 2. Xử lý danh sách câu hỏi (Thêm / Sửa / Xóa)
        if ($request->has('questions')) {
            
            // --- BƯỚC A: XÓA CÂU HỎI ĐÃ BỊ LOẠI BỎ ---
            // Lấy danh sách ID các câu hỏi được gửi lên từ form
            $submittedIds = collect($request->questions)->pluck('id')->filter()->toArray();
            
            // Xóa các câu hỏi trong Database mà không có trong danh sách gửi lên (nghĩa là user đã xóa trên giao diện)
            $exam->questions()->whereNotIn('id', $submittedIds)->delete();

            // --- BƯỚC B: CẬP NHẬT HOẶC THÊM MỚI ---
            foreach ($request->questions as $key => $qData) {
                
                // Tìm câu hỏi cũ (nếu có ID) hoặc Tạo mới (nếu chưa có ID)
                $question = isset($qData['id']) ? Question::find($qData['id']) : new Question();
                
                // Chuẩn bị dữ liệu
                $type = $qData['type'] ?? 'one_choice';
                $finalCorrectAnswer = $qData['correct_answer'] ?? '';

                if ($type == 'true_false') {
                    $answers = $qData['tf_correct'] ?? [];
                    $finalCorrectAnswer = implode(',', [
                        ($answers['a'] ?? 'F') == 'T' ? 'T' : 'F',
                        ($answers['b'] ?? 'F') == 'T' ? 'T' : 'F',
                        ($answers['c'] ?? 'F') == 'T' ? 'T' : 'F',
                        ($answers['d'] ?? 'F') == 'T' ? 'T' : 'F',
                    ]);
                }

                $question->exam_id = $exam->id;
                $question->content = $qData['content'] ?? '';
                $question->type = $type;
                
                // Mapping options
                if ($type == 'true_false') {
                    $question->option_a = $qData['tf_options']['a'] ?? ($qData['option_a'] ?? '');
                    $question->option_b = $qData['tf_options']['b'] ?? ($qData['option_b'] ?? '');
                    $question->option_c = $qData['tf_options']['c'] ?? ($qData['option_c'] ?? '');
                    $question->option_d = $qData['tf_options']['d'] ?? ($qData['option_d'] ?? '');
                } else {
                    $question->option_a = $qData['option_a'] ?? '';
                    $question->option_b = $qData['option_b'] ?? '';
                    $question->option_c = $qData['option_c'] ?? '';
                    $question->option_d = $qData['option_d'] ?? '';
                }

                $question->correct_answer = $finalCorrectAnswer;
                // Cập nhật lại thứ tự (order) theo vị trí trên form
                $question->order = $key + 1; 
                $question->save();
            }

            // --- BƯỚC C: CẬP NHẬT TỔNG SỐ CÂU (QUAN TRỌNG) ---
            // Đếm lại thực tế trong Database và lưu vào bảng exams
            $exam->update(['total_questions' => $exam->questions()->count()]);
        }

        return redirect()->route('teacher.exams.show', $exam->id)
                 ->with('success', 'Đã cập nhật đề thi thành công!');
    }

    // 6. Xem chi tiết đề thi
    public function show($id)
    {
        // Lấy đề thi kèm câu hỏi, sắp xếp theo ID (tạo trước đứng trước)
        // Chỉ lấy đề do chính giáo viên đó tạo để bảo mật
        $exam = Exam::with(['questions' => function($q) {
            $q->orderBy('id', 'asc'); 
        }])
        ->where('created_by', Auth::id())
        ->findOrFail($id);

        $realCount = $exam->questions->count();
    if ($exam->total_questions !== $realCount) {
        $exam->update(['total_questions' => $realCount]);
    }

        return view('teacher.exams.show', compact('exam'));
    }

    // 7. Xóa đề thi
    public function destroy($id)
    {
        $exam = Exam::where('created_by', Auth::id())->findOrFail($id);
        $exam->delete();
        return back()->with('success', 'Đã xóa đề thi.');
    }

    // --- TÍNH NĂNG NHẬP ĐỀ NHANH (QUICK CREATE) ---

    public function createQuick()
    {
        $categories = Category::all();
        return view('teacher.exams.create_quick', compact('categories'));
    }

    public function storeQuick(Request $request)
    {
        // 1. Validate
        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
        ]);

        // 2. Tạo Đề thi
        $exam = Exam::create([
            'created_by' => Auth::id(),
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'duration' => $request->duration,
            'difficulty' => 'mixed', // Mặc định
            'total_questions' => 0,
            'is_published' => 0,
        ]);

        // 3. Lưu Câu hỏi
        $count = 0;
        if ($request->has('questions')) {
            foreach ($request->questions as $key => $qData) {
                // Gọi hàm lưu tách biệt để code gọn hơn
                $this->saveQuestion($exam->id, $qData, $count + 1, $request, "questions.$key.image");
                $count++;
            }
            
            // Cập nhật số lượng
            $exam->update(['total_questions' => $count]);
        }

        return redirect()->route('teacher.exams.show', $exam->id)
                 ->with('success', 'Đã tạo đề thi thành công! Hãy kiểm tra lại nội dung.');
    }

    // --- HÀM HỖ TRỢ LƯU CÂU HỎI (Private Helper) ---
    private function saveQuestion($examId, $qData, $order, $request, $fileInputName)
    {
        // 1. Upload Ảnh
        $imagePath = null;
        if ($request->hasFile($fileInputName)) {
            $imagePath = $request->file($fileInputName)->store('question_images', 'public');
        }

        // 2. Xử lý Đáp án
        $type = $qData['type'] ?? 'one_choice';
        $finalCorrectAnswer = $qData['correct_answer'] ?? '';

        if ($type == 'true_false') {
            $answers = $qData['tf_correct'] ?? [];
            $finalCorrectAnswer = implode(',', [
                ($answers['a'] ?? 'F') == 'T' ? 'T' : 'F',
                ($answers['b'] ?? 'F') == 'T' ? 'T' : 'F',
                ($answers['c'] ?? 'F') == 'T' ? 'T' : 'F',
                ($answers['d'] ?? 'F') == 'T' ? 'T' : 'F',
            ]);
        }

        // 3. Xử lý nội dung Options (Hỗ trợ cả 2 nguồn dữ liệu option_a và tf_options)
        $optA = $qData['option_a'] ?? ''; 
        $optB = $qData['option_b'] ?? '';
        $optC = $qData['option_c'] ?? '';
        $optD = $qData['option_d'] ?? '';

        if ($type == 'true_false' && isset($qData['tf_options'])) {
            $optA = $qData['tf_options']['a'] ?? $optA;
            $optB = $qData['tf_options']['b'] ?? $optB;
            $optC = $qData['tf_options']['c'] ?? $optC;
            $optD = $qData['tf_options']['d'] ?? $optD;
        }

        // 4. Insert vào DB
        Question::create([
            'exam_id' => $examId,
            'content' => $qData['content'] ?? 'Nội dung trống',
            'image'   => $imagePath,
            'type'    => $type,
            'level'   => $qData['level'] ?? 'medium',
            'category_id' => $qData['category_id'] ?? null,
            'option_a' => $optA,
            'option_b' => $optB,
            'option_c' => $optC,
            'option_d' => $optD,
            'correct_answer' => $finalCorrectAnswer,
            'order' => $order
        ]);
    }
}