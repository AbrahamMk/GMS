<?php

declare(strict_types=1);

namespace App\Actions\Attendance;

use App\Models\AttendanceSession;
use App\Models\User;
use App\Support\BranchContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CheckOutMember
{
    public function handle(AttendanceSession $session, ?User $performedBy = null, array $payload = []): AttendanceSession
    {
        return DB::transaction(function () use ($session, $performedBy, $payload): AttendanceSession {
            $session->refresh();

            if ($session->checked_out_at !== null || $session->status !== 'open') {
                throw ValidationException::withMessages([
                    'attendance_session_id' => 'The attendance session is already closed.',
                ]);
            }

            $session->forceFill([
                'checked_out_at' => app(BranchContext::class)->now(),
                'check_out_method' => $payload['check_out_method'] ?? 'qr',
                'status' => 'closed',
                'verified_by_user_id' => $payload['verified_by_user_id'] ?? $performedBy?->getKey(),
                'performed_by_user_id' => $performedBy?->getKey(),
            ])->save();

            return $session;
        });
    }
}
