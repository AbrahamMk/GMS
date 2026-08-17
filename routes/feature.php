<?php

use App\Http\Controllers\AdminClassController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ChapaPaymentController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\WorkoutController;
use App\Models\AttendanceSession;
use App\Models\ClassBooking;
use App\Models\EquipmentItem;
use App\Models\Member;
use App\Models\Membership;
use App\Models\StockItem;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth'])->group(function (): void {
    Route::prefix('portal')->group(function (): void {
        // Dashboard & Profile
        Route::get('/', [PortalController::class, 'dashboard'])->name('portal.dashboard');
        Route::get('/profile', [PortalController::class, 'profile'])->name('portal.profile');
        Route::put('/profile', [PortalController::class, 'updateProfile'])->name('portal.profile.update');

        // Bookings
        Route::get('/bookings', [PortalController::class, 'bookings'])->name('portal.bookings');
        Route::post('/bookings', [PortalController::class, 'bookClass'])->name('portal.bookings.store');
        Route::post('/book-class', [PortalController::class, 'bookClass'])->name('portal.book-class');
        Route::delete('/bookings', [PortalController::class, 'cancelBooking'])->name('portal.bookings.destroy');

        // Members
        Route::get('/members', [MemberController::class, 'index'])->name('portal.members');
        Route::post('/members', [MemberController::class, 'store'])->name('portal.members.store');
        Route::put('/members/{member}', [MemberController::class, 'update'])->name('portal.members.update');
        Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('portal.members.destroy');
        Route::post('/members/{member}/renew', [MemberController::class, 'renewMembership'])->name('portal.members.renew');
        Route::get('/members/{member}', [MemberController::class, 'show'])->name('portal.members.show');

        // Attendance
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
                    ->limit(15)
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
        })->name('portal.attendance');

        // Membership Plans CRUD
        Route::get('/memberships', [MembershipPlanController::class, 'index'])->name('portal.memberships');
        Route::post('/memberships', [MembershipPlanController::class, 'store'])->name('portal.memberships.store');
        Route::put('/memberships/{membershipPlan}', [MembershipPlanController::class, 'update'])->name('portal.memberships.update');
        Route::delete('/memberships/{membershipPlan}', [MembershipPlanController::class, 'destroy'])->name('portal.memberships.destroy');

        // Inventory
        Route::get('/inventory', function () {
            return Inertia::render('Inventory/Index', [
                'summary' => [
                    'stockItems'     => StockItem::withoutGlobalScope(\App\Scopes\BranchScope::class)->count(),
                    'lowStockItems'  => StockItem::withoutGlobalScope(\App\Scopes\BranchScope::class)->whereColumn('current_stock', '<=', 'reorder_level')->count(),
                    'equipmentItems' => EquipmentItem::withoutGlobalScope(\App\Scopes\BranchScope::class)->count(),
                ],
                'stockItems' => StockItem::withoutGlobalScope(\App\Scopes\BranchScope::class)
                    ->latest()
                    ->limit(20)
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
                    ->limit(20)
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
        })->name('portal.inventory');

        // Finance & Reports
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
        })->name('portal.finance');

        Route::get('/reports', function () {
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
        })->name('portal.reports');

        // Trainers CRUD
        Route::get('/trainers', [TrainerController::class, 'index'])->name('portal.trainers');
        Route::get('/trainers/{trainer}', [TrainerController::class, 'show'])->name('portal.trainers.show');
        Route::post('/trainers', [TrainerController::class, 'store'])->name('portal.trainers.store');
        Route::put('/trainers/{trainer}', [TrainerController::class, 'update'])->name('portal.trainers.update');
        Route::delete('/trainers/{trainer}', [TrainerController::class, 'destroy'])->name('portal.trainers.destroy');

        // Classes CRUD
        Route::get('/classes', [AdminClassController::class, 'index'])->name('portal.classes');
        Route::post('/classes', [AdminClassController::class, 'store'])->name('portal.classes.store');
        Route::put('/classes/{gymClass}', [AdminClassController::class, 'update'])->name('portal.classes.update');
        Route::delete('/classes/{gymClass}', [AdminClassController::class, 'destroy'])->name('portal.classes.destroy');

        // Payments & Chapa
        Route::get('/payments', [PaymentController::class, 'index'])->name('portal.payments');
        Route::post('/payments/chapa/initialize', [ChapaPaymentController::class, 'initialize'])->name('portal.payments.chapa.initialize');
        Route::get('/payments/chapa/callback/{tx_ref}', [ChapaPaymentController::class, 'callback'])->name('portal.payments.chapa.callback');
        Route::post('/payments/chapa/webhook', [ChapaPaymentController::class, 'webhook'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->name('portal.payments.chapa.webhook');

        // Workouts CRUD & Assignments
        Route::get('/workouts', [WorkoutController::class, 'index'])->name('portal.workouts');
        Route::post('/workouts', [WorkoutController::class, 'store'])->name('portal.workouts.store');
        Route::put('/workouts/{workoutPlan}', [WorkoutController::class, 'update'])->name('portal.workouts.update');
        Route::delete('/workouts/{workoutPlan}', [WorkoutController::class, 'destroy'])->name('portal.workouts.destroy');
        Route::post('/workouts/{workoutPlan}/assign', [WorkoutController::class, 'assign'])->name('portal.workouts.assign');
        Route::post('/workouts/{workoutPlan}/unassign', [WorkoutController::class, 'unassign'])->name('portal.workouts.unassign');
    });

    // Attendance Direct Endpoints
    Route::prefix('attendance')->group(function (): void {
        Route::post('check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
        Route::post('check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    });

    // Membership Plans Direct Endpoints
    Route::prefix('membership-plans')->group(function (): void {
        Route::get('/', [MembershipPlanController::class, 'index'])->name('membership-plans.index');
        Route::post('/', [MembershipPlanController::class, 'store'])->name('membership-plans.store');
        Route::put('{membershipPlan}', [MembershipPlanController::class, 'update'])->name('membership-plans.update');
    });

    // Memberships Activation & Renew
    Route::prefix('memberships')->group(function (): void {
        Route::post('activate', [MembershipController::class, 'activate'])->name('memberships.activate');
        Route::post('renew', [MembershipController::class, 'renew'])->name('memberships.renew');
    });
});
