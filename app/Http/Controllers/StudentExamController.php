<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Result;
use App\Models\Document;
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

        // 2. Lấy Diễn đàn nổi bật (Nếu có model ForumPost)
 $trendingPosts = [];
        if (class_exists('App\Models\ForumPost')) {
            $trendingPosts = ForumPost::where('scope', 'public') // <--- QUAN TRỌNG: Chỉ lấy bài công khai
                ->with('user')
                ->withCount('replies')
                ->orderBy('replies_count', 'desc') // Bài nào nhiều comment nhất lên đầu
                ->take(4) 
                ->get();
        }

        // 3. Lấy Tài liệu mới nhất
        $latestDocuments = Document::with('category')->latest()->take(4)->get();

        // 4. Số liệu cho "Tiến độ học tập"
        $totalExams = Exam::where('is_published', 1)->count();
        $attemptedExamsCount = Result::where('user_id', $userId)->distinct('exam_id')->count('exam_id');
        $notAttemptedCount = max(0, $totalExams - $attemptedExamsCount);

        // Dữ liệu biểu đồ
        $recentResults = Result::where('user_id', $userId)->with('exam')->orderBy('created_at', 'asc')->take(10)->get();
        $chartLabels = $recentResults->map(fn($r) => $r->exam->title);
        $chartScores = $recentResults->pluck('score');
//Lấy danh sách ID các đề đã làm
        $attemptedExamIds = Result::where('user_id', $userId)
                                  ->distinct()
                                  ->pluck('exam_id')
                                  ->toArray();

        return view('student.dashboard', compact(
            'exams',
            'attemptedExamIds',
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
        $exam = Exam::with(['questions' => function($q) {
            $q->orderBy('order', 'asc'); // Sắp xếp câu hỏi theo thứ tự
        }])->findOrFail($id);
        
        return view('student.exams.show', compact('exam'));
    }

    // 3. Xử lý nộp bài và Chấm điểm (CẬP NHẬT QUAN TRỌNG)
// 3. Xử lý nộp bài và Chấm điểm (CẬP NHẬT LOGIC ĐIỂM THÀNH PHẦN)
public function store(Request $request, $id)
    {
        // 1. Lấy dữ liệu
        $exam = Exam::with('questions')->findOrFail($id);
        $submittedAnswers = $request->input('answers', []); 
        
        // 2. Tính điểm
        $totalRawPoints = 0;  // Tổng điểm gốc tối đa
        $earnedRawPoints = 0; // Tổng điểm gốc học sinh đạt được
        
        foreach ($exam->questions as $question) {
            $totalRawPoints++; 
            $userAnswer = $submittedAnswers[$question->id] ?? null;

            // --- A. TRẮC NGHIỆM ---
            if ($question->type == 'one_choice') {
                if ($userAnswer && strtoupper($userAnswer) == strtoupper($question->correct_answer)) {
                    $earnedRawPoints += 1;
                }
            } 
            // --- B. ĐÚNG / SAI ---
            elseif ($question->type == 'true_false') {
                $correctArr = explode(',', $question->correct_answer);
                $keys = ['a', 'b', 'c', 'd'];
                $correctSubCount = 0;

                foreach($keys as $idx => $key) {
                    $uAns = $userAnswer[$key] ?? 'F';
                    $cAns = $correctArr[$idx] ?? 'F';
                    if ($uAns == $cAns) $correctSubCount++;
                }
                $earnedRawPoints += ($correctSubCount * 0.25);
            }
        }
        
        // Quy đổi thang điểm 10
        $score = ($totalRawPoints > 0) ? round(($earnedRawPoints / $totalRawPoints) * 10, 2) : 0;
        
        // 3. LƯU VÀO DATABASE (CÓ BẮT LỖI)
        try {
            $result = Result::create([
                'user_id'         => Auth::id(),
                'exam_id'         => $exam->id,
                'score'           => $score,
                'completion_time' => 0, 
                
                // --- LƯU SONG SONG CẢ 2 CỘT ---
                'student_answers'  => $submittedAnswers, // Cột mới
                'selected_answers' => $submittedAnswers, // Cột cũ (Backup)
                
                'submitted_at'    => now(),
            ]);

            return redirect()->route('student.exams.result', $result->id);

        } catch (\Exception $e) {
            // NẾU LỖI, IN RA MÀN HÌNH ĐỂ BIẾT NGAY
            dd([
                'LỖI KHI LƯU DỮ LIỆU' => $e->getMessage(),
                'GỢI Ý' => 'Hãy kiểm tra xem bảng results đã có đủ 2 cột student_answers và selected_answers chưa, và Model Result.php đã khai báo fillable chưa.',
            ]);
        }
    }
    // 4. Trang hiển thị kết quả chi tiết & Gợi ý tài liệu
    public function result($id)
    {
        $result = Result::with(['exam.questions.category', 'user'])->where('user_id', Auth::id())->findOrFail($id);

        // Lấy lại đáp án đã lưu (decode JSON nếu cần)
        $studentAnswers = $result->student_answers; 
        if (is_string($studentAnswers)) {
            $studentAnswers = json_decode($studentAnswers, true);
        }

        // --- LOGIC GỢI Ý TÀI LIỆU ---
        $wrongCategoryIds = [];

        foreach ($result->exam->questions as $q) {
            $userAns = $studentAnswers[$q->id] ?? null;
            
            // Logic check sai đơn giản (chỉ áp dụng cho trắc nghiệm 1 lựa chọn để gợi ý)
            // Với câu đúng sai, logic này cần mở rộng thêm
            if ($q->type == 'one_choice' && ($userAns !== $q->correct_answer) && $q->category_id) {
                $wrongCategoryIds[] = $q->category_id;
            }
        }

        $recommendedDocuments = Document::whereIn('category_id', array_unique($wrongCategoryIds))->take(3)->get();

        return view('student.exams.result', compact('result', 'studentAnswers', 'recommendedDocuments'));
    }

    // 5. Lịch sử làm bài
    public function history()
    {
        $userId = Auth::id();
        $results = Result::where('user_id', $userId)->get();

        $totalAttempts = $results->count();
        $uniqueExamsCount = $results->unique('exam_id')->count();
        $avgScore = round($results->avg('score') ?? 0, 1);
        $highestScore = $results->max('score') ?? 0;

        $recentAccess = Result::with('exam')
                            ->where('user_id', $userId)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        // Kiểm tra quan hệ bookmarks trong User model
        $favorites = collect([]);
        if (method_exists(Auth::user(), 'bookmarks')) {
            $favorites = Auth::user()->bookmarks;
        }

        // Biểu đồ theo tháng
        $monthlyStats = Result::where('user_id', $userId)
            ->selectRaw('MONTH(created_at) as month, AVG(score) as avg_score')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')->orderBy('month')->get();
        
        $months = $monthlyStats->map(fn($item) => "Tháng " . $item->month);
        $scores = $monthlyStats->pluck('avg_score');

        return view('student.exams.library', compact(
            'totalAttempts', 
            'uniqueExamsCount',
            'avgScore', 
            'highestScore',
            'recentAccess',
            'favorites',
            'months', 'scores'
        ));
    }

    // 6. Trang Khám phá toàn bộ đề thi
    public function explore(Request $request)
    {
        $categories = \App\Models\Category::all();
        $query = Exam::where('is_published', 1);

        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where('title', 'like', "%{$keyword}%");
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(12);

        $attemptedExamIds = Result::where('user_id', Auth::id())
                                  ->distinct()
                                  ->pluck('exam_id')
                                  ->toArray();

        return view('student.exams.explore', compact('exams', 'categories', 'attemptedExamIds'));
    }

    // Hàm xử lý Lưu / Bỏ lưu đề thi
    public function toggleBookmark($id)
    {
        $user = Auth::user();
        if (method_exists($user, 'bookmarks')) {
            $user->bookmarks()->toggle($id);
            return back()->with('success', 'Đã cập nhật kho đề thi đã lưu.');
        }
        return back()->with('error', 'Chức năng đang bảo trì.');
    }

    // Trang Tiến độ học tập chi tiết
    public function progress()
    {
        $userId = Auth::id();

        $totalExamsInSystem = Exam::where('is_published', 1)->count();
        $completedExams = Result::where('user_id', $userId)->distinct('exam_id')->count('exam_id');
        $remainingExams = max(0, $totalExamsInSystem - $completedExams);

        $scoreHistory = Result::where('user_id', $userId)
                        ->orderBy('created_at', 'asc')
                        ->take(20) 
                        ->get();
        
        $chartDates = $scoreHistory->map(fn($r) => $r->created_at->format('d/m'));
        $chartScores = $scoreHistory->pluck('score');

        // Dữ liệu giả lập kỹ năng (Cần phát triển thêm logic tính toán thật sau này)
        $skillStats = [
            'Lý thuyết' => rand(50, 90),
            'Thực hành' => rand(40, 80),
            'Tư duy' => rand(60, 95),
            'Tốc độ' => rand(30, 80),
            'Chính xác' => rand(70, 100),
        ];

$historyDetails = Result::where('user_id', $userId)
                    ->with(['exam.questions']) // <--- THÊM .questions VÀO ĐÂY
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