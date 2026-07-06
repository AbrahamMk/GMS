<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Models\User;
use App\Support\BranchContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class InitializeBranchContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $context = app(BranchContext::class);
        $context->clear();

        /** @var User|null $user */
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        $branch = $user->currentBranch ?? $user->branch;

        if ($branch === null) {
            $branch = $user->branches()
                ->wherePivot('is_default', true)
                ->orderBy('branches.id')
                ->first()
                ?? $user->branches()->orderBy('branches.id')->first();
        }

        if ($branch instanceof Branch) {
            $context->setBranch($branch);

            return $next($request);
        }

        $context->setBranchId($user->current_branch_id ?? $user->branch_id);

        return $next($request);
    }
}
