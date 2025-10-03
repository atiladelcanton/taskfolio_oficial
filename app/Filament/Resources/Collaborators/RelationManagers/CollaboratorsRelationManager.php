<?php

declare(strict_types=1);

namespace App\Filament\Resources\Collaborators\RelationManagers;

use App\Models\Project;
use Filament\Actions\{AttachAction, DetachAction, EditAction};
use Filament\Forms\Components\{Select, TextInput};
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CollaboratorsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Projetos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('project_name')
            ->columns([
                TextColumn::make('project_name')
                    ->label('Nome do Projeto')
                    ->searchable(),

                TextColumn::make('pivot.payment_type')
                    ->label('Tipo de Pagamento')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'monthly' => 'success',
                        'sprint' => 'info',
                        'hours' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn ($state): string => match ($state) {
                        'monthly' => 'heroicon-m-calendar',
                        'sprint' => 'heroicon-m-rocket-launch',
                        'hours' => 'heroicon-m-clock',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        'monthly' => 'Mensal',
                        'sprint' => 'Sprint',
                        'hours' => 'Hora',
                        default => $state ?? 'N/A',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pivot.collaborator_value')
                    ->label('Valor')
                    ->formatStateUsing(fn ($state) => 'R$ '.number_format($state, 2, ',', '.'))
                    ->icon('heroicon-m-banknotes')
                    ->iconColor('success')
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Vincular Projeto')
                    ->modalHeading('Vincular ao Projeto')
                    ->modalDescription('Vincule o colaborador a um projeto e informe quanto ele vai receber nesse projeto')
                    ->modalSubmitActionLabel('Vincular')
                    ->modalIcon('heroicon-o-link')
                    ->preloadRecordSelect()
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Projeto')
                            ->prefixIcon(Heroicon::Briefcase)
                            ->options(
                                Project::query()
                                    ->with(['client'])
                                    ->get()
                                    ->mapWithKeys(fn ($project) => [
                                        $project->id => '<span class="text-blue-600 dark:text-blue-400 font-bold">('
                                            .e($project->client->company_name)
                                            .')</span> <strong>'
                                            .e($project->project_name)
                                            .'</strong>',
                                    ])
                            )
                            ->allowHtml()
                            ->searchable()
                            ->preload(),

                        TextInput::make('collaborator_value')
                            ->label('Valor')
                            ->helperText('Exemplo: 15,50 ou 1.234,56')
                            ->prefix('R$')
                            ->mask(RawJs::make('$money($input, \',\', \'.\')'))
                            ->stripCharacters(['.', ',']) // Remove pontos e vírgulas
                            ->numeric()
                            ->dehydrateStateUsing(function ($state) {
                                if ($state === null || $state === '') {
                                    return null;
                                }

                                $state = (string) $state;

                                return (float) str_replace(['.', ','], ['', '.'], $state);
                            })
                            ->required(),
                        Select::make('payment_type')
                            ->label('Tipo de Pagamento')
                            ->options(['monthly' => 'Mensal', 'sprint' => 'Sprint', 'hours' => 'Hora'])
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->using(function (RelationManager $livewire, array $data, $record): void {
                        $collaborator = $livewire->getOwnerRecord();

                        foreach ((array) $data['recordId'] as $projectId) {
                            $collaborator->projects()->attach($projectId, [
                                'collaborator_value' => $data['collaborator_value'],
                                'payment_type' => $data['payment_type'],
                            ]);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Editar')
                    ->modalHeading('Editar Vínculo do Projeto')
                    ->modalDescription('Atualize o valor e tipo de pagamento do colaborador neste projeto')
                    ->modalSubmitActionLabel('Salvar')
                    ->modalIcon('heroicon-o-pencil-square')
                    ->fillForm(function ($record): array {
                        // Carrega os dados do pivot manualmente
                        return [
                            'collaborator_value' => number_format($record->pivot->collaborator_value, 2, ',', '.'),
                            'payment_type' => $record->pivot->payment_type,
                        ];
                    })
                    ->schema([
                        TextInput::make('collaborator_value')
                            ->label('Valor')
                            ->helperText('Exemplo: 15,50 ou 1.234,56')
                            ->prefix('R$')
                            ->prefixIcon(Heroicon::Calculator)
                            ->mask(RawJs::make('$money($input, \',\', \'.\')'))
                            ->dehydrateStateUsing(function ($state) {
                                return $state ? (float) str_replace(['.', ','], ['', '.'], $state) : null;
                            })
                            ->required(),

                        Select::make('payment_type')
                            ->label('Tipo de Pagamento')
                            ->prefixIcon(Heroicon::CreditCard)
                            ->options(['monthly' => 'Mensal', 'sprint' => 'Sprint', 'hours' => 'Hora'])
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->using(function (RelationManager $livewire, $record, array $data): void {
                        $collaborator = $livewire->getOwnerRecord();

                        $collaborator->projects()->updateExistingPivot($record->id, [
                            'collaborator_value' => $data['collaborator_value'],
                            'payment_type' => $data['payment_type'],
                        ]);
                    }),

                DetachAction::make()
                    ->label('Remover do Projeto')
                    ->modalHeading('Remover colaborador do projeto')
                    ->modalDescription(fn ($record) => new \Illuminate\Support\HtmlString(
                        'Você está prestes a remover o colaborador do projeto:<br><br>'
                        .'<span class="inline-flex items-center rounded-md bg-red-50 px-3 py-1.5 text-sm font-semibold text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20">'
                        .e($record->project_name)
                        .'</span><br><br>'
                        .'Deseja continuar?'
                    ))
                    ->modalSubmitActionLabel('Sim, remover')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->color('danger'),

            ]);
    }
}
