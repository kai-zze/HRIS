<?php

namespace App\Providers\Filament;

use App\Http\Middleware\AutoLoginSuperAdmin;
use App\Filament\Widgets\LeaveCalendarWidget;
use App\Filament\Widgets\DocumentExpiryWidget;
use App\Filament\Widgets\EntityHeadcountStatsWidget;
use App\Filament\Widgets\EntityOverviewTableWidget;
use App\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
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
                NavigationGroup::make('Core HR'),
                NavigationGroup::make('Recruitment & Talent'),
                NavigationGroup::make('Performance & Growth'),
                NavigationGroup::make('Discipline & Employee Relations'),
                NavigationGroup::make('Time & Attendance'),
                NavigationGroup::make('Leave Management'),
                NavigationGroup::make('Organization'),
                NavigationGroup::make('Access Control'),
            ])
            ->userMenuItems([
                \Filament\Navigation\MenuItem::make()
                    ->label('Employee Portal')
                    ->icon('heroicon-o-user')
                    ->url(fn (): string => url('/portal')),
            ])
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => Blade::render('@livewire(\'entity-switcher\')'),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                EntityHeadcountStatsWidget::class,
                EntityOverviewTableWidget::class,
                DocumentExpiryWidget::class,
                LeaveCalendarWidget::class,
                \App\Filament\Widgets\TopPerformersLeaderboardWidget::class,
                \App\Filament\Widgets\HeadcountDistributionChartWidget::class,
                \App\Filament\Widgets\MonthlyPayrollExpensesChartWidget::class,
                \App\Filament\Widgets\AttendanceTrendChartWidget::class,
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
