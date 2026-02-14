<?php

namespace App\Filament\SuperAdmin\Resources\SubscriptionPlanResource\Widgets;

use App\Models\SubscriptionPlan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class PlanStatsOverview extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        // If we are on a View/Edit page, we might have access to the record.
        // However, widgets on resource pages usually need to handle context correctly.
        // Assuming this widget is displayed on the ViewSubscriptionPlan page, $this->record should be available if passed correctly.

        if (!$this->record) {
            return [];
        }

        $plan = $this->record;

        $totalSubscribers = $plan->subscriptions()->where('is_active', true)->count();
        $newSubscribersThisMonth = $plan->subscriptions()->where('created_at', '>=', now()->startOfMonth())->count();
        $expiringSoon = $plan->subscriptions()->where('end_date', '<=', now()->addDays(7))->where('end_date', '>=', now())->count();

        // Revenue calculation (approximation based on plan price * count, or separate payments table)
        // For now, let's assume all active subscriptions paid the plan price once.
        // A better approach would be summing actual payment records.
        $totalRevenue = $plan->subscriptions()->count() * $plan->price;

        $expectedRevenueNextMonth = $plan->subscriptions()->where('is_active', true)->count() * $plan->price; // Crude estimate for recurring

        return [
            Stat::make('المشتركين النشطين', $totalSubscribers)
                ->description('إجمالي المشتركين حالياً')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('مشتركين جدد (هذا الشهر)', $newSubscribersThisMonth)
                ->description('نمو الاشتراكات')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('تنتهي قريباً', $expiringSoon)
                ->description('اشتراكات تنتهي خلال 7 أيام')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('إجمالي الدخل المقدر', number_format($totalRevenue) . ' IQD')
                ->description('بناءً على عدد الاشتراكات الكلي')
                ->color('success'),
        ];
    }
}
