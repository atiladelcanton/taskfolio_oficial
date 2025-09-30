<?php

declare(strict_types=1);

namespace App\Filament\Resources\Collaborators\Schemas;

use App\Forms\Components\{DocumentInput, PhoneInput};
use Filament\Forms\Components\{Hidden, Select, TextInput};
use Filament\Schemas\Components\{Grid, Section};
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
                        Select::make('payment_type')
                            ->label('Tipo de Pagamento')
                            ->placeholder('Selecione o tipo de pagamento')
                            ->options([1 => 'Quinzenalmente', 2 => 'Mensalmente'])
                            ->required(),
                        TextInput::make('pix_key')
                            ->label('Chave Pix')
                            ->visible(fn (Get $get) => $get('payment_method') == 'pix'),
                        TextInput::make('bb_agency')->label('Agencia')
                            ->visible(fn (Get $get) => $get('payment_method') == 'transf'),
                        TextInput::make('bb_account')
                            ->label('Conta')->visible(fn (Get $get) => $get('payment_method') == 'transf'),
                        Select::make('user_type')
                            ->label('Perfil')
                            ->options([
                                1 => 'Administrador',
                                3 => 'Colaborador',
                            ])
                            ->default(3)
                            ->required()
                            ->helperText(
                                fn ($context) => $context === 'create'
                                ? 'Um usuário será criado automaticamente com este perfil'
                                : null
                            ),
                    ])->columns(3),
                ])->columnSpanFull(),
            ]);
    }
}
