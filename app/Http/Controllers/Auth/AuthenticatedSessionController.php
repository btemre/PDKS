<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('admin.admin_login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Login alanının email mi yoksa username mi olduğunu belirle
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Auth attempt
        if (!Auth::attempt([$loginType => $request->login, 'password' => $request->password], $request->filled('remember'))) {
            throw ValidationException::withMessages([
                'login' => __('Girdiğiniz bilgiler yanlış.'),
            ]);
        }

        // Session yenile
        $request->session()->regenerate();

        // Role bazlı yönlendirme
        $url = '';
        if ($request->user()->role === 'admin') {
            $url = 'admin/dashboard';
        } elseif ($request->user()->role === 'instructor') {
            $url = 'instructor/dashboard';
        } elseif ($request->user()->role === 'user') {
            $url = '/dashboard';
        }

        return redirect()->intended($url);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
