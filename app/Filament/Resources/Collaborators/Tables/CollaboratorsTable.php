<?php

namespace App\Filament\Resources\Collaborators\Tables;

use App\Tables\Columns\DocumentColumn;
use App\Tables\Columns\PhoneColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CollaboratorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                DocumentColumn::make('document')->label('Documento')->searchable(),
                PhoneColumn::make('phone')->label('Telefone')->searchable(),
                TextColumn::make('address')
                    ->label('Endereco')
                    ->searchable(),
                TextColumn::make('payment_method')
                    ->label('Pagamento')
                    ->badge(),
                TextColumn::make('pix_key')
                    ->label('Chave Pix')
                    ->searchable(),
                TextColumn::make('bb_account')
                    ->label('CC')
                    ->searchable(),
                TextColumn::make('bb_agency')
                    ->label('Ag')
                    ->searchable(),
                TextColumn::make('payment_day')
                    ->label('Dia do Pagamento')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
