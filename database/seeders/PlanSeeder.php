<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::create([
            'name' => 'Starter',
            'max_users' => 5,
            'price' => 0.00,
            'billing_cycle' => 'monthly',
        ]);

        Plan::create([
            'name' => 'Business',
            'max_users' => 10,
            'price' => 99.90,
            'billing_cycle' => 'monthly',
        ]);

        Plan::create([
            'name' => 'Enterprise',
            'max_users' => 20,
            'price' => 199.90,
            'billing_cycle' => 'monthly',
        ]);
    }
}
