<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with('employee');

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by username or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get available employees for linking
        $availableEmployees = Employee::whereDoesntHave('user')->get();

        return view('admin.users.index', compact('users', 'availableEmployees'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $availableEmployees = Employee::whereDoesntHave('user')->get();
        return view('admin.users.create', compact('availableEmployees'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,baker,cashier',
            'emp_id' => 'nullable|exists:employees,emp_id|unique:users,emp_id',
            'status' => 'in:active,inactive',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'emp_id' => $request->emp_id,
                'status' => $request->input('status', 'active'),
            ]);

            AuditLog::logAction(
                'create',
                'users',
                $user->user_id,
                null,
                $user->toArray(),
                "Created user account: {$user->username} with role: {$user->role}"
            );
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('employee');
        
        // Get user's recent activities
        $recentActivities = AuditLog::where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.users.show', compact('user', 'recentActivities'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $availableEmployees = Employee::whereDoesntHave('user')
            ->orWhere('emp_id', $user->emp_id)
            ->get();

        return view('admin.users.edit', compact('user', 'availableEmployees'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        // Prevent last admin from being demoted
        if ($user->role === 'admin' && $request->role !== 'admin') {
            $adminCount = User::where('role', 'admin')->where('status', 'active')->count();
            if ($adminCount <= 1) {
                return redirect()->back()
                    ->withErrors(['role' => 'Cannot demote the last active admin.']);
            }
        }

        $request->validate([
            'username' => [
                'required',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->user_id, 'user_id'),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->user_id, 'user_id'),
            ],
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|in:admin,baker,cashier',
            'emp_id' => [
                'nullable',
                'exists:employees,emp_id',
                Rule::unique('users', 'emp_id')->ignore($user->user_id, 'user_id'),
            ],
            'status' => 'in:active,inactive',
        ]);

        DB::transaction(function () use ($request, $user) {
            $originalData = $user->toArray();

            $updateData = [
                'username' => $request->username,
                'email' => $request->email,
                'role' => $request->role,
                'emp_id' => $request->emp_id,
                'status' => $request->input('status', 'active'),
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            AuditLog::logAction(
                'update',
                'users',
                $user->user_id,
                $originalData,
                $user->getChanges(),
                "Updated user account: {$user->username}"
            );
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent last admin from being deleted
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->where('status', 'active')->count();
            if ($adminCount <= 1) {
                return redirect()->back()
                    ->withErrors(['error' => 'Cannot delete the last active admin.']);
            }
        }

        DB::transaction(function () use ($user) {
            $userData = $user->toArray();

            AuditLog::logAction(
                'delete',
                'users',
                $user->user_id,
                $userData,
                null,
                "Deleted user account: {$user->username}"
            );

            $user->delete();
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLog::logAction(
            'password_reset',
            'users',
            $user->user_id,
            null,
            null,
            "Reset password for user: {$user->username}"
        );

        return redirect()->back()
            ->with('success', 'Password reset successfully.');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        // Prevent last admin from being deactivated
        if ($user->role === 'admin' && $user->status === 'active') {
            $adminCount = User::where('role', 'admin')->where('status', 'active')->count();
            if ($adminCount <= 1) {
                return redirect()->back()
                    ->withErrors(['error' => 'Cannot deactivate the last active admin.']);
            }
        }

        $oldStatus = $user->status;
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        AuditLog::logAction(
            $newStatus === 'active' ? 'activated' : 'deactivated',
            'users',
            $user->user_id,
            ['status' => $oldStatus],
            ['status' => $newStatus],
            ($newStatus === 'active' ? 'Activated' : 'Deactivated') . " user: {$user->username}"
        );

        return redirect()->back()
            ->with('success', 'User status updated successfully.');
    }
}
