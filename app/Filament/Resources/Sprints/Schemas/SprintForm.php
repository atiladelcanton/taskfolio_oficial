<?php

declare(strict_types=1);

namespace App\Filament\Resources\Sprints\Schemas;

use App\Models\Project;
use Carbon\Carbon;
use Filament\Forms\Components\{DatePicker, RichEditor, Select, TextInput, ToggleButtons};
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\{Get, Set};
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SprintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    ToggleButtons::make('status')
                        ->label('Status da Sprint')
                        ->options([
                            'BACKLOG' => 'Aguardando',
                            'PLANNING' => 'Planejamento',
                            'ACTIVE' => 'Andamento',
                            'PAUSED' => 'Pausada',
                            'COMPLETED' => 'Concluída',
                            'CANCELLED' => 'Cancelada',
                        ])
                        ->icons([
                            'BACKLOG' => 'heroicon-m-inbox',
                            'PLANNING' => 'heroicon-m-clipboard-document-list',
                            'ACTIVE' => 'heroicon-m-play-circle',
                            'PAUSED' => 'heroicon-m-pause-circle',
                            'COMPLETED' => 'heroicon-m-check-circle',
                            'CANCELLED' => 'heroicon-m-x-circle',
                        ])
                        ->colors([
                            'BACKLOG' => 'gray',
                            'PLANNING' => 'info',
                            'ACTIVE' => 'success',
                            'PAUSED' => 'warning',
                            'COMPLETED' => 'primary',
                            'CANCELLED' => 'danger',
                        ])
                        ->inline()
                        ->default('BACKLOG')
                        ->required()
                        ->columnSpanFull(),
                    Select::make('project_id')
                        ->label('Projeto')
                        ->prefixIcon(Heroicon::Briefcase)
                        ->options(
                            Project::query()
                                ->with(['client'])
                                ->get()
                                ->groupBy('client.company_name')
                                ->map(
                                    fn ($projects) => $projects->mapWithKeys(fn ($project) => [
                                        $project->id => $project->project_name,
                                    ])
                                )
                        )->afterStateUpdated(function (Set $set) {
                            $set('start_at', null);
                            $set('end_at', null);
                        })
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('title')
                        ->label('Sprint')
                        ->required(),
                    DatePicker::make('start_at')
                        ->label('Data de Início')
                        ->displayFormat('d/m/Y')
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if ($state) {
                                $endDate = Carbon::parse($state)->addDays(14);

                                if ($endDate->isWeekend()) {
                                    $endDate = $endDate->previousWeekday();
                                }

                                $set('end_at', $endDate->format('Y-m-d'));
                            }
                        })
                        ->required(),
                    DatePicker::make('end_at')
                        ->label('Data de Término')
                        ->displayFormat('d/m/Y')
                        ->native(false)
                        ->required()
                        ->minDate(fn (Get $get) => $get('start_at'))
                        ->disabled(fn (Get $get) => ! $get('start_at'))
                        ->helperText('Calculado automaticamente a partir da data de início'),
                    RichEditor::make('description')
                        ->label('Descricao')
                        ->helperText('Descreva qual o objetivo da Sprint')

                        ->columnSpanFull(),
                ])->columns(2)->columnSpanFull(),

            ]);
    }
}
