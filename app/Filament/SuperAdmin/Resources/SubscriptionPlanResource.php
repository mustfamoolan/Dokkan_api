<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\SubscriptionPlanResource\Pages;
use App\Filament\SuperAdmin\Resources\SubscriptionPlanResource\RelationManagers;
use App\Filament\SuperAdmin\Resources\SubscriptionPlanResource\Widgets\PlanStatsOverview;
use App\Filament\SuperAdmin\Resources\SubscriptionPlanResource\Widgets\PlanGrowthChart;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'إدارة الاشتراكات';

    protected static ?string $label = 'باقة اشتراك';

    protected static ?string $pluralLabel = 'باقات الاشتراكات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل الباقة')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الباقة')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_free')
                            ->label('باقة مجانية')
                            ->onIcon('heroicon-m-gift')
                            ->offIcon('heroicon-m-currency-dollar')
                            ->live()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?bool $state) {
                                if ($state) {
                                    $set('price', 0);
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('price')
                            ->label('سعر الاشتراك')
                            ->numeric()
                            ->required()
                            ->prefix('IQD')
                            ->disabled(fn(Forms\Get $get): bool => $get('is_free'))
                            ->dehydrated(),
                        Forms\Components\Select::make('duration_days')
                            ->label('مدة الاشتراك')
                            ->options([
                                7 => 'أسبوع واحد',
                                30 => 'شهر واحد',
                                90 => '3 أشهر',
                                180 => '6 أشهر',
                                365 => 'سنة واحدة',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('المميزات')
                    ->schema([
                        Forms\Components\Repeater::make('features')
                            ->label('مميزات الباقة')
                            ->schema([
                                Forms\Components\TextInput::make('feature')
                                    ->label('الميزة')
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الباقة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('IQD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_days')
                    ->label('المدة (أيام)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'view' => Pages\ViewSubscriptionPlan::route('/{record}'),
            'edit' => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            PlanStatsOverview::class,
            PlanGrowthChart::class,
        ];
    }
}
