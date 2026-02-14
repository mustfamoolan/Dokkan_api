<?php

namespace App\Filament\Resources\CashboxTransactionResource\Pages;

use App\Filament\Resources\CashboxTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashboxTransactions extends ListRecords
{
    protected static string $resource = CashboxTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
