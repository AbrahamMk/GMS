<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ClassSessions\StoreClassSessionRequest;
use App\Http\Requests\ClassSessions\UpdateClassSessionRequest;
use App\Http\Resources\ClassSessions\ClassSessionResource;
use App\Models\ClassSession;
use App\Models\GymClass;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ClassSessionController extends Controller
{
    public function index(Request $request): Response
    {
        $sessions = ClassSession::query()
            ->with(['gymClass', 'bookings'])
            ->when($request->gym_class_id, fn ($query, $id) => $query->where('gym_class_id', $id))
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->starts_from, fn ($query, $date) => $query->where('starts_at', '>=', $date))
            ->when($request->starts_to, fn ($query, $date) => $query->where('starts_at', '<=', $date))
            ->latest('starts_at')
            ->paginate(15)
            ->withQueryString();

        $gymClasses = GymClass::query()->select('id', 'name')->where('is_active', true)->get();

        return Inertia::render('ClassSessions/Index', [
            'sessions' => ClassSessionResource::collection($sessions)->response()->getData(true),
            'gymClasses' => $gymClasses,
            'filters' => [
                'gym_class_id' => $request->gym_class_id ?? '',
                'status' => $request->status ?? '',
                'starts_from' => $request->starts_from ?? '',
                'starts_to' => $request->starts_to ?? '',
            ],
        ]);
    }

    public function store(StoreClassSessionRequest $request)
    {
        $session = ClassSession::query()->create($request->validated());

        return redirect()->route('portal.class-sessions.index')->with('success', 'Class session created successfully.');
    }

    public function show(ClassSession $classSession): Response
    {
        return Inertia::render('ClassSessions/Show', [
            'classSession' => new ClassSessionResource($classSession->load(['gymClass', 'bookings'])),
        ]);
    }

    public function update(UpdateClassSessionRequest $request, ClassSession $classSession)
    {
        $classSession->fill($request->validated())->save();

        return redirect()->route('portal.class-sessions.index')->with('success', 'Class session updated successfully.');
    }

    public function destroy(ClassSession $classSession)
    {
        $classSession->delete();

        return redirect()->route('portal.class-sessions.index')->with('success', 'Class session deleted successfully.');
    }
}
