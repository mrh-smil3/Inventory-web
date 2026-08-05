<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Actions\Action;
use App\Filament\Pages\Settings;
use Illuminate\Support\HtmlString; 

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            // Logo Brand
            ->brandLogo(asset('logo.png'))
            ->brandName(config('app.name'))
            ->favicon(asset('logo.png'))
            ->brandLogoHeight('4rem')

            // ->brandLogo(new \Illuminate\Support\HtmlString(
            //     '<div style="display:flex;align-items:center;gap:10px">' .
            //     '<img src="' . asset('logo.png') . '" style="height:4rem" alt="Logo">' .
            //     '<span style="font-weight:700;font-size:1.1rem;color:#111827">' . config('app.name') . '</span>' .
            //     '</div>'
            // ))
            // ->brandLogoHeight('4rem')

            ->id('admin')
            ->path('admin')
            ->login()
            ->spa()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->databaseNotifications()
            ->globalSearch(false)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationSort(20),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->userMenuItems([
                Action::make('settings')
                ->url(fn (): string => Settings::getUrl())
                ->icon('heroicon-o-cog-6-tooth')
            ]);
    }
}
