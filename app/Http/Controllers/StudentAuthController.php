<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StudentAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('home');
        }

        return view('front.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required'],
        ]);

        $identifier = trim($request->login);

        // Try national_id first, then email
        $student = Student::where('national_id', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (! $student || ! Hash::check($request->password, $student->password)) {
            return back()
                ->withInput($request->only('login'))
                ->withErrors(['login' => __('front.auth_login_error')]);
        }

        if (! $student->is_active) {
            return back()
                ->withInput($request->only('login'))
                ->withErrors(['login' => app()->getLocale() === 'ar'
                    ? 'الحساب موقوف، تواصل مع الإدارة'
                    : 'Account is suspended. Contact admin.']);
        }

        Auth::guard('student')->login($student, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function showRegister()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('home');
        }

        return view('front.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:200'],
            'national_id' => ['required', 'string', 'max:50', 'unique:students,national_id'],
            'email'       => ['nullable', 'email', 'max:200', 'unique:students,email'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'password'    => ['required', 'confirmed', Password::min(8)],
            'terms'       => ['accepted'],
        ]);

        $student = Student::create([
            'name'        => $validated['name'],
            'national_id' => $validated['national_id'],
            'email'       => $validated['email'] ?? null,
            'phone'       => $validated['phone'] ?? null,
            'password'    => $validated['password'],
            'is_active'   => true,
        ]);

        Auth::guard('student')->login($student);
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('register_success', __('front.auth_register_success'));
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
