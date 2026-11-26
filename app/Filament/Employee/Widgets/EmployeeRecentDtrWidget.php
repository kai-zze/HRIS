<?php

namespace App\Filament\Employee\Widgets;

use App\Enums\AttendanceStatus;
use App\Models\DailyTimeRecord;
use App\Models\Employee;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class EmployeeRecentDtrWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $employee = $user?->employee ?? Employee::withoutGlobalScopes()->where('email', $user?->email)->first();

        return $table
            ->heading('Recent Time & Attendance Logs')
            ->description('Your last 5 recorded punches and daily attendance computations.')
            ->query(
                DailyTimeRecord::query()
                    ->withoutGlobalScopes()
                    ->where('employee_id', $employee?->id ?? 0)
                    ->orderByDesc('record_date')
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('record_date')
                    ->label('Date')
                    ->date('M d, Y (D)')
                    ->weight('bold'),
                TextColumn::make('time_in')
                    ->label('In')
                    ->time('h:i A')
                    ->placeholder('—'),
                TextColumn::make('time_out')
                    ->label('Out')
                    ->time('h:i A')
                    ->placeholder('—'),
                TextColumn::make('late_minutes')
                    ->label('Late')
                    ->formatStateUsing(fn ($state) => $state > 0 ? sprintf('%d m', $state) : '—')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),
                TextColumn::make('regular_hours')
                    ->label('Hours')
                    ->formatStateUsing(fn ($state) => sprintf('%.1f h', $state)),
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
            ]);
    }
}
