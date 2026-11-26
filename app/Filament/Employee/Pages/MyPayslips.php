<?php

namespace App\Filament\Employee\Pages;

use App\Models\Employee;
use App\Models\PayrollItem;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class MyPayslips extends Page
{
    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static string | UnitEnum | null $navigationGroup = 'My Self-Service';

    protected static ?string $navigationLabel = 'My Payslips';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.employee.pages.my-payslips';

    public ?Employee $employee = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->employee = $user?->employee ?? Employee::withoutGlobalScopes()->where('email', $user?->email)->first();
    }

    public function getPayslipsProperty()
    {
        if (! $this->employee) {
            return collect();
        }

        return PayrollItem::with(['payrollRun'])
            ->where('employee_id', $this->employee->id)
            ->whereHas('payrollRun', fn ($q) => $q->whereIn('status', ['approved', 'locked']))
            ->orderByDesc('id')
            ->get();
    }

    public function downloadPayslip(int $itemId): StreamedResponse
    {
        $item = PayrollItem::with(['payrollRun', 'payrollRun.entity', 'employee', 'employee.department', 'employee.position'])
            ->where('id', $itemId)
            ->where('employee_id', $this->employee?->id)
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.payslip', ['item' => $item]);
        $filename = sprintf(
            'Payslip-%s-%s.pdf',
            $item->employee->employee_number,
            $item->payrollRun->payout_date->format('Ymd')
        );

        Notification::make()
            ->title('Downloading Payslip')
            ->body("Generating official PDF payslip for {$item->payrollRun->name}...")
            ->success()
            ->send();

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }
}
