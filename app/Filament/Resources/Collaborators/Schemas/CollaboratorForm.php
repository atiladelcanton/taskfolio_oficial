<?php

namespace App\Filament\Resources\Collaborators\Schemas;

use App\Forms\Components\DocumentInput;
use App\Forms\Components\PhoneInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CollaboratorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    Grid::make()->schema([
                        Hidden::make('id'),
                        TextInput::make('name')
                            ->label('Nome')
                            ->required(),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required(),
                        DocumentInput::make('document')->label('CPF/CNPJ'),
                        PhoneInput::make('cellphone')->label('Telefone'),
                        TextInput::make('address')->label('Endereço'),
                        Select::make('payment_method')
                            ->label('Forma de Pagamento')
                            ->placeholder('Selecione a forma de pagamento')
                            ->options(['pix' => 'Pix', 'transf' => 'Transferencia'])
                            ->reactive()
                            ->required(),
                        TextInput::make('pix_key')
                            ->label('Chave Pix')
                            ->visible(fn(Get $get) => $get('payment_method') == 'pix'),
                        TextInput::make('bb_agency')->label('Agencia')
                            ->visible(fn(Get $get) => $get('payment_method') == 'transf'),
                        TextInput::make('bb_account')
                            ->label('Conta')->visible(fn(Get $get) => $get('payment_method') == 'transf'),

                        Select::make('payment_day')
                            ->label('Dia de Pagamento')
                            ->placeholder('Selecione o Dia de Pagamento')
                            ->options([
                                15 => '15',
                                25 => '25',
                                30 => '30',
                            ]),
                        Select::make('user.type')
                            ->label('Perfil')
                            ->options([
                                1 => 'Administrador',
                                3 => 'Colaborador'
                            ]) ->formatStateUsing(fn ($record) => $record?->user?->type)
                            ->saveRelationshipsUsing(function ($component, $state, $record) {
                                if ($record && $record->user) {
                                    $record->user->update(['type' => $state]);
                                }
                            })
                            ->dehydrated(false)
                    ])->columns(3)
                ])->columnSpanFull()
            ]);
    }
}
