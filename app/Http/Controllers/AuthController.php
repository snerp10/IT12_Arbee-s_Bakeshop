<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        
        \Log::info('Login attempt', ['credentials' => $credentials]);
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            \Log::info('User authenticated', ['user_id' => $user->user_id, 'role' => $user->role]);

            // Prevent login if status is pending
            if ($user->status === 'pending') {
                Auth::logout();
                return back()->withErrors(['username' => 'Your account is pending admin approval. Please wait for approval before logging in.']);
            }

            if ($user->isAdmin()) {
                \Log::info('User is admin, redirecting to admin dashboard');
                return redirect('/admin/dashboard');
            } elseif ($user->isBaker()) {
                \Log::info('User is baker, redirecting to baker dashboard');
                return redirect('/dashboard/baker');
            } elseif ($user->isCashier()) {
                \Log::info('User is cashier, redirecting to cashier dashboard');
                return redirect('dashboard/cashier');
            }
        } else {
            \Log::info('Authentication failed');
        }

        return back()->withErrors(['username' => 'Invalid credentials']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:baker,cashier',
        ]);

        // Create employee record with minimal info (to be completed later)
        $employee = Employee::create([
            'first_name' => '', // Empty - to be filled later
            'middle_name' => '',
            'last_name' => '',
            'phone' => '',
            'address' => '',
            'status' => 'inactive', // Mark as inactive until they fill the profile
        ]);

        // Create user record with employee relationship, status 'pending'
        $user = User::create([
            'emp_id' => $employee->emp_id,
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => 'pending',
        ]);

        // Do not log in user, show approval message
        return redirect()->route('login')->with('info', 'Your account request has been submitted and is pending admin approval. You will be notified once approved.');
    }
}
