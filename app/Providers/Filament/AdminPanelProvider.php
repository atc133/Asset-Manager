<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ActionNeededWidget;
use App\Filament\Widgets\ITStatsOverview;
use App\Filament\Widgets\OpenMaintenanceCasesWidget;
use App\Filament\Widgets\RecentAssignmentsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\ReturnOperationsOverview;
use App\Filament\Widgets\AssetHealthOverview;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->plugin(
                BreezyCore::make()
                    ->myProfile(
                        slug: 'my-profile',
                    )
                    ->enableTwoFactorAuthentication()
            )
            ->brandName('Asset Manager')
            ->brandLogo(asset('images/logo.png'))
            ->favicon(asset('images/favicon.ico'))
            ->maxContentWidth('full')
            ->colors([
                'primary' => Color::hex('#408dcb'),
                'danger' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'info' => Color::hex('#8dd4ed'),
                'gray' => Color::Slate,
            ])
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                AccountWidget::class,
                QuickActionsWidget::class,
                ReturnOperationsOverview::class,
                AssetHealthOverview::class,
                ITStatsOverview::class,
                ActionNeededWidget::class,
                RecentAssignmentsWidget::class,
                OpenMaintenanceCasesWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}