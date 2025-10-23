<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index()
    {
        try {
            $employees = Employee::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Get statistics
            $stats = [
                'total' => Employee::count(),
                'active' => Employee::where('status', 'active')->count(),
                'on_leave' => Employee::where('status', 'on_leave')->count(),
                'inactive' => Employee::where('status', 'inactive')->count(),
                'with_accounts' => Employee::whereHas('user')->count(),
            ];

            return view('admin.employees.index', compact('employees', 'stats'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load employees: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        try {
            return view('admin.employees.create');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load create form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20|unique:employees,phone',
                'address' => 'nullable|string|max:500',
                'status' => 'required|in:active,inactive,on_leave',
                'shift_start' => 'nullable|date_format:H:i',
                'shift_end' => 'nullable|date_format:H:i|after:shift_start',
            ]);

            DB::beginTransaction();

            $employee = Employee::create($validated);

            // Log the action
            AuditLog::logAction(
                'create',
                'employees',
                $employee->emp_id, // Use emp_id instead of employee_id
                null,
                $employee->toArray(),
                "Created employee: {$employee->full_name}"
            );

            DB::commit();

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create employee: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        try {
            $employee->load('user');
            return view('admin.employees.show', compact('employee'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load employee details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        try {
            $employee->load('user');
            return view('admin.employees.edit', compact('employee'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('employees', 'phone')->ignore($employee->emp_id, 'emp_id')
                ],
                'address' => 'nullable|string|max:500',
                'status' => 'required|in:active,inactive,on_leave',
                'shift_start' => 'nullable|date_format:H:i',
                'shift_end' => 'nullable|date_format:H:i|after:shift_start',
            ]);

            DB::beginTransaction();

            $oldValues = $employee->toArray();
            $employee->update($validated);
            $newValues = $employee->fresh()->toArray();

            // Log the action
            AuditLog::logAction(
                'update',
                'employees',
                $employee->emp_id, // Use emp_id instead of employee_id
                $oldValues,
                $newValues,
                "Updated employee: {$employee->full_name}"
            );

            DB::commit();

            return redirect()->route('admin.employees.show', $employee)
                ->with('success', 'Employee updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update employee: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            // Check if employee has a user account
            if ($employee->user) {
                return back()->with('error', 'Cannot delete employee with an active user account. Delete the user account first.');
            }

            DB::beginTransaction();

            $employeeId = $employee->emp_id; // Store ID before deletion
            $employeeData = $employee->toArray();
            $employeeName = $employee->full_name;
            
            $employee->delete();

            // Log the action
            AuditLog::logAction(
                'delete',
                'employees',
                $employeeId, // Use stored ID
                $employeeData,
                null,
                "Deleted employee: {$employeeName}"
            );

            DB::commit();

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }

    /**
     * Toggle employee status (active/inactive).
     */
    public function toggleStatus(Employee $employee)
    {
        try {
            DB::beginTransaction();

            $oldStatus = $employee->status;
            $newStatus = $employee->status === 'active' ? 'inactive' : 'active';
            
            $employee->update(['status' => $newStatus]);

            // Log the action
            AuditLog::logAction(
                'update',
                'employees',
                $employee->employee_id,
                ['status' => $oldStatus],
                ['status' => $newStatus],
                "Changed employee status: {$employee->full_name} from {$oldStatus} to {$newStatus}"
            );

            DB::commit();

            return back()->with('success', "Employee status updated to {$newStatus}!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update employee status: ' . $e->getMessage());
        }
    }
}