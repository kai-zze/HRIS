<?php

namespace App\Filament\Employee\Resources\MyDailyTimeRecords\Tables;

use App\Enums\AttendanceStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyDailyTimeRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('record_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->description(fn ($record) => $record->record_date?->format('l'))
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('time_in')
                    ->label('Time In')
                    ->time('h:i A')
                    ->placeholder('—'),
                TextColumn::make('time_out')
                    ->label('Time Out')
                    ->time('h:i A')
                    ->placeholder('—'),
                TextColumn::make('late_minutes')
                    ->label('Late (mins)')
                    ->numeric()
                    ->alignCenter()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),
                TextColumn::make('undertime_minutes')
                    ->label('Undertime')
                    ->numeric()
                    ->alignCenter()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),
                TextColumn::make('overtime_hours')
                    ->label('OT (hrs)')
                    ->numeric(1)
                    ->alignCenter()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                TextColumn::make('regular_hours')
                    ->label('Rendered')
                    ->formatStateUsing(fn ($state) => sprintf('%.1f hrs', $state))
                    ->alignCenter()
                    ->weight('medium'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (AttendanceStatus $state): string => match ($state) {
                        AttendanceStatus::Present => 'success',
                        AttendanceStatus::Late => 'danger',
                        AttendanceStatus::Undertime => 'warning',
                        AttendanceStatus::Overtime => 'info',
                        AttendanceStatus::Absent => 'danger',
                        AttendanceStatus::OnLeave => 'primary',
                        AttendanceStatus::Holiday => 'gray',
                        AttendanceStatus::RestDay => 'gray',
                    }),
            ])
            ->defaultSort('record_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(AttendanceStatus::class),
                Filter::make('current_month')
                    ->label('Current Month')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('record_date', now()->month)->whereYear('record_date', now()->year))
                    ->default(),
                Filter::make('last_month')
                    ->label('Previous Month')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('record_date', now()->subMonth()->month)->whereYear('record_date', now()->subMonth()->year)),
            ]);
    }
}
