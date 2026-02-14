<?php

namespace App\Filament\SuperAdmin\Resources\SubscriptionPlanResource\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class PlanGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'نمو المشتركين';

    public ?Model $record = null;

    protected function getData(): array
    {
        if (!$this->record) {
            return [];
        }

        $plan = $this->record;

        $data = Trend::query(
            \App\Models\Subscription::query()->where('subscription_plan_id', $plan->id)
        )
            ->between(
                start: now()->subMonths(6),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'اشتراكات جديدة',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
