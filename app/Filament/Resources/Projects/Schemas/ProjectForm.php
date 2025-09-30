<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Client;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    Grid::make()->schema([
                        Select::make('client_id')
                            ->label('Cliente')
                            ->options(Client::query()->orderBy("company_name")->pluck('clients.company_name', 'clients.id'))
                            ->searchable()
                            ->required()->preload(),


                        TextInput::make('project_name')->label('Nome do Projeto')->required(),
                        Select::make('payment_type')
                            ->label('Tipo de Pagamento')
                            ->options(['monthly' => 'Mensal', 'sprint' => 'Sprint', 'hours' => 'Hora'])
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('payment_method')
                            ->label('Método de Pagamento')
                            ->options(['monthly' => 'Mensal', 'sprint' => 'Sprint', 'hours' => 'Hora'])
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('payment_day')->label('Dia do Pagamento')->required(),
                        RichEditor::make('description')
                            ->label("Descrição do Projeto")
                            ->columnSpanFull(),
                        Select::make('status')
                            ->options([
                                'negociation' => 'Em Negocição',
                                'pending' => 'Pendente',
                                'doing' => 'Andamento',
                                'canceled' => 'Cancelado',
                                'finished' => 'Finalizado',
                            ])
                            ->default('negociation')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2)
                ])->columnSpanFull()

            ]);
    }
}
