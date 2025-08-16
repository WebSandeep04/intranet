<?php
namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

   public function login(Request $request)
{
    $credentials = $request->only('email', 'password');
    $remember = $request->filled('remember');

    if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate();
        session([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name
        ]);
         if (Auth::user()->role_id == 3) {
            return redirect('/superadmindashboard')->with('success', 'Welcome Super Admin!');
        }
        return redirect()->intended('/dashboard')->with('success', 'Login successful! Welcome back, ' . Auth::user()->name);
    }

    return back()->withErrors([
        'email' => 'Invalid credentials provided.',
    ]);
}


public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    $request->session()->forget(['user_id', 'user_name']);
    return redirect('/login');
}


public function showRegisterForm(){
    return view('auth.register');
}

   public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'tenant_code' => 'required|string|exists:tenants,tenant_code',
        ]);
        $tenant = Tenant::where('tenant_code', $request->tenant_code)->first();

       User::create([
        'name'      => $request->name,
        'email'     => $request->email,
        'password'  => Hash::make($request->password),
        'tenant_id' => $tenant->id,
    ]);

        return redirect('/login')->with('success', 'Registration successful. Please login.');
    }
}
