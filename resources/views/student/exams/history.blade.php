<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử Luyện thi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8fafc; }
        .history-card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; }
        .score-box { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: 800; font-size: 1.1rem; color: white; }
    </style>
</head>
<body class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h3 class="fw-bold">Lịch sử làm bài</h3>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary rounded-pill">Dashboard</a>
        </div>

        <div class="card history-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Đề thi</th>
                            <th>Ngày thực hiện</th>
                            <th>Thời lượng</th>
                            <th class="text-center">Điểm số</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $result)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $result->exam->title ?? 'Đề đã xóa' }}</div>
                                <small class="text-muted">Độ khó: {{ $result->exam->difficulty ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <div>{{ $result->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $result->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <i class="fa-regular fa-clock me-1 text-muted"></i>
                                {{ gmdate("i:s", $result->completion_time ?? ($result->exam->duration * 60)) }} phút
                            </td>
                            <td class="text-center">
                                @php
                                    $bg = $result->score >= 8 ? 'bg-success' : ($result->score >= 5 ? 'bg-warning' : 'bg-danger');
                                @endphp
                                <div class="score-box {{ $bg }} mx-auto shadow-sm">
                                    {{ $result->score }}
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('student.exams.result', $result->id) }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                                    Xem chi tiết & Ôn tập
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5">Chưa có dữ liệu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $results->links() }}</div>
        </div>
    </div>
</body>
</html>