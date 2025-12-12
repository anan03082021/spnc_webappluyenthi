<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Classroom; 
use App\Models\User; 
use Illuminate\Support\Str;

class TeacherClassController extends Controller
{
    // 1. Hiển thị danh sách lớp
    public function index(Request $request)
    {
        $teacherId = Auth::id();

        // Lấy lớp của giáo viên hiện tại + đếm số học sinh
        $query = Classroom::where('teacher_id', $teacherId)
                          ->withCount('students'); 

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('code', 'like', "%{$keyword}%");
            });
        }

        $classes = $query->orderBy('created_at', 'desc')->paginate(9);

        return view('teacher.classes.index', compact('classes'));
    }

    // 2. Xử lý tạo lớp mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string',
        ]);

        // Tạo mã lớp tự động (VD: CLASS-AB123)
        $code = strtoupper('CLASS-' . Str::random(5));

        Classroom::create([
            'teacher_id' => Auth::id(),
            'name' => $request->name,
            'code' => $code,
            'academic_year' => $request->academic_year,
        ]);

        return redirect()->back()->with('success', 'Đã tạo lớp học thành công!');
    }

    // 3. XEM CHI TIẾT LỚP HỌC (Hàm bạn đang thiếu)
    public function show($id)
    {
        // Tìm lớp học (phải thuộc về giáo viên này)
        $classroom = Classroom::where('teacher_id', Auth::id())
                              ->withCount('students') // Đếm tổng học sinh
                              ->findOrFail($id);
        
        // Lấy danh sách học sinh trong lớp (phân trang 20 em/trang)
        // Lưu ý: Đảm bảo Model Classroom có hàm students() là hasMany(User::class)
        $students = $classroom->students()->orderBy('name')->paginate(20);

        return view('teacher.classes.show', compact('classroom', 'students'));
    }

    // 4. Xóa lớp học
    public function destroy($id)
    {
        $classroom = Classroom::where('teacher_id', Auth::id())->findOrFail($id);
        $classroom->delete();
        
        return redirect()->route('teacher.classes.index')->with('success', 'Đã giải tán lớp học.');
    }

    // 5. Xóa học sinh khỏi lớp (Nếu cần dùng sau này)
    public function removeStudent($classId, $studentId)
    {
        // Kiểm tra quyền sở hữu lớp
        $classroom = Classroom::where('teacher_id', Auth::id())->findOrFail($classId);
        
        // Tìm học sinh và set classroom_id về null
        $student = User::where('id', $studentId)->where('classroom_id', $classId)->firstOrFail();
        $student->classroom_id = null;
        $student->save();

        return back()->with('success', 'Đã mời học sinh ra khỏi lớp.');
    }

    // Thêm học sinh vào lớp bằng Email
    public function addStudent(Request $request, $classId)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Không tìm thấy học sinh có email này trong hệ thống.',
        ]);

        // 1. Tìm lớp
        $classroom = Classroom::where('teacher_id', Auth::id())->findOrFail($classId);

        // 2. Tìm học sinh
        $student = \App\Models\User::where('email', $request->email)
                                   ->where('role', 'student') // Chỉ thêm được học sinh
                                   ->first();

        if (!$student) {
            return back()->with('error', 'Email này không phải là tài khoản học sinh.');
        }

        // 3. Kiểm tra xem học sinh đã có lớp chưa
        if ($student->classroom_id) {
            return back()->with('error', 'Học sinh này đã thuộc về một lớp khác rồi.');
        }

        // 4. Cập nhật lớp cho học sinh
        $student->classroom_id = $classroom->id;
        $student->save();

        return back()->with('success', 'Đã thêm học sinh ' . $student->name . ' vào lớp.');
    }

    // Xử lý nhập file Excel
public function importStudents(Request $request, $classId)
{
    // Validate file: Phải là file excel và dung lượng < 2MB
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048', 
    ]);

    try {
        // Gọi hàm Import và truyền classId vào
        Excel::import(new StudentsImport($classId), $request->file('file'));

        return back()->with('success', 'Nhập danh sách học sinh thành công!');
    } catch (\Exception $e) {
        // Bắt lỗi nếu file sai định dạng hoặc lỗi DB
        return back()->with('error', 'Lỗi nhập dữ liệu: ' . $e->getMessage());
    }
}
}