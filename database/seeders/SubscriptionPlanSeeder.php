<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscriptionPlan::updateOrCreate(
            ['name' => 'Free Week'],
            [
                'price' => 0,
                'duration_days' => 7,
                'features' => [
                    'max_products' => 30,
                    'max_customers' => 50,
                    'is_trial' => true,
                ],
                'is_active' => true,
            ]
        );

        SubscriptionPlan::updateOrCreate(
            ['name' => 'Trial'],
            [
                'price' => 0,
                'duration_days' => 14,
                'features' => [
                    'max_products' => 50,
                    'max_customers' => 100,
                    'is_trial' => true,
                ],
                'is_active' => true,
            ]
        );

        SubscriptionPlan::updateOrCreate(
            ['name' => 'Monthly'],
            [
                'price' => 25000, // Example IQD
                'duration_days' => 30,
                'features' => [
                    'max_products' => 1000,
                    'max_customers' => 2000,
                ],
                'is_active' => true,
            ]
        );
    }
}
