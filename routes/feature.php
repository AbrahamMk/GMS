<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MembershipPlanController;
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
        Route::get('/members', function () {
            return Inertia::render('Members/Index', [
                'summary' => [
                    'members' => Member::query()->count(),
                    'active' => Member::query()->active()->count(),
                    'memberships' => Membership::query()->active()->count(),
                ],
                'members' => Member::query()
                    ->latest()
                    ->limit(12)
                    ->get()
                    ->map(fn (Member $member): array => [
                        'id' => $member->id,
                        'member_code' => $member->member_code,
                        'first_name' => $member->first_name,
                        'last_name' => $member->last_name,
                        'phone' => $member->phone,
                        'email' => $member->email,
                        'status' => $member->status,
                    ])
                    ->values(),
            ]);
        })->middleware('permission:view members')->name('portal.members');
        Route::get('/attendance', function () {
            $now = app(BranchContext::class)->now();

            return Inertia::render('Attendance/Index', [
                'summary' => [
                    'todayCheckIns' => AttendanceSession::query()->whereDate('checked_in_at', $now)->count(),
                    'openSessions' => AttendanceSession::query()->where('status', 'open')->count(),
                ],
                'recentSessions' => AttendanceSession::query()
                    ->with('member')
                    ->latest('checked_in_at')
                    ->limit(10)
                    ->get()
                    ->map(fn (AttendanceSession $session): array => [
                        'id' => $session->id,
                        'member' => $session->member?->only(['id', 'member_code', 'first_name', 'last_name']),
                        'checked_in_at' => $session->checked_in_at,
                        'checked_out_at' => $session->checked_out_at,
                        'status' => $session->status,
                    ])
                    ->values(),
            ]);
        })->middleware('permission:view attendance')->name('portal.attendance');
        Route::get('/memberships', function (\Illuminate\Http\Request $request) {
            $user = $request->user();
            $member = $user?->member()->first();

            return Inertia::render('Memberships/Index', [
                'plans' => MembershipPlan::query()
                    ->active()
                    ->latest()
                    ->limit(12)
                    ->get()
                    ->map(fn (MembershipPlan $plan): array => [
                        'id' => $plan->id,
                        'code' => $plan->code,
                        'name' => $plan->name,
                        'type' => $plan->type,
                        'price' => $plan->price,
                        'currency' => $plan->currency,
                        'duration_days' => $plan->duration_days,
                        'visit_limit' => $plan->visit_limit,
                    ])
                    ->values(),
                'activeMemberships' => Membership::query()
                    ->with(['member', 'plan'])
                    ->when($user?->hasRole('member') === true, static function ($query) use ($member): void {
                        if ($member !== null) {
                            $query->where('member_id', $member->getKey());
                        }
                    })
                    ->active()
                    ->latest('starts_at')
                    ->limit(10)
                    ->get()
                    ->map(fn (Membership $membership): array => [
                        'id' => $membership->id,
                        'status' => $membership->status,
                        'remaining_visits' => $membership->remaining_visits,
                        'starts_at' => $membership->starts_at,
                        'ends_at' => $membership->ends_at,
                        'member' => $membership->member?->only(['id', 'member_code', 'first_name', 'last_name']),
                        'plan' => $membership->plan?->only(['id', 'name', 'type', 'price', 'currency']),
                    ])
                    ->values(),
            ]);
        })->middleware('permission:view memberships')->name('portal.memberships');
        Route::get('/inventory', function () {
            return Inertia::render('Inventory/Index', [
                'summary' => [
                    'stockItems' => StockItem::query()->count(),
                    'lowStockItems' => StockItem::query()->whereColumn('current_stock', '<=', 'reorder_level')->count(),
                    'equipmentItems' => EquipmentItem::query()->count(),
                ],
                'stockItems' => StockItem::query()
                    ->latest()
                    ->limit(8)
                    ->get()
                    ->map(fn (StockItem $item): array => [
                        'id' => $item->id,
                        'sku' => $item->sku,
                        'name' => $item->name,
                        'current_stock' => $item->current_stock,
                        'reorder_level' => $item->reorder_level,
                        'unit' => $item->unit,
                        'is_active' => $item->is_active,
                    ])
                    ->values(),
                'equipmentItems' => EquipmentItem::query()
                    ->latest()
                    ->limit(8)
                    ->get()
                    ->map(fn (EquipmentItem $item): array => [
                        'id' => $item->id,
                        'asset_tag' => $item->asset_tag,
                        'name' => $item->name,
                        'status' => $item->status,
                        'location' => $item->location,
                        'condition' => $item->condition,
                    ])
                    ->values(),
            ]);
        })->middleware('permission:view inventory')->name('portal.inventory');
        Route::get('/reports', function () {
            $now = app(BranchContext::class)->now();

            return Inertia::render('Reports/Index', [
                'summary' => [
                    'members' => Member::query()->count(),
                    'activeMemberships' => Membership::query()->active()->count(),
                    'todayCheckIns' => AttendanceSession::query()->whereDate('checked_in_at', $now)->count(),
                    'bookings' => ClassBooking::query()->count(),
                ],
                'trend' => [
                    'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    'values' => [12, 18, 21, 16, 28, 35, 31],
                ],
            ]);
        })->middleware('permission:view reports')->name('portal.reports');
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
