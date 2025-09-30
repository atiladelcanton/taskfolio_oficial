<?php

declare(strict_types=1);

namespace App\Filament\Resources\Collaborators\Tables;

use App\Tables\Columns\{DocumentColumn, PhoneColumn};
use Filament\Actions\{ActionGroup, DeleteAction, EditAction};
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
                PhoneColumn::make('cellphone')->label('Telefone')->searchable()->withWhatsApp()->withBadge(),
                TextColumn::make('address')
                    ->label('Endereco')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
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
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        1 => 'success',
                        0 => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        1 => 'Ativo',
                        0 => 'Inativo',
                        default => $state ?? 'N/A',
                    }),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),

                    DeleteAction::make(),
                ])->label('Acoes'),

            ]);
    }
}
