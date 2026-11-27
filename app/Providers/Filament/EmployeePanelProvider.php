<?php

namespace App\Providers\Filament;

use App\Enums\RoleName;
use App\Http\Middleware\AutoLoginSuperAdmin;
use App\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class EmployeePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('portal')
            ->path('portal')
            ->spa(hasPrefetching: true)
            ->unsavedChangesAlerts()
            ->brandName('KM100 Group')
            ->brandLogo(fn () => view('filament.admin.components.brand-logo'))
            ->brandLogoHeight('3.25rem')
            ->favicon(asset('favicon.svg'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->font('Public Sans')
            ->monoFont('JetBrains Mono')
            ->colors([
                'primary' => [
                    50 => '#FFF8ED',
                    100 => '#FFEFD4',
                    200 => '#FFDCA8',
                    300 => '#FFC270',
                    400 => '#F89D38',
                    500 => '#F2851D',
                    600 => '#C2510A',
                    700 => '#9E3D08',
                    800 => '#7F310E',
                    900 => '#682A0F',
                    950 => '#381205',
                ],
                'gray' => Color::Zinc,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'danger' => Color::Rose,
                'info' => Color::Sky,
            ])
            ->navigationGroups([
                NavigationGroup::make('My Self-Service'),
                NavigationGroup::make('My Attendance & Leave'),
                NavigationGroup::make('Company Info'),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Admin / HR Panel')
                    ->icon('heroicon-o-shield-check')
                    ->url(fn (): string => url('/admin'))
                    ->visible(fn (): bool => auth()->user()?->hasAnyRole([
                        RoleName::SuperAdmin->value,
                        RoleName::HrAdmin->value,
                        RoleName::Manager->value,
                    ])),
            ])
            ->discoverResources(in: app_path('Filament/Employee/Resources'), for: 'App\Filament\Employee\Resources')
            ->discoverPages(in: app_path('Filament/Employee/Pages'), for: 'App\Filament\Employee\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Employee/Widgets'), for: 'App\Filament\Employee\Widgets')
            ->widgets([
                \App\Filament\Employee\Widgets\EmployeeWelcomeWidget::class,
                \App\Filament\Employee\Widgets\EmployeeLeaveStatsWidget::class,
                \App\Filament\Employee\Widgets\EmployeeRecentDtrWidget::class,
                \App\Filament\Employee\Widgets\EmployeeAnnouncementsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                AutoLoginSuperAdmin::class,
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
