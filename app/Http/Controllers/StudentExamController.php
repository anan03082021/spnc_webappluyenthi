<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Result;
use App\Models\Document; // Đã thêm Model Document
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamController extends Controller
{
    // 1. Dashboard: Hiển thị danh sách đề thi khả dụng & Thống kê
    public function index()
    {
        $userId = Auth::id();

        // 1. Lấy danh sách đề thi để hiển thị
        $exams = Exam::where('is_published', 1)->orderBy('created_at', 'desc')->get();

        // 2. Thống kê cho Biểu đồ Tròn (Tiến độ)
        $totalExams = $exams->count();
        $attemptedExamsCount = Result::where('user_id', $userId)->distinct('exam_id')->count('exam_id');
        $notAttemptedCount = max(0, $totalExams - $attemptedExamsCount); // Dùng max(0) cho gọn

        // 3. Thống kê cho Biểu đồ Đường (Biến động điểm số - 10 bài gần nhất)
        $recentResults = Result::where('user_id', $userId)
                                ->with('exam')
                                ->orderBy('created_at', 'asc')
                                ->take(10)
                                ->get();

        // Chuẩn bị dữ liệu Chart.js
        $chartLabels = $recentResults->map(function ($result) {
            return $result->exam->title . ' (' . $result->created_at->format('d/m') . ')';
        });
        $chartScores = $recentResults->pluck('score');

        return view('student.dashboard', compact(
            'exams', 
            'attemptedExamsCount', 
            'notAttemptedCount', 
            'chartLabels', 
            'chartScores'
        ));
    }

    // 2. Vào trang làm bài
    public function show($id)
    {
        $exam = Exam::with('questions')->findOrFail($id);
        return view('student.exams.show', compact('exam'));
    }

    // 3. Xử lý nộp bài và Chấm điểm
    public function store(Request $request, $id)
    {
        $exam = Exam::with('questions')->findOrFail($id);
        $answers = $request->input('answers', []); // Mặc định là mảng rỗng nếu không chọn gì
        
        $correctCount = 0;
        $totalQuestions = $exam->questions->count();
        
        foreach ($exam->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            if ($userAnswer === $question->correct_answer) {
                $correctCount++;
            }
        }
        
        // Tính điểm (Thang 10)
        $score = ($totalQuestions > 0) ? ($correctCount / $totalQuestions) * 10 : 0;
        
        // Lưu kết quả
        $result = Result::create([
            'user_id' => Auth::id(),
            'exam_id' => $exam->id,
            'score' => round($score, 2),
            'completion_time' => 0, // Sau này có thể lấy từ request input hidden
            'selected_answers' => $answers, 
            'submitted_at' => now(),
        ]);

        return redirect()->route('student.exams.result', $result->id);
    }

    // 4. Trang hiển thị kết quả chi tiết & Gợi ý tài liệu
    public function result($id)
    {
        // Load kèm quan hệ category của câu hỏi để tìm lỗi sai thuộc chương nào
        $result = Result::with(['exam.questions.category', 'user'])->findOrFail($id);

        // Bảo mật: Không cho xem kết quả của người khác
        if ($result->user_id != Auth::id()) {
            abort(403);
        }

        // --- LOGIC GỢI Ý TÀI LIỆU ---
        $wrongCategoryIds = [];

        foreach ($result->exam->questions as $q) {
            $userAns = $result->selected_answers[$q->id] ?? null;
            
            // Nếu làm sai hoặc chưa làm VÀ câu hỏi đó có gắn chủ đề
            if (($userAns !== $q->correct_answer) && $q->category_id) {
                $wrongCategoryIds[] = $q->category_id;
            }
        }

        // Lấy danh sách tài liệu thuộc các chủ đề làm sai
        // array_unique để loại bỏ trùng lặp (ví dụ sai 3 câu cùng chương 1 thì chỉ cần lấy tài liệu chương 1 một lần)
        $recommendedDocuments = Document::whereIn('category_id', array_unique($wrongCategoryIds))->get();

        return view('student.exams.result', compact('result', 'recommendedDocuments'));
    }

    // 5. [BỔ SUNG] Lịch sử làm bài
    public function history()
    {
        $results = Result::where('user_id', Auth::id())
                    ->with('exam') // Lấy kèm thông tin đề thi
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('student.exams.history', compact('results'));
    }
}