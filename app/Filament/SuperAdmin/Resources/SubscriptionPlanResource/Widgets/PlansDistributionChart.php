<?php

namespace App\Filament\SuperAdmin\Resources\SubscriptionPlanResource\Widgets;

use App\Models\SubscriptionPlan;
use Filament\Widgets\ChartWidget;

class PlansDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'توزيع المشتركين على الباقات';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $plans = SubscriptionPlan::withCount([
            'subscriptions' => function ($query) {
                $query->where('end_date', '>', now());
            }
        ])->get();

        return [
            'datasets' => [
                [
                    'label' => 'عدد المشتركين النشطين',
                    'data' => $plans->pluck('subscriptions_count')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6',
                        '#ef4444',
                        '#10b981',
                        '#f59e0b',
                        '#8b5cf6',
                        '#ec4899',
                        '#6366f1',
                        '#14b8a6',
                        '#f43f5e',
                        '#84cc16',
                    ],
                ],
            ],
            'labels' => $plans->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
