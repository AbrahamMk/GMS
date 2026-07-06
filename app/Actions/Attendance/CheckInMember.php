<?php

declare(strict_types=1);

namespace App\Actions\Attendance;

use App\Models\AttendanceSession;
use App\Models\Member;
use App\Models\MemberQrToken;
use App\Models\Membership;
use App\Models\MembershipTransaction;
use App\Models\User;
use App\Support\BranchContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CheckInMember
{
    public function handle(Member $member, Request $request, ?User $performedBy = null, array $payload = []): AttendanceSession
    {
        return DB::transaction(function () use ($member, $request, $performedBy, $payload): AttendanceSession {
            $existingOpenSession = AttendanceSession::query()
                ->where('member_id', $member->getKey())
                ->where('status', 'open')
                ->lockForUpdate()
                ->first();

            if ($existingOpenSession !== null) {
                throw ValidationException::withMessages([
                    'member_id' => 'The member already has an open attendance session.',
                ]);
            }

            $membership = Membership::query()
                ->with('plan')
                ->where('member_id', $member->getKey())
                ->active()
                ->latest('starts_at')
                ->lockForUpdate()
                ->first();

            if ($membership === null || $membership->plan === null || $membership->isExpired()) {
                throw ValidationException::withMessages([
                    'member_id' => 'No active membership is available for check-in.',
                ]);
            }

            $now = app(BranchContext::class)->now();

            if ($membership->plan->isTimeBased()) {
                $windowHours = (int) ($membership->plan->allowed_check_in_window_hours ?? 24);
                $startsAt = $membership->starts_at?->toImmutable();
                $endsAt = $membership->ends_at?->toImmutable();

                if ($startsAt !== null && $now->lt($startsAt->subHours($windowHours))) {
                    throw ValidationException::withMessages([
                        'member_id' => 'The membership check-in window has not opened yet.',
                    ]);
                }

                if ($endsAt !== null && $now->gt($endsAt->addHours($windowHours))) {
                    throw ValidationException::withMessages([
                        'member_id' => 'The membership check-in window has closed.',
                    ]);
                }
            }

            if ($membership->plan->max_daily_visits !== null) {
                $dailyVisits = AttendanceSession::query()
                    ->where('member_id', $member->getKey())
                    ->whereDate('checked_in_at', $now->toDateString())
                    ->count();

                if ($dailyVisits >= $membership->plan->max_daily_visits) {
                    throw ValidationException::withMessages([
                        'member_id' => 'The member has reached the daily visit limit.',
                    ]);
                }
            }

            if ($membership->plan->isPackBased() && ($membership->remaining_visits ?? 0) <= 0) {
                throw ValidationException::withMessages([
                    'member_id' => 'The membership visit pack has been exhausted.',
                ]);
            }

            $qrToken = null;
            $rawToken = $payload['qr_token'] ?? null;

            if (is_string($rawToken) && $rawToken !== '') {
                $qrToken = MemberQrToken::query()
                    ->where('member_id', $member->getKey())
                    ->where('token_hash', hash('sha256', $rawToken))
                    ->where('is_active', true)
                    ->first();

                if ($qrToken === null) {
                    throw ValidationException::withMessages([
                        'qr_token' => 'The QR token is invalid or inactive.',
                    ]);
                }
            }

            $session = AttendanceSession::query()->create([
                'member_id' => $member->getKey(),
                'membership_id' => $membership->getKey(),
                'qr_token_id' => $qrToken?->getKey(),
                'checked_in_at' => $now,
                'check_in_ip' => $request->ip(),
                'device_info' => $payload['device_info'] ?? [
                    'user_agent' => $request->userAgent(),
                ],
                'verified_by_user_id' => $payload['verified_by_user_id'] ?? $performedBy?->getKey(),
                'check_in_method' => $payload['check_in_method'] ?? 'qr',
                'status' => 'open',
                'performed_by_user_id' => $performedBy?->getKey(),
            ]);

            if ($membership->plan->isPackBased()) {
                $previousRemainingVisits = $membership->remaining_visits;
                $membership->forceFill([
                    'remaining_visits' => max(0, (int) $membership->remaining_visits - 1),
                ])->save();

                MembershipTransaction::query()->create([
                    'membership_id' => $membership->getKey(),
                    'type' => 'adjustment',
                    'previous_remaining_visits' => $previousRemainingVisits,
                    'new_remaining_visits' => $membership->remaining_visits,
                    'performed_by_user_id' => $performedBy?->getKey(),
                    'metadata' => [
                        'reason' => 'attendance_check_in',
                        'attendance_session_id' => $session->getKey(),
                    ],
                ]);
            }

            return $session;
        });
    }
}
