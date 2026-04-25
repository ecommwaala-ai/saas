<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\CompensationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\Admin\SalesController as AdminSalesController;
use App\Http\Controllers\Agent\AttendanceController as AgentAttendanceController;
use App\Http\Controllers\Agent\EarningsController;
use App\Http\Controllers\Agent\LeaveController as AgentLeaveController;
use App\Http\Controllers\Agent\SalesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;
use App\Http\Controllers\SuperAdmin\TenantController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::view('/users', 'placeholder', ['title' => 'Users'])->middleware('role:super_admin,admin')->name('users.index');
    Route::view('/sales', 'placeholder', ['title' => 'Sales'])->name('sales.index');
    Route::view('/attendance', 'placeholder', ['title' => 'Attendance'])->name('attendance.index');
});

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super')
    ->name('super.')
    ->group(function (): void {
        Route::get('/dashboard', [TenantController::class, 'dashboard'])->name('dashboard');
        Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
        Route::patch('/tenants/{tenant}/status', [TenantController::class, 'updateStatus'])->name('tenants.status');
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/{tenant_id}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
        Route::post('/subscriptions/{tenant_id}', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
        Route::get('/agents/create', [AgentController::class, 'create'])->name('agents.create');
        Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
        Route::patch('/agents/{id}', [AgentController::class, 'update'])->name('agents.update');
        Route::delete('/agents/{id}', [AgentController::class, 'destroy'])->name('agents.destroy');
        Route::get('/sales', [AdminSalesController::class, 'index'])->name('sales.index');
        Route::patch('/sales/{id}/approve', [AdminSalesController::class, 'approve'])->name('sales.approve');
        Route::patch('/sales/{id}/reject', [AdminSalesController::class, 'reject'])->name('sales.reject');
        Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/leaves', [AdminLeaveController::class, 'index'])->name('leaves.index');
        Route::patch('/leaves/{id}/approve', [AdminLeaveController::class, 'approve'])->name('leaves.approve');
        Route::patch('/leaves/{id}/reject', [AdminLeaveController::class, 'reject'])->name('leaves.reject');
        Route::get('/compensation', [CompensationController::class, 'index'])->name('compensation.index');
        Route::get('/compensation/{agent_id}', [CompensationController::class, 'show'])->name('compensation.show');
        Route::post('/compensation/{agent_id}', [CompensationController::class, 'store'])->name('compensation.store');
    });

Route::middleware(['auth', 'role:agent'])
    ->prefix('agent')
    ->name('agent.')
    ->group(function (): void {
        Route::get('/dashboard', [SalesController::class, 'dashboard'])->name('dashboard');
        Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
        Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
        Route::patch('/sales/{id}', [SalesController::class, 'update'])->name('sales.update');
        Route::delete('/sales/{id}', [SalesController::class, 'destroy'])->name('sales.destroy');
        Route::get('/attendance', [AgentAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/clock-in', [AgentAttendanceController::class, 'clockIn'])->name('attendance.clock-in');
        Route::post('/attendance/clock-out', [AgentAttendanceController::class, 'clockOut'])->name('attendance.clock-out');
        Route::get('/leaves', [AgentLeaveController::class, 'index'])->name('leaves.index');
        Route::get('/leaves/create', [AgentLeaveController::class, 'create'])->name('leaves.create');
        Route::post('/leaves', [AgentLeaveController::class, 'store'])->name('leaves.store');
        Route::get('/earnings', EarningsController::class)->name('earnings.index');
    });

require __DIR__.'/auth.php';
