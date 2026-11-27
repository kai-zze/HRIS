<?php

namespace App\Filament\Employee\Resources\MyOvertimeRequests\Tables;

use App\Enums\RequestStatus;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MyOvertimeRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('overtime_date')
                    ->label('OT Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('start_time')
                    ->label('Period')
                    ->formatStateUsing(fn ($record) => sprintf('%s — %s', substr($record->start_time, 0, 5), substr($record->end_time, 0, 5))),
                TextColumn::make('hours_requested')
                    ->label('Hours')
                    ->numeric(2)
                    ->alignCenter(),
                TextColumn::make('purpose')
                    ->label('Reason / Purpose')
                    ->limit(40),
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
                TextColumn::make('approved_at')
                    ->label('Actioned Date')
                    ->dateTime('M d, Y')
                    ->placeholder('Pending Action'),
            ])
            ->defaultSort('overtime_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(RequestStatus::class),
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make()
                    ->label('Cancel')
                    ->modalHeading('Cancel Overtime Request')
                    ->visible(fn ($record) => $record->status === RequestStatus::Pending),
            ]);
    }
}
