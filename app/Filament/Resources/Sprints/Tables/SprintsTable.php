<?php

declare(strict_types=1);

namespace App\Filament\Resources\Sprints\Tables;

use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction};
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\{Filter, SelectFilter};
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SprintsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('project.project_name')
                    ->label('Projeto')
                    ->collapsible(),
                Group::make('status')
                    ->label('Status')
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'BACKLOG' => 'Aguardando',
                        'PLANNING' => 'Planejamento',
                        'ACTIVE' => 'Andamento',
                        'PAUSED' => 'Pausada',
                        'COMPLETED' => 'Concluída',
                        'CANCELLED' => 'Cancelada',
                    ]),
                Filter::make('start_at')
                    ->schema([
                        DatePicker::make('start_at')->label('Inicio da Sprint'),
                        DatePicker::make('end_at')->label('Fim da Sprint'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_at'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_at', '>=', $date),
                            )
                            ->when(
                                $data['end_at'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_at', '<=', $date),
                            );
                    }),
            ])
            ->defaultGroup('project.project_name')
            ->columns([

                TextColumn::make('title')
                    ->label('Sprint')
                    ->searchable(),
                TextColumn::make('start_at')
                    ->label('Inicio da Sprint')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('end_at')
                    ->label('Final da Sprint')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'BACKLOG' => 'gray',
                        'PLANNING' => 'info',
                        'ACTIVE' => 'success',
                        'PAUSED' => 'warning',
                        'COMPLETED' => 'primary',
                        'CANCELLED' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'BACKLOG' => 'Aguardando',
                        'PLANNING' => 'Planejamento',
                        'ACTIVE' => 'Andamento',
                        'PAUSED' => 'Pausada',
                        'COMPLETED' => 'Concluída',
                        'CANCELLED' => 'Cancelada',
                        default => $state,
                    }),

            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
