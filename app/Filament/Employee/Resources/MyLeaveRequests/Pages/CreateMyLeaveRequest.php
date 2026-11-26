<?php

namespace App\Filament\Employee\Resources\MyLeaveRequests\Pages;

use App\Enums\RequestStatus;
use App\Filament\Employee\Resources\MyLeaveRequests\MyLeaveRequestResource;
use App\Models\Employee;
use App\Services\LeaveBalanceService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateMyLeaveRequest extends CreateRecord
{
    protected static string $resource = MyLeaveRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $employee = $user?->employee ?? Employee::withoutGlobalScopes()->where('email', $user?->email)->first();

        if (! $employee) {
            Notification::make()
                ->title('No Employee Profile Linked')
                ->body('Unable to file leave: your account is not associated with an active employee record.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'leave_type_id' => 'No active employee profile linked to user.',
            ]);
        }

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        if (! empty($data['is_half_day'])) {
            $days = 0.5;
        } else {
            $days = $startDate->diffInDaysFiltered(fn (Carbon $date) => ! $date->isSunday(), $endDate) + 1;
        }

        // Validate credit balance
        $leaveBalanceService = app(LeaveBalanceService::class);
        $available = $leaveBalanceService->getAvailableBalance($employee, $data['leave_type_id'], (int) $startDate->year);

        if ($available < $days) {
            Notification::make()
                ->title('Insufficient Leave Balance')
                ->body(sprintf('You only have %.1f day(s) remaining for this leave type, but requested %.1f day(s).', $available, $days))
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'data.leave_type_id' => sprintf('Insufficient leave credits. Remaining: %.1f, Requested: %.1f', $available, $days),
            ]);
        }

        $data['employee_id'] = $employee->id;
        $data['manager_id'] = $employee->supervisor_id;
        $data['number_of_days'] = $days;
        $data['status'] = RequestStatus::Pending;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
