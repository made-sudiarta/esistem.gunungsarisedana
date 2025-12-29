<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->brandName('E-Sistem Koperasi')
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])


            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\TenggatBungaHariIni::class,
                \App\Filament\Widgets\TanggalJamWidget::class,
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->renderHook(
                'panels::head.end',
                fn () =>"<script>
                        window.addEventListener('open-new-tab', event => {
                            window.open(event.detail.url, '_blank');
                        });
                    </script>"
            )

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
            ])
            ->renderHook(
                        'panels::head.end',
                        fn () => '<style>
                            .custom-auth-empty-panel{
                                width: 100% !important;
                            }
                        </style>'
                    )


            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                AuthUIEnhancerPlugin::make()
                    ->formPanelPosition('right')
                    ->emptyPanelBackgroundImageUrl('https://images.pexels.com/photos/20237836/pexels-photo-20237836.jpeg')
                    ->emptyPanelBackgroundImageOpacity('70%')
                    ->showEmptyPanelOnMobile(false)
                    // ->formPanelWidth('100%')
            ]);
    }
}
