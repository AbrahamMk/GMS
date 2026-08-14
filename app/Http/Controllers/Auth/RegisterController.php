<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

final class RegisterController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'branches' => Branch::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:users,phone',
            'email' => 'required|string|email|max:255|unique:users,email',
            'branch_id' => 'required|exists:branches,id',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'branch_id' => $request->branch_id,
            'current_branch_id' => $request->branch_id,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        $user->branches()->syncWithoutDetaching([
            $request->branch_id => ['is_default' => true]
        ]);

        // Assign member role
        $user->assignRole('member');

        // Generate a random unique member code (e.g. MBR-XXXXXX)
        $memberCode = 'MBR-' . strtoupper(bin2hex(random_bytes(3)));

        // Split name into first and last
        $parts = explode(' ', $request->name, 2);
        $firstName = $parts[0];
        $lastName = $parts[1] ?? '';

        // Create Member profile
        Member::create([
            'branch_id' => $request->branch_id,
            'user_id' => $user->id,
            'member_code' => $memberCode,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        auth()->login($user);

        return redirect()->route('member.dashboard');
    }
}
