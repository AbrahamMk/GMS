<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Members\StoreMemberRequest;
use App\Http\Requests\Members\UpdateMemberRequest;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class MemberController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');

        $members = Member::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('member_code', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($member) => [
                'id' => $member->id,
                'member_code' => $member->member_code,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'gender' => $member->gender,
                'date_of_birth' => $member->date_of_birth,
                'phone' => $member->phone,
                'email' => $member->email,
                'address' => $member->address,
                'emergency_contact_name' => $member->emergency_contact_name,
                'emergency_contact_phone' => $member->emergency_contact_phone,
                'photo_path' => $member->photo_path,
                'status' => $member->status,
                'joined_at' => $member->joined_at,
            ]);

        return Inertia::render('Members/Index', [
            'members' => $members,
            'filters' => ['search' => $search ?? ''],
        ]);
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        Member::query()->create($request->validated());

        return redirect()->route('portal.members')->with('success', 'Member created successfully.');
    }

    public function show(Member $member): Response
    {
        return Inertia::render('Members/Show', [
            'member' => $member,
        ]);
    }

    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $member->fill($request->validated())->save();

        return redirect()->route('portal.members')->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('portal.members')->with('success', 'Member deleted successfully.');
    }
}
