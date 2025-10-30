<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Actions\Tasks\{ChangeStatusTaskAction, SyncTaskEvidencesAction};
use App\Enums\TypeTaskEnum;
use App\Models\Task;
use App\Support\Tasks\ComponentsHelper;
use BackedEnum;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\{Grid, Section};
use Filament\Schemas\Schema;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Relaticle\Flowforge\{Board, BoardPage, Column};

class TaskBoard extends BoardPage implements HasActions
{
    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-view-columns';

    protected static ?string $navigationLabel = 'Tasks Board';

    protected static ?string $title = 'Tasks Board';

    public function moveCard(string $cardId, string $targetColumnId, ?string $afterCardId = null, ?string $beforeCardId = null): void
    {
        $task = Task::find($cardId);
        ChangeStatusTaskAction::handle($task, ['status' => $targetColumnId]);
    }

    /**
     * @throws Exception
     */
    public function board(Board $board): Board
    {
        return $board
            ->query($this->getEloquentQuery())
            ->recordTitleAttribute('title')
            ->columnIdentifier('status')

            ->positionIdentifier('position')
            ->searchable(['title', 'description', 'collaborator.name'])
            ->filters([
                SelectFilter::make('type_task')
                    ->options([
                        'epic' => 'Epico',
                        'task' => 'Task',
                        'bug' => 'Bug',
                        'feature' => 'Feature',
                        'improvement' => 'Melhoria',
                    ]),
            ])
            ->cardActions(
                ComponentsHelper::getActionsTaskBoard()
            )
            ->cardSchema(fn (Schema $schema) => $schema->components([
                Grid::make()
                    ->extraAttributes(fn ($record) => [
                        'class' => ($record->type_task === 'bug' && in_array($record->priority, ['high', 'urgent']))
                            ? 'bg-red-50 ring-1 ring-red-200 rounded-xl p-2 dark:bg-red-900/20 dark:ring-red-800'
                            : '', ])
                    ->schema([
                        ViewEntry::make('meta_row')
                            ->view('filament/tasks/cards/meta-row') // Blade logo abaixo
                            ->columnSpanFull(),
                        ViewEntry::make('collab_time_row')
                            ->view('filament/tasks/cards/collab-time-row') // blade abaixo
                            ->columnSpanFull(),
                    ]),
            ]))
            ->columns([
                Column::make('backlog')->label('Planejado')->color('gray'),
                Column::make('refinement')->label('Refinamento')->color('primary'),
                Column::make('todo')->label('A Fazer')->color('secondary'),
                Column::make('doing')->label('Executando')->color('info'),
                Column::make('validation')->label('Validação')->color('warning'),
                Column::make('ready_to_deploy')->label('Aguardando Deploy')->color('danger'),
                Column::make('done')->label('Concluído')->color('success'),
                Column::make('cancelled')->label('Cancelada')->color('danger'),
            ]);
    }

    public function getEloquentQuery(): Builder
    {
        return Task::query()->with(['collaborator', 'activeTracking', 'evidences'])
            ->whereNotIn('type_task', [TypeTaskEnum::EPIC->value, TypeTaskEnum::FEATURE->value]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('new_tas')
                ->label('Adicionar Task')
                ->model(Task::class)
                ->modal()->slideOver()
                ->modalHeading('Adicionar nova tarefa')
                ->schema([
                    ComponentsHelper::BasicInformationsSection(),
                    ComponentsHelper::VinculeAndResponsible()->columns(2)
                        ->columnSpan(['md' => 12, 'xl' => 8, '2xl' => 7]),
                    Section::make('Evidências e Anexos')
                        ->icon('heroicon-o-paper-clip')
                        ->description('Faça upload de arquivos relacionados à task')
                        ->schema([
                            FileUpload::make('attachments')
                                ->label(__('modules.tasks.form.attachments.label'))
                                ->multiple()
                                ->directory('task-attachments')
                                ->acceptedFileTypes([
                                    'image/*',
                                    'application/pdf',
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/vnd.ms-excel',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                ])
                                ->maxSize(10240)
                                ->downloadable()
                                ->openable()
                                ->afterStateHydrated(function (FileUpload $component, $state, $record) {
                                    if ($record) {
                                        $component->state(
                                            $record->evidences()->pluck('file')->all()
                                        );
                                    }
                                })->saveUploadedFileUsing(fn (TemporaryUploadedFile $file) => $file->store('task-attachments'))
                                ->appendFiles()
                                ->helperText(__('modules.tasks.form.attachments.helpText'))
                                ->columnSpanFull(),
                        ])
                        ->collapsible()
                        ->collapsed()
                        ->columnSpan(['md' => 12, 'xl' => 8, '2xl' => 7]),
                ])
                ->action(function (array $data, Action $action): void {
                    $attachments = $data['attachments'] ?? [];
                    unset($data['attachments']);
                    $data['applicant_id'] = auth()->id();
                    /** @var Task $task */
                    $task = Task::create($data);

                    SyncTaskEvidencesAction::handle($task, $attachments);

                    Notification::make()
                        ->success()
                        ->title('Tarefa criada')
                        ->body('Anexos sincronizados com sucesso.')
                        ->send();

                    $action->getLivewire()->dispatch('$refresh');
                }),
        ];
    }
}
