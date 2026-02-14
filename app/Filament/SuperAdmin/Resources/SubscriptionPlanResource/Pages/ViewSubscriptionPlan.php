<?php

namespace App\Filament\SuperAdmin\Resources\SubscriptionPlanResource\Pages;

use App\Filament\SuperAdmin\Resources\SubscriptionPlanResource;
use App\Filament\SuperAdmin\Resources\SubscriptionPlanResource\Widgets\PlanStatsOverview;
use App\Filament\SuperAdmin\Resources\SubscriptionPlanResource\Widgets\PlanGrowthChart;
use Filament\Resources\Pages\ViewRecord;

class ViewSubscriptionPlan extends ViewRecord
{
    protected static string $resource = SubscriptionPlanResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            PlanStatsOverview::class,
            PlanGrowthChart::class,
        ];
    }
}
