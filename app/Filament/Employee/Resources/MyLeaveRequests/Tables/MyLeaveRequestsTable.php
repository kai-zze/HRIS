<?php

namespace App\Filament\Employee\Resources\MyLeaveRequests\Tables;

use App\Enums\RequestStatus;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MyLeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('leaveType.name')
                    ->label('Leave Type')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Period')
                    ->formatStateUsing(function ($record) {
                        if ($record->is_half_day) {
                            return sprintf('%s (Half-Day %s)', $record->start_date->format('M d, Y'), strtoupper($record->half_day_type ?? 'AM'));
                        }

                        return $record->start_date->format('M d, Y') . ' — ' . $record->end_date->format('M d, Y');
                    })
                    ->sortable(),
                TextColumn::make('number_of_days')
                    ->label('Days')
                    ->numeric(1)
                    ->alignCenter(),
                TextColumn::make('status')
                    ->label('Status')
                    ->color(fn (RequestStatus $state): string => match ($state) {
                        RequestStatus::Pending => 'warning',
                        RequestStatus::Endorsed => 'info',
                        RequestStatus::Approved => 'success',
                        RequestStatus::Rejected, RequestStatus::Cancelled => 'danger',
                    })
                    ->icon(fn (RequestStatus $state): string => match ($state) {
                        RequestStatus::Pending => 'heroicon-m-clock',
                        RequestStatus::Endorsed => 'heroicon-m-arrow-path',
                        RequestStatus::Approved => 'heroicon-m-check-badge',
                        RequestStatus::Rejected, RequestStatus::Cancelled => 'heroicon-m-x-circle',
                    }),
                TextColumn::make('created_at')
                    ->label('Filed On')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('rejection_reason')
                    ->label('Remarks / Feedback')
                    ->placeholder('None')
                    ->limit(30),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(RequestStatus::class),
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make()
                    ->label('Cancel')
                    ->modalHeading('Cancel Leave Application')
                    ->visible(fn ($record) => $record->status === RequestStatus::Pending),
            ]);
    }
}
