<?php

namespace App\Filament\Pages\Concerns;

use Filament\Actions\Action;

trait HasReportExports
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->url(route('reports.export.excel', [
                    'report' => static::$reportKey,
                ]))
                ->openUrlInNewTab(),

            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-text')
                ->url(route('reports.export.pdf', [
                    'report' => static::$reportKey,
                ]))
                ->openUrlInNewTab(),
        ];
    }
}