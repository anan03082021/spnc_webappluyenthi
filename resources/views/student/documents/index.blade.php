<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thư viện Tài liệu - SPNC Edutech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f3f4f6; color: #4a5568; }
        
        /* Navbar Minimal */
        .navbar { background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

        /* Document Card */
        .doc-card {
            background: white; border: none; border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: all 0.3s ease;
            height: 100%; position: relative; overflow: hidden;
        }
        .doc-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        
        .icon-wrapper {
            height: 120px; display: flex; align-items: center; justify-content: center;
            background-color: #f8fafc; border-bottom: 1px solid #edf2f7;
        }
        .icon-file { font-size: 50px; }
        
        .btn-download {
            background-color: #ebf8ff; color: #3182ce; font-weight: 700; border: none; width: 100%;
            padding: 10px; border-radius: 10px; transition: 0.3s;
        }
        .btn-download:hover { background-color: #3182ce; color: white; }

        /* Categories */
        .nav-pills .nav-link {
            color: #4a5568; background: white; border: 1px solid #e2e8f0; margin-right: 10px; border-radius: 50px; padding: 8px 20px; font-weight: 600;
        }
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/dashboard') }}">
                <i class="fa-solid fa-graduation-cap text-primary me-2"></i>SPNC<span class="text-primary">Edu</span>
            </a>
            <a href="{{ url('/dashboard') }}" class="btn btn-light btn-sm rounded-pill fw-bold text-muted border">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </nav>

    <div class="container py-3">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">Thư viện Tài liệu</h2>
            <p class="text-muted">Tổng hợp giáo trình, đề cương và tài liệu ôn thi chất lượng cao</p>
        </div>

        <div class="d-flex justify-content-center mb-5 flex-wrap gap-2">
            <a href="{{ route('student.documents.index') }}" class="btn btn-outline-secondary rounded-pill px-4 {{ !request('category_id') ? 'active bg-primary text-white border-primary' : '' }}">
                Tất cả
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('student.documents.index', ['category_id' => $cat->id]) }}" 
                   class="btn btn-outline-secondary rounded-pill px-4 {{ request('category_id') == $cat->id ? 'active bg-primary text-white border-primary' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        <div class="row g-4">
            @forelse($documents as $doc)
                @php
                    // Xử lý icon dựa trên đuôi file (giả lập)
                    $ext = pathinfo($doc->file_path, PATHINFO_EXTENSION);
                    $iconClass = match(strtolower($ext)) {
                        'pdf' => 'fa-file-pdf text-danger',
                        'doc', 'docx' => 'fa-file-word text-primary',
                        'ppt', 'pptx' => 'fa-file-powerpoint text-warning',
                        'zip', 'rar' => 'fa-file-zipper text-secondary',
                        default => 'fa-file-lines text-info'
                    };
                @endphp
                <div class="col-md-3 col-sm-6">
                    <div class="doc-card">
                        <div class="icon-wrapper">
                            <i class="fa-regular {{ $iconClass }} icon-file"></i>
                        </div>
                        <div class="card-body p-3">
                            <div class="badge bg-light text-secondary mb-2 border">
                                {{ $doc->category->name ?? 'Tài liệu chung' }}
                            </div>
                            <h6 class="card-title fw-bold text-truncate mb-3" title="{{ $doc->title }}">
                                {{ $doc->title }}
                            </h6>
                            <div class="d-flex justify-content-between align-items-center mb-3 text-muted small">
                                <span><i class="fa-regular fa-clock me-1"></i> {{ $doc->created_at->format('d/m/Y') }}</span>
                                <span><i class="fa-solid fa-download me-1"></i> Tải về</span>
                            </div>
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn-download text-decoration-none text-center d-block">
                                <i class="fa-solid fa-cloud-arrow-down me-2"></i>Xem / Tải
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-folder-4048243-3353215.png" width="150" style="opacity: 0.6">
                    <h5 class="mt-3 text-muted">Chưa có tài liệu nào trong mục này.</h5>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>