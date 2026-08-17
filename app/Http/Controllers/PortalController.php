<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Portal\BookClassRequest;
use App\Http\Requests\Portal\CancelBookingRequest;
use App\Http\Requests\Portal\UpdateProfileRequest;
use App\Models\AttendanceSession;
use App\Models\ClassBooking;
use App\Models\ClassSession;
use App\Models\EquipmentItem;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\StockItem;
use App\Support\BranchContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class PortalController extends Controller
{
    public function dashboard(Request $request): Response
    {
        $user = $request->user();
        $member = $user?->member()->with(['memberships.plan'])->first();
        $now = app(BranchContext::class)->now();

        $activeMembership = $member?->memberships()->with('plan')->latest('starts_at')->first();
        $attendanceTrend = collect(range(6, 0))->map(function (int $daysAgo) use ($now): array {
            $day = $now->subDays($daysAgo);

            return [
                'label' => $day->format('D'),
                'value' => AttendanceSession::query()
                    ->whereBetween('checked_in_at', [$day->startOfDay(), $day->endOfDay()])
                    ->count(),
            ];
        });

        return Inertia::render('Portal/Dashboard', [
            'member' => $member?->only([
                'id',
                'member_code',
                'first_name',
                'last_name',
                'phone',
                'email',
                'status',
            ]),
            'activeMembership' => $activeMembership === null ? null : [
                ...$activeMembership->only([
                    'id',
                    'status',
                    'starts_at',
                    'ends_at',
                    'remaining_visits',
                    'total_visits',
                ]),
                'plan' => $activeMembership->plan?->only(['id', 'name', 'type', 'price', 'currency']),
            ],
            'stats' => [
                'members' => Member::withoutGlobalScopes()->count(),
                'activeMemberships' => Membership::withoutGlobalScopes()->where('status', 'active')->count(),
                'todayCheckIns' => AttendanceSession::withoutGlobalScopes()->whereDate('checked_in_at', $now)->count(),
                'upcomingClasses' => ClassSession::withoutGlobalScopes()->where('starts_at', '>=', now())->count(),
            ],
            'attendanceTrend' => [
                'labels' => $attendanceTrend->pluck('label')->values(),
                'values' => $attendanceTrend->pluck('value')->values(),
            ],
            'recentCheckIns' => AttendanceSession::withoutGlobalScopes()
                ->with('member')
                ->latest('checked_in_at')
                ->limit(6)
                ->get()
                ->map(fn (AttendanceSession $session): array => [
                    'id' => $session->id,
                    'member' => $session->member?->only(['id', 'member_code', 'first_name', 'last_name']),
                    'checked_in_at' => $session->checked_in_at,
                    'status' => $session->status,
                ])
                ->values(),
            'upcomingClasses' => ClassSession::withoutGlobalScopes()
                ->with(['gymClass'])
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->limit(5)
                ->get()
                ->map(fn (ClassSession $session): array => [
                    'id' => $session->id,
                    'name' => $session->gymClass?->name,
                    'starts_at' => $session->starts_at,
                    'ends_at' => $session->ends_at,
                    'capacity' => $session->capacity_override ?? $session->gymClass?->capacity,
                ])
                ->values(),
            'equipmentAlerts' => EquipmentItem::withoutGlobalScopes()
                ->whereIn('status', ['under_repair', 'retired'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn (EquipmentItem $equipment): array => [
                    'id' => $equipment->id,
                    'name' => $equipment->name,
                    'status' => $equipment->status,
                    'location' => $equipment->location,
                ])
                ->values(),
            'recentMembers' => Member::withoutGlobalScopes()
                ->latest()
                ->limit(6)
                ->get()
                ->map(fn (Member $m): array => [
                    'id' => $m->id,
                    'member_code' => $m->member_code,
                    'first_name' => $m->first_name,
                    'last_name' => $m->last_name,
                    'phone' => $m->phone,
                    'email' => $m->email,
                    'status' => $m->status,
                ])
                ->values(),
            'recentPayments' => Payment::withoutGlobalScopes()
                ->with('member')
                ->latest('paid_at')
                ->limit(5)
                ->get()
                ->map(fn ($p): array => [
                    'id' => $p->id,
                    'title' => $p->payment_reference ?? ucfirst($p->method ?? 'payment'),
                    'amount' => number_format((float) ($p->amount ?? 0), 2),
                    'currency' => 'ETB',
                    'status' => $p->status ?? 'pending',
                    'paid_at' => $p->paid_at ?? $p->created_at,
                    'member_name' => $p->member ? trim($p->member->first_name . ' ' . $p->member->last_name) : 'Walk-in',
                ])
                ->values(),
        ]);
    }

    public function profile(Request $request): Response
    {
        $user = $request->user();
        $member = $user?->member()->first();
        $membership = $member?->memberships()->with('plan')->latest('starts_at')->first();

        return Inertia::render('Members/Profile', [
            'user' => $user?->only(['id', 'name', 'email', 'phone']),
            'member' => $member?->only([
                'id',
                'member_code',
                'first_name',
                'last_name',
                'gender',
                'date_of_birth',
                'phone',
                'email',
                'address',
                'emergency_contact_name',
                'emergency_contact_phone',
                'photo_path',
                'status',
            ]),
            'membership' => $membership === null ? null : [
                ...$membership->only(['id', 'status', 'starts_at', 'ends_at', 'remaining_visits']),
                'plan' => $membership->plan?->only(['id', 'name', 'type', 'price', 'currency']),
            ],
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $user = $request->user();

        if ($user === null) {
            abort(403);
        }

        DB::transaction(function () use ($user, $payload): void {
            $user->forceFill([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'phone' => $payload['phone'] ?? null,
            ])->save();

            $member = $user->member()->first();

            if ($member !== null) {
                $member->forceFill($payload['member'] ?? [])->save();
            }
        });

        return back()->with('success', 'Profile updated successfully.');
    }

    public function bookings(Request $request): Response
    {
        $user = $request->user();
        $member = $user?->member()->first();
        $now = app(BranchContext::class)->now();

        return Inertia::render('Portal/Bookings', [
            'member' => $member?->only(['id', 'member_code', 'first_name', 'last_name']),
            'myBookings' => $member === null
                ? []
                : ClassBooking::query()
                    ->with(['classSession.gymClass'])
                    ->where('member_id', $member->getKey())
                    ->latest('booked_at')
                    ->get()
                    ->map(fn (ClassBooking $booking): array => [
                        'id' => $booking->id,
                        'status' => $booking->status,
                        'waitlist_position' => $booking->waitlist_position,
                        'confirmed_at' => $booking->confirmed_at,
                        'booked_at' => $booking->booked_at,
                        'session' => [
                            'id' => $booking->classSession?->id,
                            'name' => $booking->classSession?->gymClass?->name,
                            'starts_at' => $booking->classSession?->starts_at,
                            'ends_at' => $booking->classSession?->ends_at,
                        ],
                    ])
                    ->values(),
            'upcomingSessions' => ClassSession::query()
                ->with(['gymClass', 'bookings'])
                ->where('starts_at', '>=', $now)
                ->orderBy('starts_at')
                ->limit(12)
                ->get()
                ->map(fn (ClassSession $session): array => [
                    'id' => $session->id,
                    'name' => $session->gymClass?->name,
                    'description' => $session->gymClass?->description,
                    'starts_at' => $session->starts_at,
                    'ends_at' => $session->ends_at,
                    'capacity' => $session->capacity_override ?? $session->gymClass?->capacity,
                    'booked' => $session->bookings->where('status', 'booked')->whereNull('waitlist_position')->count(),
                    'waitlisted' => $session->bookings->whereNotNull('waitlist_position')->count(),
                ])
                ->values(),
        ]);
    }

    public function bookClass(BookClassRequest $request): RedirectResponse
    {
        $member = $request->user()?->member()->first();

        if ($member === null) {
            abort(403);
        }

        $payload = $request->validated();
        $session = ClassSession::query()->with(['gymClass', 'bookings'])->findOrFail((int) $payload['class_session_id']);
        $now = app(BranchContext::class)->now();

        DB::transaction(function () use ($member, $session, $now): void {
            $existingBooking = ClassBooking::query()
                ->where('class_session_id', $session->getKey())
                ->where('member_id', $member->getKey())
                ->lockForUpdate()
                ->first();

            if ($existingBooking !== null) {
                abort(422, 'The member is already booked for this class.');
            }

            $capacity = $session->capacity_override ?? $session->gymClass?->capacity ?? 0;
            $confirmedBookings = ClassBooking::query()
                ->where('class_session_id', $session->getKey())
                ->where('status', 'booked')
                ->whereNull('waitlist_position')
                ->lockForUpdate()
                ->count();

            $isWaitlisted = $confirmedBookings >= $capacity;
            $nextWaitlistPosition = ClassBooking::query()
                ->where('class_session_id', $session->getKey())
                ->lockForUpdate()
                ->max('waitlist_position');

            ClassBooking::query()->create([
                'class_session_id' => $session->getKey(),
                'member_id' => $member->getKey(),
                'status' => 'booked',
                'waitlist_position' => $isWaitlisted
                    ? (int) ($nextWaitlistPosition ?? 0) + 1
                    : null,
                'confirmed_at' => $isWaitlisted ? null : $now,
                'booked_at' => $now,
            ]);
        });

        return back()->with('success', 'Class booking saved.');
    }

    public function cancelBooking(CancelBookingRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $now = app(BranchContext::class)->now();

        DB::transaction(function () use ($payload, $now): void {
            $booking = ClassBooking::query()
                ->with('classSession')
                ->lockForUpdate()
                ->findOrFail((int) $payload['class_booking_id']);

            $booking->forceFill([
                'status' => 'cancelled',
            ])->save();

            $promotedWaitlist = ClassBooking::query()
                ->where('class_session_id', $booking->class_session_id)
                ->where('status', 'booked')
                ->whereNotNull('waitlist_position')
                ->orderBy('waitlist_position')
                ->lockForUpdate()
                ->first();

            if ($promotedWaitlist !== null) {
                $promotedWaitlist->forceFill([
                    'waitlist_position' => null,
                    'confirmed_at' => $now,
                ])->save();
            }
        });

        return back()->with('success', 'Booking cancelled.');
    }
}
