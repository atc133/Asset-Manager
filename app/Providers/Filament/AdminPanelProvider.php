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
use App\Filament\Widgets\LowStockConsumablesWidget;
use Filament\View\PanelsRenderHook;

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

                        ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => <<<'HTML'
                    <style>
                        .nwam-auth-bg {
                            display: none;
                        }

                        body:has(.fi-simple-layout) {
                            overflow: hidden;
                        }

                        body:has(.fi-simple-layout) .nwam-auth-bg {
                            display: block;
                            position: fixed;
                            inset: 0;
                            z-index: 0;
                            background:
                                radial-gradient(circle at 20% 20%, rgba(220, 58, 141, 0.35), transparent 35%),
                                radial-gradient(circle at 80% 20%, rgba(64, 141, 203, 0.35), transparent 35%),
                                linear-gradient(135deg, rgba(15, 23, 42, 0.70), rgba(15, 23, 42, 0.35)),
                                url('/Images/NETWORRKERS.png') center center / cover no-repeat;
                            transform: scale(1.03);
                        }

                        body:has(.fi-simple-layout) .fi-simple-layout {
                            position: relative;
                            z-index: 1;
                            min-height: 100vh;
                            background: transparent !important;
                        }

                        body:has(.fi-simple-layout) .fi-simple-main-ctn {
                            background: transparent !important;
                        }

                        body:has(.fi-simple-layout) .fi-simple-main {
                            background: rgba(255, 255, 255, 0.13) !important;
                            backdrop-filter: blur(24px) saturate(145%) !important;
                            -webkit-backdrop-filter: blur(24px) saturate(145%) !important;
                            border: 1px solid rgba(255, 255, 255, 0.32) !important;
                            border-radius: 28px !important;
                            box-shadow: 0 30px 90px rgba(0, 0, 0, 0.50) !important;
                        }

                        body:has(.fi-simple-layout) .fi-simple-header-heading,
                        body:has(.fi-simple-layout) .fi-fo-field-wrp-label,
                        body:has(.fi-simple-layout) label {
                            color: #ffffff !important;
                            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.45);
                        }

                        body:has(.fi-simple-layout) .fi-input-wrp {
                            background: rgba(255, 255, 255, 0.86) !important;
                            border-radius: 14px !important;
                        }

                        body:has(.fi-simple-layout) .fi-btn {
                            border-radius: 14px !important;
                            box-shadow: 0 12px 30px rgba(64, 141, 203, 0.35) !important;
                        }

                        body:has(.fi-simple-layout) .fi-logo {
                            filter: drop-shadow(0 10px 24px rgba(0, 0, 0, 0.45));
                        }
                    </style>
                HTML
            )
->renderHook(
    PanelsRenderHook::BODY_START,
    fn (): string => '<div class="nwam-auth-bg"></div>'
)

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
                LowStockConsumablesWidget::class,
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