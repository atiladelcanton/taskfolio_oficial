<?php

declare(strict_types=1);

namespace App\Filament\Resources\Sprints\Tables;

use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

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
            ->defaultGroup('project.project_name')
            ->columns([

                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('start_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('end_at')
                    ->dateTime()
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
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
