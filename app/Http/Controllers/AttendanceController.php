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

final class AttendanceController extends Controller
{
    public function checkIn(CheckInRequest $request, CheckInMember $action): AttendanceSessionResource
    {
        $member = Member::query()->findOrFail((int) $request->validated('member_id'));
        $session = $action->handle($member, $request, $request->user(), $request->validated());

        return new AttendanceSessionResource($session->fresh());
    }

    public function checkOut(CheckOutRequest $request, CheckOutMember $action): AttendanceSessionResource
    {
        $session = AttendanceSession::query()->findOrFail((int) $request->validated('attendance_session_id'));
        $session = $action->handle($session, $request->user(), $request->validated());

        return new AttendanceSessionResource($session->fresh());
    }
}
