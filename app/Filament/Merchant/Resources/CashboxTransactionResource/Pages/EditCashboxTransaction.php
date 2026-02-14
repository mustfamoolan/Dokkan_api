<?php

namespace App\Filament\Resources\CashboxTransactionResource\Pages;

use App\Filament\Resources\CashboxTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashboxTransaction extends EditRecord
{
    protected static string $resource = CashboxTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
