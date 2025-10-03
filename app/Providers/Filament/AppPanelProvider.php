<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\TaskBoard;
use Filament\Http\Middleware\{Authenticate, AuthenticateSession, DisableBladeIconComponents, DispatchServingFilamentEvent};
use Filament\Pages\Dashboard;
use Filament\{Panel, PanelProvider};
use Filament\Support\Colors\Color;
use Filament\Support\Enums\{Platform, Width};
use Filament\Widgets\{AccountWidget, FilamentInfoWidget};
use Illuminate\Cookie\Middleware\{AddQueuedCookiesToResponse, EncryptCookies};
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Yebor974\Filament\RenewPassword\RenewPasswordPlugin;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->viteTheme('resources/css/filament/app/theme.css')
            ->id('app')
            ->path('app')
            ->login()
            ->databaseNotifications()
            ->passwordReset()
            ->brandLogo(asset('images/logo.png'))
            ->colors([
                'primary' => Color::Purple,
            ])
            ->brandLogoHeight('3rem')
            ->topbar(true)
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->globalSearchFieldSuffix(fn (): ?string => match (Platform::detect()) {
                Platform::Windows, Platform::Linux => 'CTRL+K',
                Platform::Mac => '⌘K',
                default => null,
            })
            ->pages([
                Dashboard::class,
                TaskBoard::class
            ])
            ->plugins([
                RenewPasswordPlugin::make()
                    ->forceRenewPassword(),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
