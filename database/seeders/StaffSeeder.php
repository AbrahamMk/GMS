<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AttendanceSession;
use App\Models\Branch;
use App\Models\ClassBooking;
use App\Models\ClassSession;
use App\Models\EquipmentCategory;
use App\Models\EquipmentItem;
use App\Models\GymClass;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\StockItem;
use App\Models\User;
use App\Support\BranchContext;
use Carbon\CarbonImmutable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $branchContext = app(BranchContext::class);
        $seededAt = CarbonImmutable::create(2026, 7, 4, 9, 0, 0, config('app.timezone', 'Africa/Nairobi'));
        $defaultPassword = Hash::make('password');

        try {
            $branches = collect([
                [
                    'code' => 'main',
                    'name' => 'Main Branch',
                    'timezone' => config('app.timezone', 'Africa/Nairobi'),
                    'currency' => 'KES',
                    'is_active' => true,
                ],
                [
                    'code' => 'west',
                    'name' => 'West Branch',
                    'timezone' => config('app.timezone', 'Africa/Nairobi'),
                    'currency' => 'KES',
                    'is_active' => true,
                ],
            ])->map(static function (array $payload): Branch {
                return Branch::query()->updateOrCreate(
                    ['code' => $payload['code']],
                    $payload
                );
            });

            $mainBranch = $branches->firstWhere('code', 'main');
            $westBranch = $branches->firstWhere('code', 'west');

            $admin = User::query()->updateOrCreate(
                ['email' => 'admin@gms.test'],
                [
                    'name' => 'System Admin',
                    'password' => $defaultPassword,
                    'branch_id' => $mainBranch->getKey(),
                    'current_branch_id' => $mainBranch->getKey(),
                    'email_verified_at' => $seededAt,
                    'phone' => '+254700000001',
                ]
            );

            $manager = User::query()->updateOrCreate(
                ['email' => 'manager@gms.test'],
                [
                    'name' => 'Branch Manager',
                    'password' => $defaultPassword,
                    'branch_id' => $mainBranch->getKey(),
                    'current_branch_id' => $mainBranch->getKey(),
                    'email_verified_at' => $seededAt,
                    'phone' => '+254700000002',
                ]
            );

            $reception = User::query()->updateOrCreate(
                ['email' => 'reception@gms.test'],
                [
                    'name' => 'Front Desk',
                    'password' => $defaultPassword,
                    'branch_id' => $westBranch->getKey(),
                    'current_branch_id' => $westBranch->getKey(),
                    'email_verified_at' => $seededAt,
                    'phone' => '+254700000003',
                ]
            );

            foreach ([$admin, $manager, $reception] as $staffUser) {
                $staffUser->branches()->syncWithoutDetaching([
                    $mainBranch->getKey() => ['is_default' => $staffUser->is($admin) || $staffUser->is($manager)],
                    $westBranch->getKey() => ['is_default' => $staffUser->is($reception)],
                ]);
            }

            $branchContext->setBranch($mainBranch);

            $basicPlan = MembershipPlan::query()->updateOrCreate(
                ['branch_id' => $mainBranch->getKey(), 'code' => 'BASIC-MONTHLY'],
                [
                    'branch_id' => $mainBranch->getKey(),
                    'name' => 'Basic Monthly',
                    'description' => 'Time-based monthly access.',
                    'type' => 'time_based',
                    'duration_days' => 30,
                    'visit_limit' => null,
                    'price' => 2500,
                    'currency' => 'KES',
                    'grace_period_days' => 3,
                    'allowed_check_in_window_hours' => 24,
                    'max_daily_visits' => 2,
                    'freeze_allowance_days' => 7,
                    'auto_renewable' => true,
                    'is_active' => true,
                ]
            );

            $tenPackPlan = MembershipPlan::query()->updateOrCreate(
                ['branch_id' => $mainBranch->getKey(), 'code' => 'TEN-PACK'],
                [
                    'branch_id' => $mainBranch->getKey(),
                    'name' => '10 Visit Pack',
                    'description' => 'Pack-based access for 10 visits.',
                    'type' => 'pack_based',
                    'duration_days' => 90,
                    'visit_limit' => 10,
                    'price' => 3000,
                    'currency' => 'KES',
                    'grace_period_days' => 0,
                    'allowed_check_in_window_hours' => 24,
                    'max_daily_visits' => 1,
                    'freeze_allowance_days' => 0,
                    'auto_renewable' => false,
                    'is_active' => true,
                ]
            );

            $gymClass = GymClass::query()->updateOrCreate(
                ['branch_id' => $mainBranch->getKey(), 'name' => 'Strength Circuit'],
                [
                    'branch_id' => $mainBranch->getKey(),
                    'description' => 'Branch demo class for booking flow.',
                    'trainer_user_id' => $admin->getKey(),
                    'capacity' => 15,
                    'duration_minutes' => 60,
                    'is_active' => true,
                ]
            );

            $memberUser = User::query()->updateOrCreate(
                ['email' => 'member@gms.test'],
                [
                    'name' => 'Portal Member',
                    'password' => $defaultPassword,
                    'branch_id' => $mainBranch->getKey(),
                    'current_branch_id' => $mainBranch->getKey(),
                    'email_verified_at' => $seededAt,
                    'phone' => '+254700000010',
                ]
            );

            $memberUser->branches()->syncWithoutDetaching([
                $mainBranch->getKey() => ['is_default' => true],
            ]);

            $admin->syncRoles(['super-admin']);
            $manager->syncRoles(['branch-manager']);
            $reception->syncRoles(['receptionist']);
            $memberUser->syncRoles(['member']);

            $member = Member::query()->updateOrCreate(
                ['branch_id' => $mainBranch->getKey(), 'member_code' => 'MBR-001'],
                [
                    'branch_id' => $mainBranch->getKey(),
                    'user_id' => $memberUser->getKey(),
                    'first_name' => 'Demo',
                    'last_name' => 'Member',
                    'phone' => '+254700000010',
                    'email' => 'member@gms.test',
                    'status' => 'active',
                    'joined_at' => $seededAt->subDays(45),
                ]
            );

            Membership::query()->updateOrCreate(
                ['branch_id' => $mainBranch->getKey(), 'member_id' => $member->getKey(), 'membership_plan_id' => $basicPlan->getKey()],
                [
                    'branch_id' => $mainBranch->getKey(),
                    'status' => 'active',
                    'starts_at' => $seededAt->subDays(10),
                    'ends_at' => $seededAt->addDays(20),
                    'last_renewed_at' => $seededAt->subDays(10),
                    'remaining_visits' => null,
                    'total_visits' => null,
                    'auto_renew_enabled' => true,
                    'renewal_source' => 'manual',
                    'activated_at' => $seededAt->subDays(10),
                ]
            );

            Membership::query()->updateOrCreate(
                ['branch_id' => $mainBranch->getKey(), 'member_id' => $member->getKey(), 'membership_plan_id' => $tenPackPlan->getKey()],
                [
                    'branch_id' => $mainBranch->getKey(),
                    'status' => 'active',
                    'starts_at' => $seededAt->subDays(3),
                    'ends_at' => $seededAt->addDays(87),
                    'last_renewed_at' => $seededAt->subDays(3),
                    'remaining_visits' => 8,
                    'total_visits' => 10,
                    'auto_renew_enabled' => false,
                    'renewal_source' => 'manual',
                    'activated_at' => $seededAt->subDays(3),
                ]
            );

            $classSession = ClassSession::query()->updateOrCreate(
                ['branch_id' => $mainBranch->getKey(), 'gym_class_id' => $gymClass->getKey(), 'starts_at' => $seededAt->addDay()->startOfHour()],
                [
                    'branch_id' => $mainBranch->getKey(),
                    'ends_at' => $seededAt->addDay()->addHour(),
                    'capacity_override' => 15,
                    'status' => 'scheduled',
                ]
            );

            AttendanceSession::query()->updateOrCreate(
                ['branch_id' => $mainBranch->getKey(), 'member_id' => $member->getKey(), 'checked_in_at' => $seededAt->subDay()],
                [
                    'branch_id' => $mainBranch->getKey(),
                    'membership_id' => null,
                    'qr_token_id' => null,
                    'checked_out_at' => $seededAt->subDay()->addHours(2),
                    'check_in_ip' => '127.0.0.1',
                    'device_info' => [
                        'platform' => 'desktop',
                        'browser' => 'Chrome',
                        'source' => 'staff-seeder',
                    ],
                    'verified_by_user_id' => $admin->getKey(),
                    'performed_by_user_id' => $reception->getKey(),
                    'check_in_method' => 'manual',
                    'check_out_method' => 'manual',
                    'status' => 'closed',
                ]
            );

            ClassBooking::query()->updateOrCreate(
                ['branch_id' => $mainBranch->getKey(), 'class_session_id' => $classSession->getKey(), 'member_id' => $member->getKey()],
                [
                    'branch_id' => $mainBranch->getKey(),
                    'booked_by_user_id' => $reception->getKey(),
                    'status' => 'booked',
                    'waitlist_position' => null,
                    'booked_at' => $seededAt,
                    'confirmed_at' => $seededAt,
                ]
            );

            $strengthCategory = EquipmentCategory::query()->updateOrCreate(
                ['branch_id' => $mainBranch->getKey(), 'name' => 'Strength'],
                [
                    'branch_id' => $mainBranch->getKey(),
                ]
            );

            EquipmentItem::query()->updateOrCreate(
                ['branch_id' => $mainBranch->getKey(), 'asset_tag' => 'EQP-001'],
                [
                    'branch_id' => $mainBranch->getKey(),
                    'equipment_category_id' => $strengthCategory->getKey(),
                    'name' => 'Cable Machine',
                    'model' => 'Pro 4000',
                    'barcode' => 'EQP-001-BC',
                    'serial_number' => 'SN-4000',
                    'purchase_date' => $seededAt->subYear()->toDateString(),
                    'warranty_expires_at' => $seededAt->addYear()->toDateString(),
                    'condition' => 'good',
                    'location' => 'Floor A',
                    'status' => 'available',
                ]
            );

            EquipmentItem::query()->updateOrCreate(
                ['branch_id' => $mainBranch->getKey(), 'asset_tag' => 'EQP-002'],
                [
                    'branch_id' => $mainBranch->getKey(),
                    'equipment_category_id' => $strengthCategory->getKey(),
                    'name' => 'Treadmill',
                    'model' => 'Run 200',
                    'barcode' => 'EQP-002-BC',
                    'serial_number' => 'SN-2000',
                    'purchase_date' => $seededAt->subYear()->toDateString(),
                    'warranty_expires_at' => $seededAt->addMonths(8)->toDateString(),
                    'condition' => 'fair',
                    'location' => 'Cardio Floor',
                    'status' => 'under_repair',
                ]
            );

            StockItem::query()->updateOrCreate(
                ['branch_id' => $mainBranch->getKey(), 'sku' => 'SKU-001'],
                [
                    'branch_id' => $mainBranch->getKey(),
                    'name' => 'Protein Bar',
                    'unit' => 'piece',
                    'barcode' => 'BAR-001',
                    'expiry_date' => $seededAt->addMonths(6)->toDateString(),
                    'current_stock' => 48,
                    'reorder_level' => 12,
                    'cost_price' => 90,
                    'sale_price' => 150,
                    'is_active' => true,
                ]
            );

            // Seed Trainers matching the web landing page
            $trainersData = [
                [
                    'first_name' => 'Alex',
                    'last_name' => 'Morgan',
                    'email' => 'alex.morgan@fithub.com',
                    'phone' => '+251911000001',
                    'bio' => 'Certified Strength & Conditioning Specialist with 8+ years experience training elite athletes.',
                    'specializations' => 'Strength & Conditioning',
                    'image_url' => '/images/trainer-alex.jpg',
                    'is_active' => true,
                ],
                [
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'email' => 'sarah.johnson@fithub.com',
                    'phone' => '+251911000002',
                    'bio' => 'High-energy HIIT coach focusing on endurance, fat burn, and explosive movement.',
                    'specializations' => 'HIIT & Functional Training',
                    'image_url' => '/images/trainer-sarah.jpg',
                    'is_active' => true,
                ],
                [
                    'first_name' => 'Daniel',
                    'last_name' => 'Carter',
                    'email' => 'daniel.carter@fithub.com',
                    'phone' => '+251911000003',
                    'bio' => 'Personal coach dedicated to custom weight management and muscle building programs.',
                    'specializations' => 'Personal Training',
                    'image_url' => '/images/trainer-daniel.jpg',
                    'is_active' => true,
                ],
                [
                    'first_name' => 'Maya',
                    'last_name' => 'Williams',
                    'email' => 'maya.williams@fithub.com',
                    'phone' => '+251911000004',
                    'bio' => 'Holistic mobility specialist helping athletes recover and build core functional flexibility.',
                    'specializations' => 'Yoga & Mobility',
                    'image_url' => '/images/trainer-maya.jpg',
                    'is_active' => true,
                ],
            ];

            foreach ($trainersData as $tData) {
                \App\Models\Trainer::query()->updateOrCreate(
                    [
                        'branch_id' => $mainBranch->getKey(),
                        'first_name' => $tData['first_name'],
                        'last_name' => $tData['last_name'],
                    ],
                    array_merge($tData, ['branch_id' => $mainBranch->getKey()])
                );
            }

            $branchContext->clear();
        } finally {
            $branchContext->clear();
        }
    }
}
