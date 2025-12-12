<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Result;
use App\Models\Document; // Đã thêm Model Document
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamController extends Controller
{
    // 1. Dashboard: Hiển thị danh sách đề thi khả dụng & Thống kê
    public function index()
    {
        $userId = Auth::id();

        // 1. Lấy Đề thi (Đề thi thử)
        $exams = Exam::where('is_published', 1)->orderBy('created_at', 'desc')->take(8)->get();

        // 2. Lấy Diễn đàn nổi bật (Sắp xếp theo số lượng trả lời giảm dần)
        $trendingPosts = ForumPost::with('user')
            ->withCount('replies')
            ->orderBy('replies_count', 'desc') // Bài nào nhiều comment nhất lên đầu
            ->take(4) // Lấy 4 bài
            ->get();

        // 3. Lấy Tài liệu mới nhất
        $latestDocuments = Document::with('category')->latest()->take(4)->get();

        // 4. Số liệu cho "Tiến độ học tập" (Để hiển thị trong Modal)
        $totalExams = Exam::where('is_published', 1)->count();
        $attemptedExamsCount = Result::where('user_id', $userId)->distinct('exam_id')->count('exam_id');
        $notAttemptedCount = max(0, $totalExams - $attemptedExamsCount);

        // Dữ liệu biểu đồ
        $recentResults = Result::where('user_id', $userId)->with('exam')->orderBy('created_at', 'asc')->take(10)->get();
        $chartLabels = $recentResults->map(fn($r) => $r->exam->title);
        $chartScores = $recentResults->pluck('score');

        return view('student.dashboard', compact(
            'exams',
            'trendingPosts',
            'latestDocuments',
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
// Trang Thư viện cá nhân (Thay thế hàm history cũ)
public function history()
{
    $userId = Auth::id();
    
    // 1. Dữ liệu gốc
    $results = Result::where('user_id', $userId)->get();

    // 2. Tính toán Thống kê (Tổng quan)
    $totalAttempts = $results->count(); // Tổng số lượt làm bài (bao gồm làm lại)
    $uniqueExamsCount = $results->unique('exam_id')->count(); // Số đề thi khác nhau đã làm
    $avgScore = $results->avg('score'); // Điểm trung bình
    $highestScore = $results->max('score'); // Điểm cao nhất

    // 3. Truy cập gần đây (Lấy 5 bài làm gần nhất)
    $recentAccess = Result::with('exam')
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();

    // 4. Đề thi đã lưu
    $favorites = Auth::user()->bookmarks ?? collect([]);

    // Dữ liệu cho biểu đồ (nếu cần dùng lại)
    $monthlyStats = Result::where('user_id', $userId)
        ->selectRaw('MONTH(created_at) as month, AVG(score) as avg_score')
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('month')->orderBy('month')->get();
    
    $months = $monthlyStats->map(fn($item) => "Tháng " . $item->month);
    $scores = $monthlyStats->pluck('avg_score');

    return view('student.exams.library', compact(
        'totalAttempts', 
        'uniqueExamsCount', // Biến mới
        'avgScore', 
        'highestScore',
        'recentAccess',
        'favorites',
        'months', 'scores'
    ));
}
    // 6. [MỚI] Trang Khám phá toàn bộ đề thi
    public function explore(Request $request)
    {
        // Lấy danh sách chủ đề để làm bộ lọc
        $categories = \App\Models\Category::all();

        // Khởi tạo query lấy đề đã duyệt
        $query = Exam::where('is_published', 1);

        // Xử lý Tìm kiếm (nếu có nhập từ khóa)
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where('title', 'like', "%{$keyword}%");
        }

        // Sắp xếp mới nhất
        $exams = $query->orderBy('created_at', 'desc')->paginate(12); // 12 đề mỗi trang

        return view('student.exams.explore', compact('exams', 'categories'));
    }

    // Hàm xử lý Lưu / Bỏ lưu đề thi
    public function toggleBookmark($id)
    {
        $user = Auth::user();
        // Phương thức toggle: Nếu có rồi thì xóa, chưa có thì thêm
        $user->bookmarks()->toggle($id);
        
        return back()->with('success', 'Đã cập nhật kho đề thi đã lưu.');
    }

    // Trang Tiến độ học tập chi tiết
    public function progress()
    {
        $userId = Auth::id();

        // 1. Dữ liệu cho Biểu đồ Tròn (Hoàn thành vs Tổng)
        $totalExamsInSystem = Exam::where('is_published', 1)->count();
        $completedExams = Result::where('user_id', $userId)->distinct('exam_id')->count('exam_id');
        $remainingExams = max(0, $totalExamsInSystem - $completedExams);

        // 2. Dữ liệu cho Biểu đồ Miền (Biến động điểm số theo thời gian)
        // Lấy 10 bài làm gần nhất để biểu đồ không bị quá dài
        $scoreHistory = Result::where('user_id', $userId)
                        ->orderBy('created_at', 'asc') // Sắp xếp cũ -> mới để vẽ biểu đồ
                        ->take(20) 
                        ->get();
        
        $chartDates = $scoreHistory->map(fn($r) => $r->created_at->format('d/m'));
        $chartScores = $scoreHistory->pluck('score');

        // 3. Dữ liệu cho Biểu đồ Radar (Gợi ý kỹ năng - Giả lập dữ liệu)
        // *Lưu ý: Sau này bạn cần tính trung bình điểm theo category_id của câu hỏi
        // Hiện tại tôi sẽ random nhẹ để demo giao diện cho bạn hình dung
        $skillStats = [
            'Lý thuyết' => rand(50, 90),
            'Thực hành' => rand(40, 80),
            'Tư duy' => rand(60, 95),
            'Tốc độ' => rand(30, 80),
            'Chính xác' => rand(70, 100),
        ];

        // 4. Danh sách chi tiết (Phân trang)
        $historyDetails = Result::where('user_id', $userId)
                        ->with('exam')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return view('student.progress.index', compact(
            'completedExams', 'remainingExams',
            'chartDates', 'chartScores',
            'skillStats',
            'historyDetails'
        ));
    }
}