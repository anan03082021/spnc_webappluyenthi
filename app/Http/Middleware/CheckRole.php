<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
{
    if (!Auth::check()) {
        return redirect('/login');
    }

    $user = Auth::user();

    // Kiểm tra xem role của user có nằm trong danh sách các role được phép không
    // Ví dụ gọi: middleware('role:admin,teacher')
    if (in_array($user->role, $roles)) {
        return $next($request);
    }

    // Nếu không đúng quyền, trả về lỗi 403 hoặc chuyển hướng
    return abort(403, 'Bạn không có quyền truy cập trang này.');
}
}
