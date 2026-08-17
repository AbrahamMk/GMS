<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Attendance\CheckInMember;
use App\Actions\Attendance\CheckOutMember;
use App\Http\Requests\Attendance\CheckInRequest;
use App\Http\Requests\Attendance\CheckOutRequest;
use App\Http\Resources\Attendance\AttendanceSessionResource;
use App\Models\AttendanceSession;
use App\Models\Member;
use App\Scopes\BranchScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AttendanceController extends Controller
{
    public function checkIn(CheckInRequest $request, CheckInMember $action)
    {
        $member = Member::withoutGlobalScope(BranchScope::class)->findOrFail((int) $request->validated('member_id'));
        $session = $action->handle($member, $request, $request->user(), $request->validated());

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return new AttendanceSessionResource($session->fresh());
        }

        return redirect()->back()->with('success', "Member {$member->first_name} {$member->last_name} checked in successfully.");
    }

    public function checkOut(CheckOutRequest $request, CheckOutMember $action)
    {
        $session = AttendanceSession::withoutGlobalScope(BranchScope::class)->findOrFail((int) $request->validated('attendance_session_id'));
        $session = $action->handle($session, $request->user(), $request->validated());

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return new AttendanceSessionResource($session->fresh());
        }

        return redirect()->back()->with('success', 'Attendance session checked out successfully.');
    }
}
