<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Redirect to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', function () {
    \Auth::logout();
    return redirect()->route('login');
})->name('logout');

// Register routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Test route (no middleware)
Route::get('/test-admin-simple', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return "Logged in as: {$user->username}, Role: {$user->role}, IsAdmin: " . ($user->isAdmin() ? 'YES' : 'NO');
    }
    return "Not logged in";
});

// Direct admin dashboard test (no middleware)
Route::get('/test-dashboard', function () {
    if (Auth::check()) {
        return view('admin.dashboard.index');
    }
    return "Not logged in - redirecting to login";
});

// Admin dashboard routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/api/system-overview', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getSystemOverview'])->name('admin.api.system-overview');
    
    // User Management
    Route::resource('admin/users', App\Http\Controllers\Admin\UserManagementController::class, ['as' => 'admin']);
    Route::post('/admin/users/{user}/reset-password', [App\Http\Controllers\Admin\UserManagementController::class, 'resetPassword'])->name('admin.users.reset-password');
    Route::patch('/admin/users/{user}/toggle-status', [App\Http\Controllers\Admin\UserManagementController::class, 'toggleStatus'])->name('admin.users.toggle-status');

    // Pending User Approval
    Route::get('/admin/users/pending', [App\Http\Controllers\Admin\UserController::class, 'pending'])->name('admin.users.pending');
    Route::patch('/admin/users/{user}/approve', [App\Http\Controllers\Admin\UserController::class, 'approve'])->name('admin.users.approve');
    Route::patch('/admin/users/{user}/reject', [App\Http\Controllers\Admin\UserController::class, 'reject'])->name('admin.users.reject');
    
    // Employee Management
    Route::resource('admin/employees', App\Http\Controllers\Admin\EmployeeController::class, ['as' => 'admin']);
    Route::patch('/admin/employees/{employee}/toggle-status', [App\Http\Controllers\Admin\EmployeeController::class, 'toggleStatus'])->name('admin.employees.toggle-status');
    
    // Product Management
    Route::resource('admin/products', App\Http\Controllers\Admin\ProductManagementController::class, ['as' => 'admin']);
    Route::post('/admin/products/{product}/adjust-stock', [App\Http\Controllers\Admin\ProductManagementController::class, 'adjustStock'])->name('admin.products.adjust-stock');
    Route::post('/admin/products/bulk-update', [App\Http\Controllers\Admin\ProductManagementController::class, 'bulkUpdate'])->name('admin.products.bulk-update');
    Route::get('/admin/products/export/pdf', [App\Http\Controllers\Admin\ProductManagementController::class, 'export'])->name('admin.products.export');
    
    // Category Management
    Route::resource('admin/categories', App\Http\Controllers\Admin\CategoryManagementController::class, ['as' => 'admin']);
    Route::patch('/admin/categories/{category}/toggle-status', [App\Http\Controllers\Admin\CategoryManagementController::class, 'toggleStatus'])->name('admin.categories.toggle-status');
    
    // Inventory Management
    Route::resource('admin/inventory', App\Http\Controllers\Admin\InventoryManagementController::class, ['as' => 'admin']);
    Route::get('/admin/inventory/low-stock/alerts', [App\Http\Controllers\Admin\InventoryManagementController::class, 'lowStockAlerts'])->name('admin.inventory.low-stock-alerts');
    Route::post('/admin/inventory/bulk-adjust', [App\Http\Controllers\Admin\InventoryManagementController::class, 'bulkAdjust'])->name('admin.inventory.bulk-adjust');

    // Production Orders
    Route::resource('admin/productions', App\Http\Controllers\Admin\ProductionOrderController::class, ['as' => 'admin']);
    Route::patch('/admin/productions/{production}/start', [App\Http\Controllers\Admin\ProductionOrderController::class, 'start'])->name('admin.productions.start');
    Route::patch('/admin/productions/{production}/complete', [App\Http\Controllers\Admin\ProductionOrderController::class, 'complete'])->name('admin.productions.complete');
    Route::patch('/admin/productions/{production}/cancel', [App\Http\Controllers\Admin\ProductionOrderController::class, 'cancel'])->name('admin.productions.cancel');

    // Sales Management
    Route::resource('admin/sales', App\Http\Controllers\Admin\SaleManagementController::class, ['as' => 'admin'])->only(['index','create','store','show','edit','update','destroy']);
    Route::patch('/admin/sales/{sale}/cancel', [App\Http\Controllers\Admin\SaleManagementController::class, 'cancel'])->name('admin.sales.cancel');

    // Sales Reports
    Route::get('/admin/sales-reports', [App\Http\Controllers\Admin\SalesReportController::class, 'index'])->name('admin.sales-reports.index');
    Route::get('/admin/sales-reports/detailed', [App\Http\Controllers\Admin\SalesReportController::class, 'show'])->name('admin.sales-reports.show');
    Route::get('/admin/sales-reports/export', [App\Http\Controllers\Admin\SalesReportController::class, 'export'])->name('admin.sales-reports.export');
    
    // Analytics Dashboard
    Route::get('/admin/analytics', [App\Http\Controllers\Admin\AnalyticsDashboardController::class, 'index'])->name('admin.analytics.index');

    // Inventory Reports
    Route::get('/admin/inventory-reports', [App\Http\Controllers\Admin\InventoryReportController::class, 'index'])->name('admin.inventory-reports.index');
    Route::get('/admin/inventory-reports/detailed', [App\Http\Controllers\Admin\InventoryReportController::class, 'show'])->name('admin.inventory-reports.show');
    Route::get('/admin/inventory-reports/export', [App\Http\Controllers\Admin\InventoryReportController::class, 'export'])->name('admin.inventory-reports.export');
    
    // Audit Logs
    Route::resource('admin/audit-logs', App\Http\Controllers\Admin\AuditLogController::class, ['as' => 'admin']);
    Route::get('/admin/audit-logs/export/csv', [App\Http\Controllers\Admin\AuditLogController::class, 'export'])->name('admin.audit-logs.export');
    Route::get('/admin/audit-logs/export/pdf', [App\Http\Controllers\Admin\AuditLogController::class, 'exportPdf'])->name('admin.audit-logs.export-pdf');

    // System Settings
    Route::get('/admin/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('/admin/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.settings.update');

    // Backup & Restore
    Route::get('/admin/backup', [App\Http\Controllers\Admin\BackupController::class, 'index'])->name('admin.backup.index');
    Route::get('/admin/backup/download', [App\Http\Controllers\Admin\BackupController::class, 'download'])->name('admin.backup.download');
    Route::post('/admin/backup/restore', [App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('admin.backup.restore');
});

// Baker dashboard routes
Route::middleware(['auth', 'role:baker'])->group(function () {
    Route::get('/dashboard/baker', [App\Http\Controllers\Baker\DashboardController::class, 'index'])->name('baker.dashboard');
    
    Route::post('/baker/profile/complete', [App\Http\Controllers\Baker\DashboardController::class, 'completeProfile'])->name('baker.profile.complete');
    
    // Production Management
    Route::resource('baker/production', App\Http\Controllers\Baker\ProductionController::class)->names([
        'index' => 'baker.production.index',
        'create' => 'baker.production.create',
        'store' => 'baker.production.store',
        'show' => 'baker.production.show',
        'edit' => 'baker.production.edit',
        'update' => 'baker.production.update',
        'destroy' => 'baker.production.destroy',
    ]);
    
    // Inventory Management
    Route::get('baker/inventory', [App\Http\Controllers\Baker\InventoryController::class, 'index'])->name('baker.inventory.index');
    Route::get('baker/inventory/{product}', [App\Http\Controllers\Baker\InventoryController::class, 'show'])->name('baker.inventory.show');
});

// Cashier dashboard routes  
Route::middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/dashboard/cashier', [App\Http\Controllers\Cashier\CashierDashboardController::class, 'index'])->name('cashier.dashboard');
    
    // POS
    Route::get('/cashier/pos', [App\Http\Controllers\Cashier\POSController::class, 'index'])->name('cashier.pos.index');
    Route::post('/cashier/pos/checkout', [App\Http\Controllers\Cashier\POSController::class, 'checkout'])->name('cashier.pos.checkout');
    
    // Sales History
    Route::get('/cashier/sales', [App\Http\Controllers\Cashier\SalesHistoryController::class, 'index'])->name('cashier.sales.index');
    Route::get('/cashier/sales/{sale}', [App\Http\Controllers\Cashier\SalesHistoryController::class, 'show'])->name('cashier.sales.show');
    Route::get('/cashier/sales/{sale}/receipt/pdf', [App\Http\Controllers\Cashier\ReceiptController::class, 'pdf'])->name('cashier.sales.receipt.pdf');
    
    Route::post('/cashier/profile/complete', function(\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);
        
        $user = Auth::user();
        if ($user->employee) {
            $user->employee->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'middle_name' => $validated['middle_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'status' => 'active', // Mark as active when profile is complete
            ]);
        }
        
        return redirect()->route('cashier.dashboard')->with('success', 'Profile completed successfully!');
    })->name('cashier.profile.complete');
});
