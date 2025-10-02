<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.project_name')
                    ->label('Projeto')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('sprint.title')
                    ->label('Sprint')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('parent.title')
                    ->label('Task Pai')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('title')
                    ->label('Task')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->description(fn ($record) => $record->description ? \Str::limit(strip_tags($record->description), 80) : null),

                TextColumn::make('type_task')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'epic' => 'purple',
                        'feature' => 'success',
                        'task' => 'info',
                        'bug' => 'danger',
                        'improvement' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'epic' => 'heroicon-m-trophy',
                        'feature' => 'heroicon-m-sparkles',
                        'task' => 'heroicon-m-clipboard-document-list',
                        'bug' => 'heroicon-m-bug-ant',
                        'improvement' => 'heroicon-m-arrow-trending-up',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'epic' => 'Epic',
                        'feature' => 'Feature',
                        'task' => 'Task',
                        'bug' => 'Bug',
                        'improvement' => 'Melhoria',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'backlog' => 'gray',
                        'refinement' => 'info',
                        'todo' => 'warning',
                        'doing' => 'primary',
                        'validation' => 'purple',
                        'ready_to_deploy' => 'success',
                        'done' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'backlog' => 'Backlog',
                        'refinement' => 'Refinamento',
                        'todo' => 'To Do',
                        'doing' => 'Doing',
                        'validation' => 'Validação',
                        'ready_to_deploy' => 'Pronto',
                        'done' => 'Concluído',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('priority')
                    ->label('Prioridade')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'low' => 'heroicon-m-arrow-down',
                        'medium' => 'heroicon-m-minus',
                        'high' => 'heroicon-m-arrow-up',
                        'urgent' => 'heroicon-m-fire',
                        default => 'heroicon-m-flag',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Baixa',
                        'medium' => 'Média',
                        'high' => 'Alta',
                        'urgent' => 'Urgente',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('collaborator.name')
                    ->label('Responsável')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->toggleable(),

                TextColumn::make('applicant.name')
                    ->label('Solicitante')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_time_worked')
                    ->label('Tempo Gasto')
                    ->suffix('h')
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Criada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Group::make('sprint.title')
                    ->label('Sprint')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),

                Group::make('project.project_name')
                    ->label('Projeto')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),

                Group::make('type_task')
                    ->label('Tipo de Task')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn ($record) => match($record->type_task) {
                        'epic' => '🎯 Epic',
                        'feature' => '⭐ Feature',
                        'task' => '📋 Task',
                        'bug' => '🐛 Bug',
                        'improvement' => '✨ Melhoria',
                        default => $record->type_task,
                    }),

                Group::make('status')
                    ->label('Status')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn ($record) => match($record->status) {
                        'backlog' => '📥 Backlog',
                        'refinement' => '🔍 Refinamento',
                        'todo' => '📝 To Do',
                        'doing' => '⚡ Doing',
                        'validation' => '🔬 Validação',
                        'ready_to_deploy' => '🚀 Pronto',
                        'done' => '✅ Concluído',
                        default => $record->status,
                    }),
            ])
            ->defaultGroup('sprint.title') // ← AGRUPA POR SPRINT POR PADRÃO
            ->filters([
                SelectFilter::make('project_id')
                    ->label('Projeto')
                    ->relationship('project', 'project_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('sprint_id')
                    ->label('Sprint')
                    ->relationship('sprint', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type_task')
                    ->label('Tipo')
                    ->options([
                        'epic' => 'Epic',
                        'feature' => 'Feature',
                        'task' => 'Task',
                        'bug' => 'Bug',
                        'improvement' => 'Melhoria',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'backlog' => 'Backlog',
                        'refinement' => 'Refinamento',
                        'todo' => 'To Do',
                        'doing' => 'Doing',
                        'validation' => 'Validação',
                        'ready_to_deploy' => 'Pronto',
                        'done' => 'Concluído',
                    ]),

                SelectFilter::make('priority')
                    ->label('Prioridade')
                    ->options([
                        'low' => 'Baixa',
                        'medium' => 'Média',
                        'high' => 'Alta',
                        'urgent' => 'Urgente',
                    ]),

                SelectFilter::make('collaborator_id')
                    ->label('Responsável')
                    ->relationship('collaborator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->emptyStateHeading('Nenhuma task encontrada')
            ->emptyStateDescription('Crie sua primeira task clicando no botão acima.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }
}
