<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('M d, Y H:i:s')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('causer.name')
                    ->label('Operator')
                    ->default('System / Automated')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Event')
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'restored' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('subject_type')
                    ->label('Target Module')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->sortable(),
                TextColumn::make('subject_id')
                    ->label('Record ID')
                    ->alignCenter(),
                TextColumn::make('log_name')
                    ->label('Log Scope')
                    ->default('default'),
            ])
            ->filters([
                SelectFilter::make('description')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
            ])
            ->actions([
                Action::make('view_diff')
                    ->label('Inspect Changes')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading(fn (Activity $record) => "Audit Details #{$record->id} — " . class_basename($record->subject_type))
                    ->modalDescription(fn (Activity $record) => "Action: " . strtoupper($record->description) . " by " . ($record->causer?->name ?? 'System'))
                    ->form([
                        Textarea::make('properties_json')
                            ->label('Audit Attributes Diff (Old vs New)')
                            ->default(fn (Activity $record) => json_encode($record->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
                            ->rows(12)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
