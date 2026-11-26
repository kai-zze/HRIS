<?php

namespace App\Filament\Resources\PayrollRuns\Pages;

use App\Filament\Resources\PayrollRuns\PayrollRunResource;
use App\Services\BankExportService;
use App\Services\PayrollComputationService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPayrollRun extends ViewRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('compute')
                ->label('Recalculate Run')
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->action(function () {
                    app(PayrollComputationService::class)->computePayrollRun($this->record);
                    Notification::make()->title('Payroll Recalculated')->success()->send();
                }),

            Action::make('export_bank')
                ->label('Export Bank CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(fn () => app(BankExportService::class)->exportPayrollRunCsv($this->record)),

            EditAction::make(),
        ];
    }
}
