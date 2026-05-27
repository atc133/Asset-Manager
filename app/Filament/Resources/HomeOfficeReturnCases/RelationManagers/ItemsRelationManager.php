<?php

namespace App\Filament\Resources\HomeOfficeReturnCases\RelationManagers;

use App\Models\Asset;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('asset_id')
                ->label('Asset')
                ->options(fn () => Asset::query()
                    ->orderBy('asset_tag')
                    ->pluck('asset_tag', 'id')
                    ->toArray())
                ->searchable()
                ->required(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Pending',
                    'returned' => 'Returned',
                    'damaged' => 'Damaged',
                    'missing' => 'Missing',
                    'replaced' => 'Replaced',
                    'not_required' => 'Not Required',
                ])
                ->required()
                ->default('pending'),

            Select::make('replacement_asset_id')
                ->label('Replacement Asset')
                ->options(fn () => Asset::query()
                    ->orderBy('asset_tag')
                    ->pluck('asset_tag', 'id')
                    ->toArray())
                ->searchable(),

            Select::make('condition_on_return')
                ->label('Condition On Return')
                ->options([
                    'new' => 'New',
                    'good' => 'Good',
                    'used' => 'Used',
                    'needs_check' => 'Needs Check',
                    'damaged' => 'Damaged',
                    'broken' => 'Broken',
                    'missing_serial' => 'Missing Serial',
                ]),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(3),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.asset_tag')
                    ->label('Asset')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('replacementAsset.asset_tag')
                    ->label('Replacement')
                    ->placeholder('-'),

                TextColumn::make('condition_on_return')
                    ->label('Condition')
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('returned_at')
                    ->label('Returned At')
                    ->dateTime()
                    ->placeholder('-'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}