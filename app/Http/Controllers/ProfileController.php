<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $request->user()->fill($request->validated());

    if ($request->user()->isDirty('email')) {
        $request->user()->email_verified_at = null;
    }

    // --- ĐOẠN CODE THÊM MỚI BẮT ĐẦU ---
    
    // 1. Xử lý lưu các trường phụ (Lớp, SĐT)
    // Lưu ý: Cần thêm rule validate trong ProfileUpdateRequest hoặc validate trực tiếp ở đây
    $request->user()->phone = $request->input('phone');
    $request->user()->class_name = $request->input('class_name');

    // 2. Xử lý Upload Avatar
    if ($request->hasFile('avatar')) {
        // Xóa ảnh cũ
        if ($request->user()->avatar && Storage::disk('public')->exists($request->user()->avatar)) {
            Storage::disk('public')->delete($request->user()->avatar);
        }
        // Lưu ảnh mới
        $path = $request->file('avatar')->store('avatars', 'public');
        $request->user()->avatar = $path;
    }
    // --- KẾT THÚC ĐOẠN CODE THÊM MỚI ---

    $request->user()->save();

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
