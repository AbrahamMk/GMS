<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Attendance\CheckInMember;
use App\Actions\Attendance\CheckOutMember;
use App\Http\Requests\Attendance\CheckInRequest;
use App\Http\Requests\Attendance\CheckOutRequest;
use App\Http\Requests\Attendance\UpdateAttendanceSessionRequest;
use App\Http\Resources\Attendance\AttendanceSessionResource;
use App\Models\AttendanceSession;
use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class AttendanceController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $sessions = AttendanceSession::query()
            ->with('member')
            ->when($search, function ($query, $search): void {
                $query->whereHas('member', function ($q) use ($search): void {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('member_code', 'like', "%{$search}%");
                });
            })
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->when($dateFrom, fn ($query, $date) => $query->whereDate('checked_in_at', '>=', $date))
            ->when($dateTo, fn ($query, $date) => $query->whereDate('checked_in_at', '<=', $date))
            ->latest('checked_in_at')
            ->paginate(15)
            ->withQueryString();

        $now = now();

        return Inertia::render('Attendance/Index', [
            'sessions' => AttendanceSessionResource::collection($sessions)->response()->getData(true),
            'filters' => [
                'search' => $search ?? '',
                'status' => $status ?? '',
                'date_from' => $dateFrom ?? '',
                'date_to' => $dateTo ?? '',
            ],
            'summary' => [
                'todayCheckIns' => AttendanceSession::query()->whereDate('checked_in_at', $now)->count(),
                'openSessions' => AttendanceSession::query()->where('status', 'open')->count(),
            ],
            'recentSessions' => AttendanceSessionResource::collection(
                AttendanceSession::query()
                    ->with('member')
                    ->latest('checked_in_at')
                    ->limit(5)
                    ->get()
            ),
        ]);
    }

    public function show(AttendanceSession $attendanceSession): Response
    {
        return Inertia::render('Attendance/Show', [
            'session' => (new AttendanceSessionResource(
                $attendanceSession->load('member')
            ))->resolve(),
        ]);
    }

    public function update(UpdateAttendanceSessionRequest $request, AttendanceSession $attendanceSession)
    {
        $attendanceSession->fill($request->validated())->save();

        return redirect()->route('portal.attendance')->with('success', 'Attendance session updated successfully.');
    }

    public function destroy(AttendanceSession $attendanceSession)
    {
        $attendanceSession->delete();

        return redirect()->route('portal.attendance')->with('success', 'Attendance session deleted successfully.');
    }

    public function checkIn(CheckInRequest $request, CheckInMember $action)
    {
        $member = Member::query()->findOrFail((int) $request->validated('member_id'));
        $session = $action->handle($member, $request, $request->user(), $request->validated());

        return redirect()->route('portal.attendance')->with('success', 'Check-in recorded successfully.');
    }

    public function checkOut(CheckOutRequest $request, CheckOutMember $action)
    {
        $session = AttendanceSession::query()->findOrFail((int) $request->validated('attendance_session_id'));
        $session = $action->handle($session, $request->user(), $request->validated());

        return redirect()->route('portal.attendance')->with('success', 'Check-out recorded successfully.');
    }
}
