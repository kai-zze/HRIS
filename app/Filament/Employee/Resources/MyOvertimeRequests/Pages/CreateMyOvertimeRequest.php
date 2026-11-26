<?php

namespace App\Filament\Employee\Resources\MyOvertimeRequests\Pages;

use App\Enums\RequestStatus;
use App\Filament\Employee\Resources\MyOvertimeRequests\MyOvertimeRequestResource;
use App\Models\Employee;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateMyOvertimeRequest extends CreateRecord
{
    protected static string $resource = MyOvertimeRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $employee = $user?->employee ?? Employee::withoutGlobalScopes()->where('email', $user?->email)->first();

        if (! $employee) {
            Notification::make()
                ->title('No Employee Profile Linked')
                ->body('Unable to file overtime: no active employee profile linked.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'overtime_date' => 'No employee profile linked to user.',
            ]);
        }

        $start = Carbon::parse($data['start_time']);
        $end = Carbon::parse($data['end_time']);
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }

        $hours = round($start->diffInMinutes($end) / 60, 2);

        $data['employee_id'] = $employee->id;
        $data['manager_id'] = $employee->supervisor_id;
        $data['hours_requested'] = $hours;
        $data['status'] = RequestStatus::Pending;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
