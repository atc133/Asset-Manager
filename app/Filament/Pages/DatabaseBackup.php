<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class DatabaseBackup extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static ?string $navigationLabel = 'Database Backup';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.database-backup';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('manage_backups') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_backup')
                ->label('Download Database Backup')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('database.backup.download'))
                ->openUrlInNewTab(),
        ];
    }
}