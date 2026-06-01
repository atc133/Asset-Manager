<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Employees\EmployeeResource;
use App\Filament\Resources\MaintenanceCases\MaintenanceCaseResource;
use App\Filament\Resources\Users\UserResource;
use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected string $view = 'filament.widgets.quick-actions-widget';

    protected static ?int $sort = -9;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'actions' => [
                [
                    'label' => 'New Asset',
                    'description' => 'Register a new device',
                    'emoji' => '💻',
                    'color' => '#408dcb',
                    'url' => AssetResource::getUrl('create'),
                ],
                [
                    'label' => 'New Employee',
                    'description' => 'Create employee profile',
                    'emoji' => '👤',
                    'color' => '#dc3a8d',
                    'url' => EmployeeResource::getUrl('create'),
                ],
                [
                    'label' => 'Maintenance Case',
                    'description' => 'Open repair issue',
                    'emoji' => '🛠️',
                    'color' => '#8dd4ed',
                    'url' => MaintenanceCaseResource::getUrl('create'),
                ],
                [
                    'label' => 'New User',
                    'description' => 'Create system account',
                    'emoji' => '🛡️',
                    'color' => '#408dcb',
                    'url' => UserResource::getUrl('create'),
                ],
            ],
        ];
    }
}