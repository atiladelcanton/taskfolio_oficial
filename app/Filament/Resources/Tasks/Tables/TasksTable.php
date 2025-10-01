<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('sprint.title')
                    ->label('Sprint')
                    ->collapsible(),
                Group::make('type_task')
                    ->label('Tipo de Task')
                    ->collapsible(),
                Group::make('parent.title')
                    ->label('SubTask')
                    ->collapsible(),
                Group::make('project.project_name')
                    ->label('Projeto')
                    ->collapsible(),
            ])
            ->columns([
                TextColumn::make('project.project_name')
                    ->label('Projeto')
                    ->sortable(),
                TextColumn::make('sprint.title')
                    ->label('Sprint')
                    ->badge()
                    ->sortable(),
                TextColumn::make('parent.title')
                    ->label('SubTask de')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Task')
                    ->searchable(),
                TextColumn::make('collaborator.name')
                    ->label('Responsavel')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type_task')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'epic'        => 'info',
                        'task'        => 'primary',
                        'bug'         => 'danger',
                        'feature'     => 'success',
                        'improvement' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn ($state): string => match ($state) {
                        'epic'        => 'heroicon-m-sparkles',
                        'task'        => 'heroicon-m-clipboard-document-check',
                        'bug'         => 'heroicon-m-bug-ant',
                        'feature'     => 'heroicon-m-sparkles',
                        'improvement' => 'heroicon-m-wrench-screwdriver',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        'epic'       => 'Epico',
                        'task'        => 'Task',
                        'bug'         => 'Bug',
                        'feature'     => 'Feature',
                        'improvement' => 'Melhoria',
                        default => $state ?? 'N/A',
                    }),

                TextColumn::make('applicant.name')
                    ->label('Solicitante')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_time_worked')
                    ->label('Tempo de trabalho')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Data de Cadastro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Data de Atualizacao')
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
