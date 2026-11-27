<?php

namespace App\Filament\Resources\LeaveRequests\Tables;

use App\Enums\RequestStatus;
use App\Models\LeaveRequest;
use App\Services\LeaveBalanceService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')->label('Employee')->searchable()->sortable()->weight('bold'),
                TextColumn::make('employee.entity.short_name')->label('Entity')->sortable(),
                TextColumn::make('leaveType.name')->label('Leave Type'),
                TextColumn::make('start_date')->label('From')->date('M d, Y')->sortable(),
                TextColumn::make('end_date')->label('To')->date('M d, Y')->sortable(),
                TextColumn::make('number_of_days')->label('Days')->formatStateUsing(fn ($state) => "{$state}d")->weight('bold'),
                TextColumn::make('status')->label('Status')->color(fn ($state) => match ($state?->value ?? $state) {
                    'Approved' => 'success',
                    'Pending' => 'warning',
                    'Rejected', 'Cancelled' => 'danger',
                    default => 'info',
                }),
                TextColumn::make('reason')->label('Reason')->limit(30),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                SelectFilter::make('status')->options(RequestStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('managerEndorse')
                    ->label('Supervisor Endorse')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(fn (LeaveRequest $record) => $record->status === RequestStatus::Pending)
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => RequestStatus::ApprovedByManager,
                            'manager_id' => Auth::id(),
                            'manager_action_at' => now(),
                        ]);
                        Notification::make()->title('Leave Request Endorsed to HR')->success()->send();
                    }),
                Action::make('hrApprove')
                    ->label('HR Final Approval')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (LeaveRequest $record) => in_array($record->status, [RequestStatus::Pending, RequestStatus::ApprovedByManager]))
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => RequestStatus::Approved,
                            'hr_id' => Auth::id(),
                            'hr_action_at' => now(),
                        ]);
                        app(LeaveBalanceService::class)->deductBalance($record);
                        Notification::make()->title('Leave Approved & Deducted from Credits')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (LeaveRequest $record) => in_array($record->status, [RequestStatus::Pending, RequestStatus::ApprovedByManager]))
                    ->action(function (LeaveRequest $record) {
                        $record->update(['status' => RequestStatus::Rejected]);
                        Notification::make()->title('Leave Request Rejected')->danger()->send();
                    }),
            ]);
    }
}
