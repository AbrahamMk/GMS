<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

final class PaymentController extends Controller
{
    public function index(Request $request): \Inertia\Response
    {
        // Summary stats
        $totalRevenue = Membership::query()
            ->join('membership_plans', 'memberships.membership_plan_id', '=', 'membership_plans.id')
            ->where('memberships.status', 'active')
            ->sum('membership_plans.price');

        $thisMonthRevenue = Membership::query()
            ->join('membership_plans', 'memberships.membership_plan_id', '=', 'membership_plans.id')
            ->whereMonth('memberships.starts_at', now()->month)
            ->whereYear('memberships.starts_at', now()->year)
            ->sum('membership_plans.price');

        // Payments from payments table
        $dbPayments = \App\Models\Payment::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($p) {
                $meta = is_array($p->metadata) ? $p->metadata : json_decode($p->metadata ?? '[]', true);
                $member = \App\Models\Member::withoutGlobalScope(\App\Scopes\BranchScope::class)->find($p->member_id);
                return [
                    'id'       => $p->id,
                    'member'   => $member ? ['id' => $member->id, 'member_code' => $member->member_code, 'first_name' => $member->first_name, 'last_name' => $member->last_name] : null,
                    'plan'     => ['name' => $meta['title'] ?? 'Chapa / Direct Payment'],
                    'status'   => $p->status,
                    'amount'   => (float) $p->amount,
                    'currency' => $meta['currency'] ?? 'ETB',
                    'paid_at'  => $p->paid_at ?? $p->created_at,
                    'method'   => $p->method,
                ];
            });

        // Recent payment history (recent memberships + db payments)
        $recentPayments = Membership::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->with(['member', 'plan'])
            ->latest('starts_at')
            ->limit(20)
            ->get()
            ->map(fn (Membership $membership): array => [
                'id'          => $membership->id,
                'member'      => $membership->member?->only(['id', 'member_code', 'first_name', 'last_name']),
                'plan'        => $membership->plan?->only(['id', 'name', 'type', 'price', 'currency']),
                'status'      => $membership->status,
                'amount'      => $membership->plan?->price ?? 0,
                'currency'    => $membership->plan?->currency ?? 'USD',
                'paid_at'     => $membership->starts_at,
            ])
            ->concat($dbPayments)
            ->sortByDesc('paid_at')
            ->values();

        // Revenue breakdown by plan
        $revenueByPlan = DB::table('memberships')
            ->join('membership_plans', 'memberships.membership_plan_id', '=', 'membership_plans.id')
            ->select('membership_plans.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(membership_plans.price) as revenue'))
            ->groupBy('membership_plans.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $membersList = \App\Models\Member::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->select('id', 'first_name', 'last_name', 'member_code', 'email')
            ->get();

        return Inertia::render('Payments/Index', [
            'summary' => [
                'totalRevenue'      => $totalRevenue + \App\Models\Payment::withoutGlobalScope(\App\Scopes\BranchScope::class)->where('status', 'completed')->sum('amount'),
                'thisMonthRevenue'  => $thisMonthRevenue,
                'totalTransactions' => Membership::withoutGlobalScope(\App\Scopes\BranchScope::class)->count() + \App\Models\Payment::withoutGlobalScope(\App\Scopes\BranchScope::class)->count(),
                'activePlans'       => Membership::withoutGlobalScope(\App\Scopes\BranchScope::class)->where('status', 'active')->count(),
            ],
            'recentPayments' => $recentPayments,
            'revenueByPlan'  => $revenueByPlan,
            'members'        => $membersList,
        ]);
    }
}
