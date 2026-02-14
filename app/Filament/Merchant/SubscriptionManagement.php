<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Subscription;
use Filament\Support\Colors\Color;

class SubscriptionManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.pages.subscription-management';

    protected static ?string $navigationGroup = 'نظام المنصة';

    protected static ?string $title = 'الاشتراك والدفع';

    public $subscription;

    public function mount()
    {
        $this->subscription = auth()->user()->subscription;
    }

    public static function getNavigationLabel(): string
    {
        return 'الاشتراك والدفع';
    }
}
