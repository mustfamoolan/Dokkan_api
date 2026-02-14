<?php

namespace App\Filament\SuperAdmin\Resources\SubscriptionPlanResource\Widgets;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // 1. Most Popular Plan
        $popularPlan = SubscriptionPlan::withCount('subscriptions')
            ->orderByDesc('subscriptions_count')
            ->first();

        // 2. Least Popular Plan
        $leastPopularPlan = SubscriptionPlan::withCount('subscriptions')
            ->orderBy('subscriptions_count')
            ->first();

        // 2. Total Active Subscribers (Global)
        $totalActiveSubscribers = Subscription::where('end_date', '>', now())->count();

        // 3. Total Monthly Revenue (Approximation based on active plans)
        // Calculating sum of prices for all active subscriptions
        // This assumes 'price' is monthly or standard. If plans have different durations, 
        // to get true MRR we'd need to normalize. For now, we sum the face value of active plans.
        $totalRevenue = Subscription::where('end_date', '>', now())
            ->join('subscription_plans', 'subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->sum('subscription_plans.price');

        return [
            Stat::make('أكثر باقة طلبًا', $popularPlan ? $popularPlan->name : 'لا يوجد بيانات')
                ->description($popularPlan ? "{$popularPlan->subscriptions_count} مشترك" : '-')
                ->descriptionIcon($popularPlan ? 'heroicon-m-user-group' : 'heroicon-m-x-circle')
                ->color('success'),

            Stat::make('أقل باقة طلبًا', $leastPopularPlan ? $leastPopularPlan->name : 'لا يوجد بيانات')
                ->description($leastPopularPlan ? "{$leastPopularPlan->subscriptions_count} مشترك" : '-')
                ->descriptionIcon($leastPopularPlan ? 'heroicon-m-user-minus' : 'heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('إجمالي المشتركين النشطين', $totalActiveSubscribers)
                ->description('في جميع الباقات')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('إجمالي الدخل المتوقع', number_format($totalRevenue) . ' IQD')
                ->description('بناءً على الاشتراكات النشطة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}
