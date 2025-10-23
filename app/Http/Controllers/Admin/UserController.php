<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * List all pending users for admin approval.
     */
    public function pending()
    {
        $pendingUsers = User::where('status', 'pending')->orderBy('created_at', 'asc')->get();
        return view('admin.users.pending', compact('pendingUsers'));
    }

    /**
     * Approve a pending user (set status to active).
     */
    public function approve(User $user)
    {
        if ($user->status !== 'pending') {
            return back()->with('error', 'User is not pending approval.');
        }
        $user->update(['status' => 'active']);
        // Optionally: send notification/email to user here
        // Audit log
        if (class_exists('App\\Models\\AuditLog')) {
            \App\Models\AuditLog::logAction('approve', 'users', $user->user_id, null, $user->toArray(), "Approved user {$user->username}");
        }
        return back()->with('success', "User '{$user->username}' approved and activated.");
    }

    /**
     * Reject a pending user (delete user and employee record).
     */
    public function reject(User $user)
    {
        if ($user->status !== 'pending') {
            return back()->with('error', 'User is not pending approval.');
        }
        $username = $user->username;
        $emp = $user->employee;
        $user->delete();
        if ($emp) { $emp->delete(); }
        // Audit log
        if (class_exists('App\\Models\\AuditLog')) {
            \App\Models\AuditLog::logAction('reject', 'users', $user->user_id, null, null, "Rejected user {$username}");
        }
        return back()->with('success', "User '{$username}' rejected and removed.");
    }

    /**
     * Display a listing of users.
     */
    public function index()
    {
        try {
            $users = User::with('employee')
                ->paginate(10);

            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load users: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        try {
            // Get employees that don't have users yet
            $availableEmployees = Employee::whereDoesntHave('user')->get();
            
            return view('admin.users.create', compact('availableEmployees'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load create form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        try {
            // Debug: Log all request data
            \Log::info('User creation request data:', $request->all());
            
            $validated = $request->validate([
                'emp_id' => 'required|exists:employees,emp_id|unique:users,emp_id',
                'username' => 'required|string|max:255|unique:users,username',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:admin,baker,cashier',
                'status' => 'in:active,inactive'
            ]);

            // Debug: Log validated data
            \Log::info('Validated user data:', $validated);

            DB::beginTransaction();

            $user = User::create([
                'emp_id' => $validated['emp_id'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'status' => $request->input('status', 'active'),
            ]);

            // Debug: Log created user
            \Log::info('Created user:', $user->toArray());

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        try {
            $user->load('employee');
            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load user details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        try {
            // Get employees that don't have users yet, plus the current user's employee
            $availableEmployees = Employee::whereDoesntHave('user')
                ->orWhere('emp_id', $user->emp_id)
                ->get();
            
            return view('admin.users.edit', compact('user', 'availableEmployees'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'emp_id' => [
                    'required',
                    'exists:employees,emp_id',
                    Rule::unique('users', 'emp_id')->ignore($user->user_id, 'user_id')
                ],
                'username' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users', 'username')->ignore($user->user_id, 'user_id')
                ],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')
                ],
                'password' => 'nullable|string|min:8|confirmed',
                'role' => 'required|in:admin,manager,baker,cashier',
                'status' => 'required|in:active,inactive'
            ]);

            DB::beginTransaction();

            $updateData = [
                'emp_id' => $validated['emp_id'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'status' => $validated['status'],
            ];

            // Only update password if provided
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deletion of the current logged-in user
            if (auth()->id() === $user->user_id) {
                return back()->with('error', 'You cannot delete your own account!');
            }

            DB::beginTransaction();

            $userName = $user->username;
            $user->delete();

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', "User '{$userName}' deleted successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        try {
            // Prevent deactivating the current logged-in user
            if (auth()->id() === $user->user_id) {
                return back()->with('error', 'You cannot deactivate your own account!');
            }

            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            $user->update([
                'status' => $newStatus
            ]);

            $statusText = $newStatus === 'active' ? 'activated' : 'deactivated';
            
            return back()->with('success', "User '{$user->username}' has been {$statusText}!");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update user status: ' . $e->getMessage());
        }
    }
}