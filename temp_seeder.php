<?php
App\Models\MembershipPlan::withoutGlobalScopes()->update(['is_active' => false]);

App\Models\MembershipPlan::create([
    'name' => 'Basic',
    'description' => 'Standard membership for gym access.',
    'price' => 29.00,
    'currency' => 'USD',
    'billing_cycle' => 'monthly',
    'duration_in_days' => 30,
    'features' => "Full gym access\nCardio & strength equipment\nLocker access\nFree fitness assessment",
    'is_active' => true,
]);

App\Models\MembershipPlan::create([
    'name' => 'Premium',
    'description' => 'Unlimited group classes and premium features.',
    'price' => 49.00,
    'currency' => 'USD',
    'billing_cycle' => 'monthly',
    'duration_in_days' => 30,
    'features' => "Everything in Basic\nUnlimited group classes\nPersonal fitness plan\nSauna & recovery area",
    'is_active' => true,
]);

App\Models\MembershipPlan::create([
    'name' => 'Elite',
    'description' => 'Ultimate package with personal training.',
    'price' => 79.00,
    'currency' => 'USD',
    'billing_cycle' => 'monthly',
    'duration_in_days' => 30,
    'features' => "Everything in Premium\n4 personal training sessions\nNutrition consultation\nPriority class booking",
    'is_active' => true,
]);
