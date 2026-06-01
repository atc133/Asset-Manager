<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\MaintenanceCase;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ActionNeededWidget extends TableWidget
{
    protected static ?string $heading = 'Action Needed';

    protected static ?int $sort = -8;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Asset::query()->whereRaw('1 = 0'))
            ->records(fn (): Collection => $this->getActionItems())
            ->columns([
                TextColumn::make('severity')
                    ->label('Priority')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        'High' => 'heroicon-m-exclamation-triangle',
                        'Medium' => 'heroicon-m-clock',
                        default => 'heroicon-m-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'High' => 'danger',
                        'Medium' => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('type')
                    ->label('Area')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Assets' => 'primary',
                        'Repair' => 'warning',
                        'Employees' => 'info',
                        'Home Office' => 'gray',
                        'Maintenance' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('title')
                    ->label('Issue')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('count')
                    ->label('Count')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success')
                    ->alignCenter(),

                TextColumn::make('items')
                    ->label('Asset Tags / Items')
                    ->wrap()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Copied items')
                    ->copyMessageDuration(1500),

                TextColumn::make('details')
                    ->label('Details')
                    ->wrap()
                    ->toggleable(),
            ])
            ->defaultSort('severity', 'desc')
            ->emptyStateHeading('No action needed')
            ->emptyStateDescription('Everything looks clean. No critical asset issues found.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    protected function getActionItems(): Collection
    {
        $missingSerialItems = Asset::query()
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('serial_number')
                    ->orWhere('serial_number', '')
                    ->orWhere('serial_number', '-')
                    ->orWhere('serial_number', '????')
                    ->orWhere('serial_number', 'NO LABEL');
            })
            ->pluck('asset_tag')
            ->filter()
            ->values();

        $repairOver30DaysItems = Asset::query()
            ->where('status', 'in_repair')
            ->where('updated_at', '<=', now()->subDays(30))
            ->pluck('asset_tag')
            ->filter()
            ->values();

        $withoutLocationItems = Asset::query()
            ->whereNull('current_location_id')
            ->pluck('asset_tag')
            ->filter()
            ->values();

        $inactiveEmployeesItems = Employee::query()
            ->where('status', 'inactive')
            ->whereHas('assets')
            ->pluck('full_name')
            ->filter()
            ->values();

        $homeOfficeReturnItems = Employee::query()
            ->whereIn('work_mode', ['home_office', 'hybrid'])
            ->where('return_required', true)
            ->pluck('full_name')
            ->filter()
            ->values();

        $openMaintenanceCaseItems = MaintenanceCase::query()
            ->with('asset')
            ->whereIn('status', ['open', 'in_progress'])
            ->get()
            ->map(fn (MaintenanceCase $case): string => $case->asset?->asset_tag
                ? $case->asset->asset_tag . ' - ' . ($case->issue ?? 'Maintenance case')
                : 'Case #' . $case->id)
            ->values();

        return collect([
            $this->makeActionItem(
                severity: 'High',
                type: 'Assets',
                title: 'Missing serial numbers',
                details: 'Assets without a valid serial number. Fix these first because they reduce traceability.',
                items: $missingSerialItems,
            ),

            $this->makeActionItem(
                severity: 'High',
                type: 'Repair',
                title: 'Repair over 30 days',
                details: 'Assets that have stayed in repair too long. These need technical follow-up.',
                items: $repairOver30DaysItems,
            ),

            $this->makeActionItem(
                severity: 'Medium',
                type: 'Assets',
                title: 'Assets without location',
                details: 'Assets that do not have a current location. This creates inventory risk.',
                items: $withoutLocationItems,
            ),

            $this->makeActionItem(
                severity: 'High',
                type: 'Employees',
                title: 'Inactive employees with assets',
                details: 'Inactive employees that still have assigned equipment.',
                items: $inactiveEmployeesItems,
            ),

            $this->makeActionItem(
                severity: 'High',
                type: 'Home Office',
                title: 'Home office returns pending',
                details: 'Employees marked as needing to return equipment.',
                items: $homeOfficeReturnItems,
            ),

            $this->makeActionItem(
                severity: 'Medium',
                type: 'Maintenance',
                title: 'Open maintenance cases',
                details: 'Repair or maintenance cases that are still open.',
                items: $openMaintenanceCaseItems,
            ),
        ])->filter(fn (array $item): bool => $item['count'] > 0);
    }

    protected function makeActionItem(
        string $severity,
        string $type,
        string $title,
        string $details,
        Collection $items,
    ): array {
        $count = $items->count();

        return [
            'severity' => $severity,
            'type' => $type,
            'title' => $title,
            'count' => $count,
            'items' => $items->take(20)->implode(', ') . ($count > 20 ? ' +' . ($count - 20) . ' more' : ''),
            'details' => $details,
        ];
    }
}