<?php

declare(strict_types=1);

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Client;
use Filament\Forms\Components\{RichEditor, Select, TagsInput, TextInput};
use Filament\Schemas\Components\{Grid, Section};
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
                            ->options(Client::query()->orderBy('company_name')->pluck('clients.company_name', 'clients.id'))
                            ->searchable()
                            ->required()->preload(),

                        TextInput::make('project_name')->label('Nome do Projeto')->required(),
                        Select::make('payment_type')
                            ->label('Tipo de Pagamento')
                            ->options([1 => 'Mensal', 2 => 'Sprint', 3 => 'Hora'])
                            ->searchable()
                            ->preload()
                            ->required()
                            ->afterStateHydrated(function (Select $component, $record) {
                                if ($record) {
                                    $paymentType = $record->paymentDays()->first()?->payment_type;
                                    if ($paymentType) {
                                        $component->state($paymentType);
                                    }
                                }
                            }),
                        TagsInput::make('paymentDays')
                            ->label('Dia do Pagamento')
                            ->required()
                            ->placeholder('Informe o dia do Pagamento, e precione TAB')
                            ->suggestions([
                                '10',
                                '15',
                                '20',
                                '25',
                                '30',
                            ])
                            ->separator(',')
                            ->splitKeys(['Tab', ' '])
                            ->afterStateHydrated(function (TagsInput $component, $state, $record) {
                                if ($record) {
                                    $paymentDays = $record->paymentDays()
                                        ->pluck('payment_day')
                                        ->toArray();

                                    $component->state($paymentDays);
                                }
                            }),
                        RichEditor::make('description')
                            ->label('Descrição do Projeto')
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
                    ])->columns(2),
                ])->columnSpanFull(),

            ]);
    }
}
