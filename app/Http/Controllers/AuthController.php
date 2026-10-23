<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    /**
     * Show the login view.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('tickets.index');
        }

        $demoUsers = config('demo.quick_login')
            ? User::with('role:id,name,name_ar')
                ->where('is_active', true)
                ->orderBy('id')
                ->get(['id', 'name', 'email', 'role_id'])
            : [];

        return Inertia::render('Auth/Login', ['demoUsers' => $demoUsers]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt([...$credentials, 'is_active' => true], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            $user->update(['last_login_at' => now()]);

            return redirect()->intended(route('tickets.index'));
        }

        return back()->withErrors([
            'email' => 'بيانات الاعتماد غير متطابقة مع سجلاتنا.',
        ])->onlyInput('email');
    }

    /**
     * Allow guest access to demo accounts only in the explicitly enabled local environment.
     */
    public function quickLogin(Request $request, User $user)
    {
        abort_unless(config('demo.quick_login') && $user->is_active, 404);

        Auth::login($user);
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        return redirect()->route('tickets.index');
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
