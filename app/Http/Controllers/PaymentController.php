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

        // Recent payment history (recent memberships as proxy for payments)
        $recentPayments = Membership::query()
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
            ->values();

        // Revenue breakdown by plan
        $revenueByPlan = DB::table('memberships')
            ->join('membership_plans', 'memberships.membership_plan_id', '=', 'membership_plans.id')
            ->select('membership_plans.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(membership_plans.price) as revenue'))
            ->groupBy('membership_plans.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return Inertia::render('Payments/Index', [
            'summary' => [
                'totalRevenue'      => $totalRevenue,
                'thisMonthRevenue'  => $thisMonthRevenue,
                'totalTransactions' => Membership::query()->count(),
                'activePlans'       => Membership::query()->where('status', 'active')->count(),
            ],
            'recentPayments' => $recentPayments,
            'revenueByPlan'  => $revenueByPlan,
        ]);
    }
}
