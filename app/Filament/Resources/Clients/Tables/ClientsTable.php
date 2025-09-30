<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clients\Tables;

use App\Tables\Columns\{DocumentColumn, PhoneColumn};
use Filament\Actions\{DeleteAction, EditAction, ViewAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('company_name')
                    ->label('Nome da Empresa')
                    ->searchable(),
                TextColumn::make('personal_name')
                    ->label('Nome do Cliente')
                    ->searchable(),
                DocumentColumn::make('document')->withColor()->withBadge()->withIcon()->masked(),
                TextColumn::make('email')
                    ->label('E-mail do Cliente')
                    ->searchable(),
                PhoneColumn::make('phone')
                    ->label('Telefone do Cliente')
                    ->withColor()->withBadge()->withIcon()
                    ->searchable(),
                TextColumn::make('projects_count')
                    ->label('Projetos')
                    ->sortable()
                    ->badge()
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Data do Cadastro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label('Visualizar')->icon('heroicon-s-eye'),
                EditAction::make()->label('Editar')->icon('heroicon-o-pencil'),
                DeleteAction::make()->label('Excluir')->icon('heroicon-s-trash'),
            ]);
    }
}
