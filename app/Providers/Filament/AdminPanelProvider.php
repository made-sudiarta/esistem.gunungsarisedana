<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Enums\ThemeMode;
use Filament\Support\Facades\FilamentAsset;
use Filament\Pages\Auth\Login;
use Filament\Support\Assets\Css;

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
            // ->login(Login::class)
            ->registration()
            ->font('Poppins')
            ->brandName('KSP. Gunung Sari')
            // ->brandLogo(fn () => view('filament.admin.logo'))
            ->brandLogoHeight('2rem')
            ->sidebarCollapsibleOnDesktop()
            ->defaultThemeMode(ThemeMode::Light)
            ->colors([
                'primary' => Color::Lime,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            // ->discoverWidgets(
            //     in: app_path('Filament/Widgets'),
            //     for: 'App\\Filament\\Widgets'
            // )
            ->widgets([
                \App\Filament\Widgets\StatistikAnggotaWidget::class,
                \App\Filament\Widgets\KreditBulananTahunanChart::class,
                \App\Filament\Widgets\KreditHarianTahunanChart::class,
                \App\Filament\Widgets\AbsensiKolektorWidget::class,
                \App\Filament\Widgets\AbsensiStaffWidget::class,
                \App\Filament\Widgets\TransaksiSuperAdminWidget::class,
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
                ->formPanelWidth('90%')
                ->emptyPanelBackgroundImageUrl(asset('images/auth-illustration.png'))
                ->emptyPanelBackgroundImageOpacity('100%')
                ->showEmptyPanelOnMobile(false)
            ])
            ->navigationGroups([
                'Karyawan',
                'Simpanan Berjangka',
                'Pinjaman Bulanan',
                'Pinjaman Harian',
                'Keanggotaan',
                'Setting',
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('sidebar-style', asset('css/sidebar-style.css')),
        ]);
    }
}
