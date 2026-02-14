<?php

namespace App\Filament\Resources\YesResource\Widgets;

use Filament\Widgets\ChartWidget;

class BestSellers extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bubble';
    }
}
