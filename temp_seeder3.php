<?php
$branchId = App\Models\Branch::first()->id;

App\Models\MembershipPlan::withoutGlobalScopes()->update(['is_active' => false]);

App\Models\MembershipPlan::withoutGlobalScopes()->create([
    'branch_id' => $branchId,
    'code' => 'BASIC_MONTHLY',
    'name' => 'Basic',
    'description' => "Full gym access\nCardio & strength equipment\nLocker access\nFree fitness assessment",
    'type' => 'time_based',
    'duration_days' => 30,
    'price' => 29.00,
    'currency' => 'USD',
    'is_active' => true,
]);

App\Models\MembershipPlan::withoutGlobalScopes()->create([
    'branch_id' => $branchId,
    'code' => 'PREMIUM_MONTHLY',
    'name' => 'Premium',
    'description' => "Everything in Basic\nUnlimited group classes\nPersonal fitness plan\nSauna & recovery area",
    'type' => 'time_based',
    'duration_days' => 30,
    'price' => 49.00,
    'currency' => 'USD',
    'is_active' => true,
]);

App\Models\MembershipPlan::withoutGlobalScopes()->create([
    'branch_id' => $branchId,
    'code' => 'ELITE_MONTHLY',
    'name' => 'Elite',
    'description' => "Everything in Premium\n4 personal training sessions\nNutrition consultation\nPriority class booking",
    'type' => 'time_based',
    'duration_days' => 30,
    'price' => 79.00,
    'currency' => 'USD',
    'is_active' => true,
]);
