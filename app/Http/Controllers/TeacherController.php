<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use App\Models\Result; // Model kết quả thi
use App\Models\User;
use App\Models\Classroom; // <--- BẮT BUỘC THÊM DÒNG NÀY
use App\Models\ForumPost;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacherId = Auth::id();

        // 1. TÍNH TỔNG SỐ HỌC SINH (Logic mới: Lấy từ Lớp học)
        // Bước A: Lấy danh sách ID các lớp do giáo viên này chủ nhiệm
        $myClassroomIds = Classroom::where('teacher_id', $teacherId)->pluck('id');

        // Bước B: Đếm tất cả User đang ở trong các lớp đó
        // (Tôi đã bỏ điều kiện check role để đảm bảo đếm đủ nếu dữ liệu role bị lệch)
        $totalStudents = User::whereIn('classroom_id', $myClassroomIds)->count();


        // 2. CÁC THỐNG KÊ KHÁC (Giữ nguyên logic cũ)
        
        // Query cơ bản lấy kết quả thi
        $resultsQuery = Result::whereHas('exam', function ($q) use ($teacherId) {
            $q->where('created_by', $teacherId);
        });

        $totalExams = Exam::where('created_by', $teacherId)->count();
        $totalAttempts = $resultsQuery->count();
        
        // Điểm trung bình
        $avgScore = round($resultsQuery->avg('score') ?? 0, 1);

        // 3. TÍNH DỮ LIỆU BIỂU ĐỒ (CHART DATA)
        $scoreStats = $resultsQuery->selectRaw("
            COALESCE(SUM(CASE WHEN score < 5 THEN 1 ELSE 0 END), 0) as weak,
            COALESCE(SUM(CASE WHEN score >= 5 AND score < 7 THEN 1 ELSE 0 END), 0) as average,
            COALESCE(SUM(CASE WHEN score >= 7 AND score < 9 THEN 1 ELSE 0 END), 0) as good,
            COALESCE(SUM(CASE WHEN score >= 9 THEN 1 ELSE 0 END), 0) as excellent
        ")->first();

        $chartData = [
            $scoreStats->weak,
            $scoreStats->average,
            $scoreStats->good,
            $scoreStats->excellent
        ];

        // 4. DANH SÁCH HOẠT ĐỘNG GẦN ĐÂY
        $recentResults = Result::with(['user', 'exam'])
            ->whereHas('exam', function ($q) use ($teacherId) {
                $q->where('created_by', $teacherId);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 5. TOP HỌC SINH XUẤT SẮC
        // Lưu ý: Model User cần có function results() { return $this->hasMany(Result::class); }
        $topStudents = User::whereHas('results.exam', function ($q) use ($teacherId) {
            $q->where('created_by', $teacherId);
        })
        ->withAvg(['results as avg_score' => function ($q) use ($teacherId) {
            $q->whereHas('exam', function ($subQ) use ($teacherId) {
                $subQ->where('created_by', $teacherId);
            });
        }], 'score')
        ->orderByDesc('avg_score')
        ->take(5)
        ->get();

        // 6. CÂU HỎI MỚI TRÊN DIỄN ĐÀN (Optional)
        $newQuestions = [];
        if (class_exists('App\Models\ForumPost')) {
            $newQuestions = ForumPost::with('user')
                ->where('parent_id', null)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        }

        return view('teacher.dashboard', compact(
            'totalExams',
            'totalStudents', // Biến này giờ đã chứa số liệu chính xác từ Classroom
            'totalAttempts',
            'avgScore',
            'chartData',
            'recentResults',
            'topStudents',
            'newQuestions'
        ));
    }
}