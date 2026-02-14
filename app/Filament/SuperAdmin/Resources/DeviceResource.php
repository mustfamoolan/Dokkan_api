<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\DeviceResource\Pages;
use App\Models\Device;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationGroup = 'إدارة المنصة';

    protected static ?string $label = 'جهاز';

    protected static ?string $pluralLabel = 'الأجهزة والمزامنة';

    protected static bool $isScopedToTenant = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الجهاز')
                    ->schema([
                        Forms\Components\TextInput::make('device_name')
                            ->label('اسم الجهاز')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('device_id')
                            ->label('معرف الجهاز الفريد')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('last_sync_at')
                            ->label('آخر وقت مزامنة')
                            ->disabled(),
                        Forms\Components\Select::make('sync_status')
                            ->label('حالة المزامنة')
                            ->options([
                                'good' => 'ممتازة',
                                'delayed' => 'متأخرة',
                                'error' => 'يوجد أخطاء',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true)
                            ->required(),
                        Forms\Components\Textarea::make('last_error')
                            ->label('آخر خطأ مسجل')
                            ->columnSpanFull()
                            ->disabled(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('store.name')
                    ->label('المحل')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('device_name')
                    ->label('اسم الجهاز')
                    ->searchable(),
                Tables\Columns\TextColumn::make('device_id')
                    ->label('المعرف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_sync_at')
                    ->label('آخر مزامنة')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sync_status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'good' => 'success',
                        'delayed' => 'warning',
                        'error' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'good' => 'ممتازة',
                        'delayed' => 'متأخرة',
                        'error' => 'يوجد أخطاء',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}
