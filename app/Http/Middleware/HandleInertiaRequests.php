<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\BranchContext;
use Illuminate\Http\Request;
use Inertia\Middleware;

final class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();
        $branchContext = app(BranchContext::class);
        $branch = $branchContext->branch();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user?->only(['id', 'name', 'email', 'phone', 'branch_id', 'current_branch_id']),
                'roles' => $user?->getRoleNames()->values() ?? [],
                'permissions' => $user?->getAllPermissions()->pluck('name')->values() ?? [],
            ],
            'branch' => $branch?->only(['id', 'code', 'name', 'timezone', 'currency', 'is_active']) ?? ($user?->currentBranch?->only(['id', 'code', 'name', 'timezone', 'currency', 'is_active']) ?? null),
            'branches' => $user === null
                ? []
                : $user->branches()
                    ->orderBy('branches.name')
                    ->get(['branches.id', 'branches.code', 'branches.name', 'branches.currency', 'branches.timezone']),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
