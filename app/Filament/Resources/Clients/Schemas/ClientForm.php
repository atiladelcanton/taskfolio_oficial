<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clients\Schemas;

use App\Forms\Components\{DocumentInput, PhoneInput};
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\{Grid, Section};
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    Grid::make()->schema([
                        TextInput::make('company_name')
                            ->label('Nome da Empresa')
                            ->required(),
                        TextInput::make('personal_name')
                            ->label('Nome do Cliente')
                            ->required(),
                        DocumentInput::make('document')
                            ->uniqueInTable('clients'),
                        TextInput::make('email')
                            ->label('E-mail do Cliente')
                            ->email()
                            ->required(),
                        PhoneInput::make('phone')
                            ->label('Telefone do Cliente')
                            ->tel()
                            ->required(),
                    ])->columns(2),
                ])->columnSpanFull(),
            ]);
    }
}
