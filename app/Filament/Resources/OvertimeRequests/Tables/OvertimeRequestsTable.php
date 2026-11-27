<?php

namespace App\Filament\Resources\OvertimeRequests\Tables;

use App\Enums\RequestStatus;
use App\Models\OvertimeRequest;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class OvertimeRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('overtime_date')->label('Date')->date('M d, Y (D)')->sortable()->weight('bold'),
                TextColumn::make('employee.full_name')->label('Employee')->searchable()->sortable(),
                TextColumn::make('employee.entity.short_name')->label('Entity')->sortable(),
                TextColumn::make('hours_requested')->label('Requested Hrs')->formatStateUsing(fn ($state) => "{$state} hrs")->weight('bold'),
                TextColumn::make('start_time')->time('g:i A'),
                TextColumn::make('end_time')->time('g:i A'),
                TextColumn::make('status')->label('Status')->color(fn ($state) => match ($state?->value ?? $state) {
                    'Approved' => 'success',
                    'Pending' => 'warning',
                    'Rejected', 'Cancelled' => 'danger',
                    default => 'info',
                }),
                TextColumn::make('purpose')->label('Purpose')->limit(30),
            ])
            ->defaultSort('overtime_date', 'desc')
            ->filters([
                SelectFilter::make('status')->options(RequestStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('managerApprove')
                    ->label('Supervisor Endorse')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(fn (OvertimeRequest $record) => $record->status === RequestStatus::Pending)
                    ->action(function (OvertimeRequest $record) {
                        $record->update([
                            'status' => RequestStatus::ApprovedByManager,
                            'manager_id' => Auth::id(),
                            'manager_action_at' => now(),
                        ]);
                        Notification::make()->title('Overtime Endorsed to HR')->success()->send();
                    }),
                Action::make('hrApprove')
                    ->label('HR Final Approval')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (OvertimeRequest $record) => in_array($record->status, [RequestStatus::Pending, RequestStatus::ApprovedByManager]))
                    ->action(function (OvertimeRequest $record) {
                        $record->update([
                            'status' => RequestStatus::Approved,
                            'hr_id' => Auth::id(),
                            'hr_action_at' => now(),
                        ]);
                        Notification::make()->title('Overtime Approved')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (OvertimeRequest $record) => in_array($record->status, [RequestStatus::Pending, RequestStatus::ApprovedByManager]))
                    ->action(function (OvertimeRequest $record) {
                        $record->update(['status' => RequestStatus::Rejected]);
                        Notification::make()->title('Overtime Rejected')->danger()->send();
                    }),
            ]);
    }
}
