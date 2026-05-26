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
                    ->color(fn (string $state): string => match ($state) {
                        'High' => 'danger',
                        'Medium' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),

                TextColumn::make('title')
                    ->label('Issue'),

                TextColumn::make('items')
                    ->label('Asset Tags / Items')
                    ->wrap(),

                TextColumn::make('details')
                    ->label('Details')
                    ->wrap(),
            ]);
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
                severity: $missingSerialItems->isNotEmpty() ? 'High' : 'Low',
                type: 'Assets',
                title: 'Missing serial numbers',
                details: 'Assets without valid serial number.',
                items: $missingSerialItems,
            ),

            $this->makeActionItem(
                severity: $repairOver30DaysItems->isNotEmpty() ? 'High' : 'Low',
                type: 'Repair',
                title: 'Repair over 30 days',
                details: 'Assets that have stayed in repair too long.',
                items: $repairOver30DaysItems,
            ),

            $this->makeActionItem(
                severity: $withoutLocationItems->isNotEmpty() ? 'Medium' : 'Low',
                type: 'Assets',
                title: 'Assets without location',
                details: 'Assets that do not have current location.',
                items: $withoutLocationItems,
            ),

            $this->makeActionItem(
                severity: $inactiveEmployeesItems->isNotEmpty() ? 'High' : 'Low',
                type: 'Employees',
                title: 'Inactive employees with assets',
                details: 'Inactive employees that still have assigned equipment.',
                items: $inactiveEmployeesItems,
            ),

            $this->makeActionItem(
                severity: $homeOfficeReturnItems->isNotEmpty() ? 'High' : 'Low',
                type: 'Home Office',
                title: 'Home office returns pending',
                details: 'Employees marked as needing to return equipment.',
                items: $homeOfficeReturnItems,
            ),

            $this->makeActionItem(
                severity: $openMaintenanceCaseItems->isNotEmpty() ? 'Medium' : 'Low',
                type: 'Maintenance',
                title: 'Open maintenance cases',
                details: 'Repair/maintenance cases that are still open.',
                items: $openMaintenanceCaseItems,
            ),
        ])->filter(fn (array $item): bool => filled($item['items']));
    }

    protected function makeActionItem(
        string $severity,
        string $type,
        string $title,
        string $details,
        Collection $items,
    ): array {
        return [
            'severity' => $severity,
            'type' => $type,
            'title' => $title,
            'items' => $items->take(15)->implode(', ') . ($items->count() > 15 ? ' +' . ($items->count() - 15) . ' more' : ''),
            'details' => $details,
        ];
    }
}