<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstallmentPlanResource\Pages;
use App\Filament\Resources\InstallmentPlanResource\RelationManagers;
use App\Models\InstallmentPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;

class InstallmentPlanResource extends Resource
{
    protected static ?string $model = InstallmentPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'نظام المحل';

    protected static ?string $label = 'خطة تقسيط';

    protected static ?string $pluralLabel = 'الأقساط';

    protected static bool $isScopedToTenant = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات خطة التقسيط')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('الزبون')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('invoice_id')
                            ->label('الفاتورة')
                            ->relationship('invoice', 'invoice_number')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('المبلغ الكلي')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('down_payment')
                            ->label('الدفعة الأولى')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('remaining_amount')
                            ->label('المبلغ المتبقي')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('installment_count')
                            ->label('عدد الأقساط')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('installment_value')
                            ->label('قيمة القسط')
                            ->required()
                            ->numeric(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('تاريخ البدء')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'active' => 'نشط',
                                'completed' => 'مكتمل',
                                'cancelled' => 'ملغي',
                            ])
                            ->required(),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('الزبون')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('المبلغ الكلي')
                    ->money('IQD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_amount')
                    ->label('المتبقي')
                    ->money('IQD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\Filter::make('due_today')
                    ->label('مستحق اليوم')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'active')->whereDate('start_date', '<=', today())),
                Tables\Filters\Filter::make('overdue')
                    ->label('متأخر')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'active')->where('remaining_amount', '>', 0)->whereDate('start_date', '<', today())),
            ])
            ->actions([
                Tables\Actions\Action::make('record_payment')
                    ->label('تسجيل دفعة')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('مبلغ الدفعة')
                            ->required()
                            ->numeric()
                            ->default(fn($record) => $record->installment_value),
                        Forms\Components\DatePicker::make('payment_date')
                            ->label('تاريخ الدفع')
                            ->required()
                            ->default(now()),
                        Forms\Components\Textarea::make('note')
                            ->label('ملاحظة'),
                    ])
                    ->action(function (InstallmentPlan $record, array $data): void {
                        // Create payment record
                        \App\Models\InstallmentPayment::create([
                            'installment_plan_id' => $record->id,
                            'amount' => $data['amount'],
                            'payment_date' => $data['payment_date'],
                            'note' => $data['note'],
                        ]);

                        // Create cashbox transaction
                        \App\Models\CashboxTransaction::create([
                            'store_id' => $record->store_id,
                            'type' => 'in',
                            'source' => 'installment',
                            'amount' => $data['amount'],
                            'reference_id' => $record->id,
                            'note' => 'دفعة قسط للزبون: ' . $record->customer->name,
                        ]);

                        // Update plan
                        $record->decrement('remaining_amount', $data['amount']);

                        if ($record->remaining_amount <= 0) {
                            $record->update(['status' => 'completed']);
                        }
                    }),
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
            'index' => Pages\ListInstallmentPlans::route('/'),
            'create' => Pages\CreateInstallmentPlan::route('/create'),
            'edit' => Pages\EditInstallmentPlan::route('/{record}/edit'),
        ];
    }
}
