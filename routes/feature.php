<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassSessionController;
use App\Http\Controllers\EquipmentCategoryController;
use App\Http\Controllers\EquipmentItemController;
use App\Http\Controllers\GymClassController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\StockItemController;
use App\Models\AttendanceSession;
use App\Models\ClassSession;
use App\Models\ClassBooking;
use App\Models\EquipmentItem;
use App\Models\GymClass;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\StockItem;
use App\Support\BranchContext;
use Illuminate\Http\Request;
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
        Route::prefix('members')->middleware('permission:view members')->group(function (): void {
            Route::get('/', [MemberController::class, 'index'])->name('portal.members');
            Route::get('/create', function () {
                return Inertia::render('Members/Create');
            })->middleware('permission:manage members')->name('portal.members.create');
            Route::post('/', [MemberController::class, 'store'])->middleware('permission:manage members')->name('portal.members.store');
            Route::get('/{member}', [MemberController::class, 'show'])->name('portal.members.show');
            Route::get('/{member}/edit', function (Member $member) {
                return Inertia::render('Members/Edit', ['member' => $member]);
            })->middleware('permission:manage members')->name('portal.members.edit');
            Route::put('/{member}', [MemberController::class, 'update'])->middleware('permission:manage members')->name('portal.members.update');
            Route::delete('/{member}', [MemberController::class, 'destroy'])->middleware('permission:manage members')->name('portal.members.destroy');
        });
        Route::get('/attendance', [AttendanceController::class, 'index'])->middleware('permission:view attendance')->name('portal.attendance');
        Route::get('/attendance/{attendanceSession}', [AttendanceController::class, 'show'])->middleware('permission:view attendance')->name('portal.attendance.show');
        Route::put('/attendance/{attendanceSession}', [AttendanceController::class, 'update'])->middleware('permission:manage attendance')->name('portal.attendance.update');
        Route::delete('/attendance/{attendanceSession}', [AttendanceController::class, 'destroy'])->middleware('permission:manage attendance')->name('portal.attendance.destroy');
        Route::get('/memberships', function (\Illuminate\Http\Request $request) {
            $search = $request->get('search');

            return Inertia::render('Memberships/Index', [
                'memberships' => Membership::query()
                    ->with(['member:id,member_code,first_name,last_name', 'plan:id,name,type,price,currency'])
                    ->when($search, static function ($query, $search): void {
                        $query->whereHas('member', static function ($q) use ($search): void {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    })
                    ->latest()
                    ->paginate(15)
                    ->through(fn (Membership $membership): array => [
                        'id' => $membership->id,
                        'status' => $membership->status,
                        'remaining_visits' => $membership->remaining_visits,
                        'starts_at' => $membership->starts_at?->toDateString(),
                        'ends_at' => $membership->ends_at?->toDateString(),
                        'member' => $membership->member?->only(['id', 'member_code', 'first_name', 'last_name']),
                        'plan' => $membership->plan?->only(['id', 'name']),
                    ]),
                'filters' => ['search' => $search ?? ''],
            ]);
        })->middleware('permission:view memberships')->name('portal.memberships');

        Route::get('/memberships/create', function () {
            return Inertia::render('Memberships/Create', [
                'plans' => MembershipPlan::query()
                    ->active()
                    ->get(['id', 'name', 'code', 'type', 'price', 'currency']),
                'members' => Member::query()
                    ->get(['id', 'first_name', 'last_name', 'member_code']),
            ]);
        })->middleware('permission:manage memberships')->name('portal.memberships.create');

        Route::get('/memberships/{membership}/edit', function (Membership $membership) {
            return Inertia::render('Memberships/Edit', [
                'membership' => $membership->load(['member:id,first_name,last_name', 'plan:id,name']),
                'plans' => MembershipPlan::query()->get(['id', 'name', 'code']),
            ]);
        })->middleware('permission:manage memberships')->name('portal.memberships.edit');

        Route::get('/membership-plans', function (\Illuminate\Http\Request $request) {
            $search = $request->get('search');

            return Inertia::render('MembershipPlans/Index', [
                'plans' => MembershipPlan::query()
                    ->when($search, static function ($query, $search): void {
                        $query->where(function ($q) use ($search): void {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                    })
                    ->latest()
                    ->paginate(15),
                'filters' => ['search' => $search ?? ''],
            ]);
        })->middleware('permission:view membership plans')->name('portal.membership-plans');

        Route::get('/membership-plans/create', function () {
            return Inertia::render('MembershipPlans/Create');
        })->middleware('permission:manage membership plans')->name('portal.membership-plans.create');

        Route::get('/membership-plans/{plan}/edit', function (MembershipPlan $plan) {
            return Inertia::render('MembershipPlans/Edit', [
                'plan' => $plan,
            ]);
        })->middleware('permission:manage membership plans')->name('portal.membership-plans.edit');
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

        Route::prefix('equipment-items')->middleware('permission:manage inventory')->group(function (): void {
            Route::get('/', [EquipmentItemController::class, 'index'])->name('portal.equipment-items.index');
            Route::get('/create', [EquipmentItemController::class, 'create'])->name('portal.equipment-items.create');
            Route::post('/', [EquipmentItemController::class, 'store'])->name('portal.equipment-items.store');
            Route::get('/{equipmentItem}/edit', [EquipmentItemController::class, 'edit'])->name('portal.equipment-items.edit');
            Route::put('/{equipmentItem}', [EquipmentItemController::class, 'update'])->name('portal.equipment-items.update');
            Route::delete('/{equipmentItem}', [EquipmentItemController::class, 'destroy'])->name('portal.equipment-items.destroy');
        });

        Route::prefix('stock-items')->middleware('permission:manage inventory')->group(function (): void {
            Route::get('/', [StockItemController::class, 'index'])->name('portal.stock-items.index');
            Route::get('/create', [StockItemController::class, 'create'])->name('portal.stock-items.create');
            Route::post('/', [StockItemController::class, 'store'])->name('portal.stock-items.store');
            Route::get('/{stockItem}/edit', [StockItemController::class, 'edit'])->name('portal.stock-items.edit');
            Route::put('/{stockItem}', [StockItemController::class, 'update'])->name('portal.stock-items.update');
            Route::delete('/{stockItem}', [StockItemController::class, 'destroy'])->name('portal.stock-items.destroy');
        });

        Route::prefix('equipment-categories')->middleware('permission:manage inventory')->group(function (): void {
            Route::get('/', [EquipmentCategoryController::class, 'index'])->name('portal.equipment-categories.index');
            Route::get('/create', [EquipmentCategoryController::class, 'create'])->name('portal.equipment-categories.create');
            Route::post('/', [EquipmentCategoryController::class, 'store'])->name('portal.equipment-categories.store');
            Route::get('/{equipmentCategory}/edit', [EquipmentCategoryController::class, 'edit'])->name('portal.equipment-categories.edit');
            Route::put('/{equipmentCategory}', [EquipmentCategoryController::class, 'update'])->name('portal.equipment-categories.update');
            Route::delete('/{equipmentCategory}', [EquipmentCategoryController::class, 'destroy'])->name('portal.equipment-categories.destroy');
        });

        Route::prefix('gym-classes')->middleware('permission:manage classes')->group(function (): void {
            Route::get('/', [GymClassController::class, 'index'])->name('portal.gym-classes.index');
            Route::get('/create', function () {
                return Inertia::render('GymClasses/Create');
            })->name('portal.gym-classes.create');
            Route::post('/', [GymClassController::class, 'store'])->name('portal.gym-classes.store');
            Route::get('/{gymClass}', [GymClassController::class, 'show'])->name('portal.gym-classes.show');
            Route::get('/{gymClass}/edit', function (GymClass $gymClass) {
                $gymClass->load('trainer');
                return Inertia::render('GymClasses/Edit', ['gymClass' => $gymClass]);
            })->name('portal.gym-classes.edit');
            Route::put('/{gymClass}', [GymClassController::class, 'update'])->name('portal.gym-classes.update');
            Route::delete('/{gymClass}', [GymClassController::class, 'destroy'])->name('portal.gym-classes.destroy');
        });

        Route::prefix('class-sessions')->middleware('permission:manage classes')->group(function (): void {
            Route::get('/', [ClassSessionController::class, 'index'])->name('portal.class-sessions.index');
            Route::get('/create', function () {
                $gymClasses = GymClass::query()->select('id', 'name')->where('is_active', true)->get();
                return Inertia::render('ClassSessions/Create', ['gymClasses' => $gymClasses]);
            })->name('portal.class-sessions.create');
            Route::post('/', [ClassSessionController::class, 'store'])->name('portal.class-sessions.store');
            Route::get('/{classSession}', [ClassSessionController::class, 'show'])->name('portal.class-sessions.show');
            Route::get('/{classSession}/edit', function (ClassSession $classSession) {
                $classSession->load(['gymClass', 'bookings']);
                $gymClasses = GymClass::query()->select('id', 'name')->where('is_active', true)->get();
                return Inertia::render('ClassSessions/Edit', [
                    'classSession' => $classSession,
                    'gymClasses' => $gymClasses,
                ]);
            })->name('portal.class-sessions.edit');
            Route::put('/{classSession}', [ClassSessionController::class, 'update'])->name('portal.class-sessions.update');
            Route::delete('/{classSession}', [ClassSessionController::class, 'destroy'])->name('portal.class-sessions.destroy');
        });
    });

    Route::prefix('attendance')->middleware('permission:manage attendance')->group(function (): void {
        Route::post('check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
        Route::post('check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    });

    Route::prefix('membership-plans')->middleware('permission:manage membership plans')->group(function (): void {
        Route::get('/', [MembershipPlanController::class, 'index'])->name('membership-plans.index');
        Route::post('/', [MembershipPlanController::class, 'store'])->name('membership-plans.store');
        Route::get('{membershipPlan}', [MembershipPlanController::class, 'show'])->name('membership-plans.show');
        Route::put('{membershipPlan}', [MembershipPlanController::class, 'update'])->name('membership-plans.update');
        Route::delete('{membershipPlan}', [MembershipPlanController::class, 'destroy'])->name('membership-plans.destroy');
    });

    Route::prefix('memberships')->middleware('permission:manage memberships')->group(function (): void {
        Route::get('/', [MembershipController::class, 'index'])->name('memberships.index');
        Route::post('activate', [MembershipController::class, 'activate'])->name('memberships.activate');
        Route::post('renew', [MembershipController::class, 'renew'])->name('memberships.renew');
        Route::get('{membership}', [MembershipController::class, 'show'])->name('memberships.show');
        Route::put('{membership}', [MembershipController::class, 'update'])->name('memberships.update');
        Route::delete('{membership}', [MembershipController::class, 'destroy'])->name('memberships.destroy');
        Route::post('{membership}/pause', [MembershipController::class, 'pause'])->name('memberships.pause');
        Route::post('{membership}/cancel', [MembershipController::class, 'cancel'])->name('memberships.cancel');
    });
});
