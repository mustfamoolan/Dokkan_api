<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashboxTransactionResource\Pages;
use App\Filament\Resources\CashboxTransactionResource\RelationManagers;
use App\Models\CashboxTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CashboxTransactionResource extends Resource
{
    protected static ?string $model = CashboxTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'الماليات';

    protected static ?string $label = 'حركة صندوق';

    protected static ?string $pluralLabel = 'حركات الصندوق';

    protected static bool $isScopedToTenant = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل الحركة')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('نوع الحركة')
                            ->options([
                                'in' => 'إيداع',
                                'out' => 'سحب',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('source')
                            ->label('المصدر')
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ')
                            ->required()
                            ->numeric(),
                        Forms\Components\Textarea::make('note')
                            ->label('ملاحظة')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('source')
                    ->label('المصدر')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('IQD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashboxTransactions::route('/'),
            'create' => Pages\CreateCashboxTransaction::route('/create'),
            'edit' => Pages\EditCashboxTransaction::route('/{record}/edit'),
        ];
    }
}
