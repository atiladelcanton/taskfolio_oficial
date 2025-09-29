<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Tables\Columns\DocumentColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                TextColumn::make('phone')
                    ->label('Telefone do Cliente')
                    ->searchable(),
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
