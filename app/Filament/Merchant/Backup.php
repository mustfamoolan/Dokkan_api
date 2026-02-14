<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class Backup extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-down';

    protected static string $view = 'filament.pages.backup';

    protected static ?string $navigationGroup = 'نظام المحل';

    protected static ?string $title = 'النسخ الاحتياطي';

    public static function getNavigationLabel(): string
    {
        return 'النسخ الاحتياطي';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createBackup')
                ->label('إنشاء نسخة احتياطية الآن')
                ->icon('heroicon-m-plus')
                ->color('success')
                ->action(function () {
                    // Logic for backup would go here
                    Notification::make()
                        ->title('تم بدء عملية النسخ الاحتياطي')
                        ->success()
                        ->send();
                }),
        ];
    }
}
