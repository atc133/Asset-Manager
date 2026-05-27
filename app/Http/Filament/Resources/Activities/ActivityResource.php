<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\ListActivities;
use App\Models\Asset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;
use UnitEnum;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Audit Log';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Event')
                    ->badge()
                    ->sortable(),

                TextColumn::make('subject_type')  
                    ->label('Model')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')
                    ->sortable(),

                TextColumn::make('asset_tag')
                    ->label('Asset Tag')
                    ->state(function (Activity $record): string {
                        $subject = $record->subject;

                        if (! $subject) {
                            return '-';
                        }

                        if ($subject instanceof Asset) {
                            return $subject->asset_tag ?? '-';
                        }

                        if (method_exists($subject, 'asset') && $subject->asset) {
                            return $subject->asset->asset_tag ?? '-';
                        }

                        return '-';
                    })
                    ->searchable(false)
                    ->sortable(false),

                TextColumn::make('causer.name')
                    ->label('User')
                    ->placeholder('System')
                    ->sortable(),

                TextColumn::make('changes_text')
                    ->label('Changes')
                    ->state(function (Activity $record): string {
                        $changes = $record->attribute_changes;

                        if ($changes instanceof \Illuminate\Support\Collection) {
                            $changes = $changes->toArray();
                        }

                        if (is_string($changes)) {
                            $changes = json_decode($changes, true);
                        }

                        if (! is_array($changes) || empty($changes)) {
                            return '-';
                        }

                        $newValues = $changes['attributes']
                            ?? $changes['new']
                            ?? $changes['changes']
                            ?? [];

                        $oldValues = $changes['old']
                            ?? $changes['old_values']
                            ?? [];

                        if (empty($newValues)) {
                            return json_encode($changes, JSON_UNESCAPED_UNICODE);
                        }

                        return collect($newValues)
                            ->map(function ($newValue, $field) use ($oldValues) {
                                $oldValue = $oldValues[$field] ?? '-';

                                if (is_array($oldValue)) {
                                    $oldValue = json_encode($oldValue, JSON_UNESCAPED_UNICODE);
                                }

                                if (is_array($newValue)) {
                                    $newValue = json_encode($newValue, JSON_UNESCAPED_UNICODE);
                                }

                                return "{$field}: {$oldValue} → {$newValue}";
                            })
                            ->implode(' | ');
                    })
                    ->wrap()
                    ->toggleable(),
            ])

            ->filters([
    SelectFilter::make('event')
        ->label('Event')
        ->options([
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
        ]),

    SelectFilter::make('subject_type')
        ->label('Model')
        ->options([
            \App\Models\Asset::class => 'Asset',
            \App\Models\Employee::class => 'Employee',
            \App\Models\AssetAssignment::class => 'Asset Assignment',
            \App\Models\MaintenanceCase::class => 'Maintenance Case',
        ]),

    SelectFilter::make('causer_id')
    ->label('User')
    ->options(fn () => \App\Models\User::query()
        ->orderBy('name')
        ->pluck('name', 'id')
        ->toArray())
    ->query(function (Builder $query, array $data): Builder {
        if (! filled($data['value'] ?? null)) {
            return $query;
        }

        return $query
            ->where('causer_type', \App\Models\User::class)
            ->where('causer_id', $data['value']);
    })
    ->searchable()
    ->preload(),

    Filter::make('asset_tag')
        ->label('Asset Tag')
        ->schema([
            TextInput::make('asset_tag')
                ->label('Asset Tag')
                ->placeholder('Example: PC-0001'),
        ])
        ->query(function (Builder $query, array $data): Builder {
            $assetTag = $data['asset_tag'] ?? null;

            if (! $assetTag) {
                return $query;
            }

            return $query->where(function (Builder $query) use ($assetTag): void {
                $query
                    ->where('properties->attributes->asset_tag', 'like', "%{$assetTag}%")
                    ->orWhere('properties->old->asset_tag', 'like', "%{$assetTag}%");
            });
        }),
])
            ->defaultSort('created_at', 'desc');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_audit_log') ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
        ];
    }
}