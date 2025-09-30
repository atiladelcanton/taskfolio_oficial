<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sprint_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('parent_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('applicant_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('collaborator_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('type_task')
                    ->badge(),
                TextColumn::make('total_time_worked')
                    ->searchable(),
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
