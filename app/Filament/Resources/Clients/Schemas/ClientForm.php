<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Forms\Components\DocumentInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

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
                      ->uniqueInTable(),
                   TextInput::make('email')
                       ->label('E-mail do Cliente')
                       ->email()
                       ->required(),
                   TextInput::make('phone')
                       ->label('Telefone do Cliente')
                       ->tel()
                       ->required(),
               ])->columns(2)
                ])->columnSpanFull()
            ]);
    }

}
