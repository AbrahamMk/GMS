<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberAppController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $member = $user?->member()->first();

        // Assuming a basic structure, mock data or fetch actual
        return Inertia::render('MemberApp/Dashboard', [
            'member' => $member,
            'todayWorkout' => [
                'title' => 'Full Body HIIT',
                'duration' => '45 mins',
                'instructor' => 'Sarah J.',
            ],
            'upcomingClasses' => ClassSession::query()->where('starts_at', '>', now())->orderBy('starts_at')->take(3)->get(),
        ]);
    }

    public function classes(Request $request)
    {
        return Inertia::render('MemberApp/Classes', [
            'classes' => ClassSession::query()->where('starts_at', '>', now())->orderBy('starts_at')->get(),
        ]);
    }

    public function workouts(Request $request)
    {
        return Inertia::render('MemberApp/Workouts', [
            'workouts' => [
                [
                    'day' => 'Chest & Triceps',
                    'exercises' => [
                        ['name' => 'Bench Press', 'sets' => '4 × 10', 'weight' => '60 kg'],
                        ['name' => 'Incline Dumbbell Press', 'sets' => '3 × 12', 'weight' => '22 kg'],
                        ['name' => 'Cable Fly', 'sets' => '3 × 15', 'weight' => '15 kg'],
                        ['name' => 'Tricep Pushdown', 'sets' => '3 × 12', 'weight' => '25 kg'],
                    ]
                ],
                [
                    'day' => 'Back & Biceps',
                    'exercises' => [
                        ['name' => 'Deadlift', 'sets' => '4 × 8', 'weight' => '100 kg'],
                        ['name' => 'Lat Pulldown', 'sets' => '3 × 12', 'weight' => '55 kg'],
                        ['name' => 'Seated Cable Row', 'sets' => '3 × 12', 'weight' => '50 kg'],
                        ['name' => 'Barbell Curl', 'sets' => '3 × 12', 'weight' => '30 kg'],
                    ]
                ]
            ]
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $member = $user?->member()->first();

        return Inertia::render('MemberApp/Profile', [
            'member' => $member,
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $member = $user?->member()->first();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        if ($member) {
            $member->update($validated);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
