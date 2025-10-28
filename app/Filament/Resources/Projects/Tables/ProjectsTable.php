<?php

declare(strict_types=1);

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.company_name')
                    ->sortable(),

                TextColumn::make('paymentDays.payment_type')
                    ->label('Tipo de Pagamento')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        1 => 'success',
                        2 => 'info',
                        3 => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn ($state): string => match ($state) {
                        1 => 'heroicon-m-calendar',
                        2 => 'heroicon-m-rocket-launch',
                        3 => 'heroicon-m-clock',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        1 => 'Mensal',
                        2 => 'Sprint',
                        3 => 'Hora',
                        default => $state ?? 'N/A',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('paymentDays.payment_day')
                    ->label('Dia de Pagamento')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'negociation' => 'warning',
                        'pending' => 'gray',
                        'doing' => 'info',
                        'finished' => 'success',
                        'canceled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'negociation' => 'heroicon-m-chat-bubble-left-right',
                        'pending' => 'heroicon-m-clock',
                        'doing' => 'heroicon-m-arrow-path',
                        'finished' => 'heroicon-m-check-circle',
                        'canceled' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'negociation' => 'Negociação',
                        'pending' => 'Pendente',
                        'doing' => 'Em Andamento',
                        'finished' => 'Finalizado',
                        'canceled' => 'Cancelado',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('project_name')
                    ->searchable(),
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
