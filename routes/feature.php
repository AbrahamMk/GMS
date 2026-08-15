<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PortalController;
use App\Models\AttendanceSession;
use App\Models\ClassSession;
use App\Models\ClassBooking;
use App\Models\EquipmentItem;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\StockItem;
use App\Support\BranchContext;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'permission:access portal'])->group(function (): void {
    Route::prefix('portal')->group(function (): void {
        Route::get('/', [PortalController::class, 'dashboard'])->name('portal.dashboard');
        Route::get('/profile', [PortalController::class, 'profile'])->name('portal.profile');
        Route::put('/profile', [PortalController::class, 'updateProfile'])->name('portal.profile.update');
        Route::get('/bookings', [PortalController::class, 'bookings'])->name('portal.bookings');
        Route::post('/bookings', [PortalController::class, 'bookClass'])->name('portal.bookings.store');
        Route::delete('/bookings', [PortalController::class, 'cancelBooking'])->name('portal.bookings.destroy');
        Route::get('/members', [MemberController::class, 'index'])->middleware('permission:view members')->name('portal.members');
        Route::get('/members/{member}', [MemberController::class, 'show'])->middleware('permission:view members')->name('portal.members.show');
        Route::get('/attendance', function () {
            $now = now();

            return Inertia::render('Attendance/Index', [
                'summary' => [
                    'todayCheckIns' => AttendanceSession::withoutGlobalScope(\App\Scopes\BranchScope::class)->whereDate('checked_in_at', $now)->count(),
                    'openSessions'  => AttendanceSession::withoutGlobalScope(\App\Scopes\BranchScope::class)->where('status', 'open')->count(),
                ],
                'recentSessions' => AttendanceSession::withoutGlobalScope(\App\Scopes\BranchScope::class)
                    ->with('member')
                    ->latest('checked_in_at')
                    ->limit(10)
                    ->get()
                    ->map(fn (AttendanceSession $session): array => [
                        'id'             => $session->id,
                        'member'         => $session->member?->only(['id', 'member_code', 'first_name', 'last_name']),
                        'checked_in_at'  => $session->checked_in_at,
                        'checked_out_at' => $session->checked_out_at,
                        'status'         => $session->status,
                    ])
                    ->values(),
            ]);
        })->middleware('permission:view attendance')->name('portal.attendance');
        Route::get('/memberships', [MembershipPlanController::class, 'index'])->middleware('permission:view memberships')->name('portal.memberships');
        Route::get('/inventory', function () {
            return Inertia::render('Inventory/Index', [
                'summary' => [
                    'stockItems'     => StockItem::withoutGlobalScope(\App\Scopes\BranchScope::class)->count(),
                    'lowStockItems'  => StockItem::withoutGlobalScope(\App\Scopes\BranchScope::class)->whereColumn('current_stock', '<=', 'reorder_level')->count(),
                    'equipmentItems' => EquipmentItem::withoutGlobalScope(\App\Scopes\BranchScope::class)->count(),
                ],
                'stockItems' => StockItem::withoutGlobalScope(\App\Scopes\BranchScope::class)
                    ->latest()
                    ->limit(8)
                    ->get()
                    ->map(fn (StockItem $item): array => [
                        'id'            => $item->id,
                        'sku'           => $item->sku,
                        'name'          => $item->name,
                        'current_stock' => $item->current_stock,
                        'reorder_level' => $item->reorder_level,
                        'unit'          => $item->unit,
                        'is_active'     => $item->is_active,
                    ])
                    ->values(),
                'equipmentItems' => EquipmentItem::withoutGlobalScope(\App\Scopes\BranchScope::class)
                    ->latest()
                    ->limit(8)
                    ->get()
                    ->map(fn (EquipmentItem $item): array => [
                        'id'        => $item->id,
                        'asset_tag' => $item->asset_tag,
                        'name'      => $item->name,
                        'status'    => $item->status,
                        'location'  => $item->location,
                        'condition' => $item->condition,
                    ])
                    ->values(),
            ]);
        })->middleware('permission:view inventory')->name('portal.inventory');
        Route::get('/finance', function () {
            $now = now();

            return Inertia::render('Reports/Index', [
                'summary' => [
                    'members'           => Member::withoutGlobalScope(\App\Scopes\BranchScope::class)->count(),
                    'activeMemberships' => Membership::withoutGlobalScope(\App\Scopes\BranchScope::class)->active()->count(),
                    'todayCheckIns'     => AttendanceSession::withoutGlobalScope(\App\Scopes\BranchScope::class)->whereDate('checked_in_at', $now)->count(),
                    'bookings'          => ClassBooking::withoutGlobalScope(\App\Scopes\BranchScope::class)->count(),
                ],
                'trend' => [
                    'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    'values' => [12, 18, 21, 16, 28, 35, 31],
                ],
            ]);
        })->middleware('permission:view reports')->name('portal.finance');

        Route::get('/trainers', [\App\Http\Controllers\TrainerController::class, 'index'])->name('portal.trainers');
        Route::get('/trainers/{trainer}', [\App\Http\Controllers\TrainerController::class, 'show'])->name('portal.trainers.show');
        Route::post('/trainers', [\App\Http\Controllers\TrainerController::class, 'store'])->name('portal.trainers.store');
        Route::put('/trainers/{trainer}', [\App\Http\Controllers\TrainerController::class, 'update'])->name('portal.trainers.update');
        Route::delete('/trainers/{trainer}', [\App\Http\Controllers\TrainerController::class, 'destroy'])->name('portal.trainers.destroy');

        Route::get('/classes', [\App\Http\Controllers\AdminClassController::class, 'index'])->name('portal.classes');
        Route::post('/classes', [\App\Http\Controllers\AdminClassController::class, 'store'])->name('portal.classes.store');
        Route::put('/classes/{gymClass}', [\App\Http\Controllers\AdminClassController::class, 'update'])->name('portal.classes.update');
        Route::delete('/classes/{gymClass}', [\App\Http\Controllers\AdminClassController::class, 'destroy'])->name('portal.classes.destroy');

        Route::get('/payments', [PaymentController::class, 'index'])->name('portal.payments');

        Route::get('/workouts', function () {
            return Inertia::render('Workouts/Index', []);
        })->name('portal.workouts');
    });

    Route::prefix('attendance')->middleware('permission:manage attendance')->group(function (): void {
        Route::post('check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
        Route::post('check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    });

    Route::prefix('membership-plans')->middleware('permission:manage membership plans')->group(function (): void {
        Route::get('/', [MembershipPlanController::class, 'index'])->name('membership-plans.index');
        Route::post('/', [MembershipPlanController::class, 'store'])->name('membership-plans.store');
        Route::put('{membershipPlan}', [MembershipPlanController::class, 'update'])->name('membership-plans.update');
    });

    Route::prefix('memberships')->middleware('permission:manage memberships')->group(function (): void {
        Route::post('activate', [MembershipController::class, 'activate'])->name('memberships.activate');
        Route::post('renew', [MembershipController::class, 'renew'])->name('memberships.renew');
    });
});
