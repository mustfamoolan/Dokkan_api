<?php

namespace App\Filament\Widgets;

use App\Models\Store;
use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSubscribers = User::where('role', 'owner')->count();
        $totalStores = Store::count();
        $activeSubscriptions = Subscription::where('is_active', true)->count();

        // Simplified Revenue (sum of all subscriptions marked as paid)
        // In a real scenario, this would sum actual payment records.
        $totalRevenue = Subscription::where('payment_status', 'paid')->count() * 15000; // Assuming 15k IQD per subscription

        return [
            Stat::make('المشتركين الكلي', $totalSubscribers)
                ->description('أصحاب اشتراكات فعّالة')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('المحلات المفعلة', $totalStores)
                ->description('إجمالي الفروع والمحلات')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success'),
            Stat::make('الاشتراكات الفعالة', $activeSubscriptions)
                ->description('عدد الاشتراكات النشطة حالياً')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning'),
        ];
    }
}
