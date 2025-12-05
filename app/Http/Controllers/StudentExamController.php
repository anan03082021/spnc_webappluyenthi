<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamController extends Controller
{
    // 1. Dashboard: Hiển thị danh sách đề thi khả dụng
    public function index()
{
    $userId = Auth::id();

    // 1. Lấy danh sách đề thi để hiển thị (như cũ)
    $exams = Exam::where('is_published', 1)->orderBy('created_at', 'desc')->get();

    // 2. Thống kê cho Biểu đồ Tròn (Tiến độ)
    $totalExams = $exams->count();
    // Lấy số lượng đề thi duy nhất mà user đã làm
    $attemptedExamsCount = Result::where('user_id', $userId)->distinct('exam_id')->count('exam_id');
    $notAttemptedCount = $totalExams - $attemptedExamsCount;
    // Tránh số âm nếu dữ liệu lệch
    $notAttemptedCount = $notAttemptedCount < 0 ? 0 : $notAttemptedCount;

    // 3. Thống kê cho Biểu đồ Miền/Đường (Biến động điểm số)
    // Lấy 10 bài làm gần nhất
    $recentResults = Result::where('user_id', $userId)
                        ->with('exam')
                        ->orderBy('created_at', 'asc') // Sắp xếp cũ -> mới để vẽ biểu đồ theo thời gian
                        ->take(10)
                        ->get();

    // Chuẩn bị mảng dữ liệu cho Chart.js
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
        $answers = $request->input('answers'); // Mảng đáp án user chọn: [question_id => 'A']
        
        $correctCount = 0;
        $totalQuestions = $exam->questions->count();
        
        // Logic chấm điểm
        foreach ($exam->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            if ($userAnswer === $question->correct_answer) {
                $correctCount++;
            }
        }
        
        // Tính điểm thang 10
        $score = ($totalQuestions > 0) ? ($correctCount / $totalQuestions) * 10 : 0;
        
        // Lưu kết quả
        $result = Result::create([
            'user_id' => Auth::id(),
            'exam_id' => $exam->id,
            'score' => round($score, 2),
            'completion_time' => 0, // Tạm thời để 0, sẽ nâng cấp JS đếm giờ sau
            'selected_answers' => $answers, // Laravel tự convert mảng sang JSON nhờ cast trong Model
            'submitted_at' => now(),
        ]);

        return redirect()->route('student.exams.result', $result->id);
    }

    // 4. Trang hiển thị kết quả chi tiết
    public function result($id)
    {
        $result = Result::with(['exam.questions', 'user'])->findOrFail($id);
        
        // Bảo mật: Chỉ xem được kết quả của chính mình
        if ($result->user_id != Auth::id()) {
            abort(403);
        }

        return view('student.exams.result', compact('result'));
    }
}