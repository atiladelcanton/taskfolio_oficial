<?php

declare(strict_types=1);

namespace App\Support\Tasks;

use App\Actions\Projects\ListProjectsByLoggedUser;
use App\Actions\Sprints\ListSprintsByProject;
use App\Enums\TaskStatusEnum;
use App\Models\{Collaborator, Task, TaskTrackingTime};
use Closure;
use Filament\Actions\{Action, Action as ModalAction};
use Filament\Forms\Components\{FileUpload, Repeater\TableColumn, RichEditor, Select, TextInput, ToggleButtons};
use Filament\Infolists\Components\{RepeatableEntry, TextEntry};
use Filament\Notifications\Notification;
use Filament\Schemas\Components\{Grid, Section};
use Filament\Schemas\Components\Utilities\{Get, Set};
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\{HtmlString, Str};

class ComponentsHelper
{
    public static function BasicInformationsSection(): Section
    {
        return Section::make('Informações Básicas')
            ->afterHeader([
                Action::make('help')
                    ->label('Ajuda')
                    ->icon(Heroicon::OutlinedQuestionMarkCircle)
                    ->modalSubmitAction(false)
                    ->modalContent(new HtmlString('
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-900">

                            <ul class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                                <li>🎯 <strong>EPIC</strong> → pode ter apenas <strong>FEATUREs</strong></li>
                                <li>⭐ <strong>FEATURE</strong> → pode ter <strong>TASKs, BUGs ou MELHORIAs</strong></li>
                                <li>📋 <strong>TASK</strong> → pode ter outras <strong>TASKs, BUGs ou MELHORIAs</strong></li>
                                <li>🐛 <strong>BUG/MELHORIA</strong> → pode ter <strong>TASKs, BUGs ou MELHORIAs</strong></li>
                                <li><hr/></li>
                                <li>⭐Tasks novas só podem ser criadas nestes status iniciais (BACKLOG,REFINAMENTO,TODO)</li>
                            </ul>
                        </div>
                    ')),
            ])
            ->collapsible()
            ->icon('heroicon-o-clipboard-document-list')
            ->description('Defina o título, descrição e tipo da task')
            ->schema([
                self::ToggleButtonTypeTask()->columnSpanFull(),
                self::ToggleButtonPriority(),
                self::ToggleButtonStatus(),

                TextInput::make('title')
                    ->label(__('modules.tasks.form.title.label'))
                    ->placeholder(__('modules.tasks.form.title.placeholder'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                RichEditor::make('description')
                    ->label(__('modules.tasks.form.description.label'))
                    ->helperText(__('modules.tasks.form.description.helpText'))
                    ->placeholder(__('modules.tasks.form.description.placeholder'))
                    ->required()
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'min-h-[200px]']),
                RichEditor::make('accept_criteria')
                    ->label(__('modules.tasks.form.accept_criteria.label'))
                    ->helperText(__('modules.tasks.form.accept_criteria.helpText'))
                    ->placeholder(__('modules.tasks.form.accept_criteria.placeholder'))
                    ->required()
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'min-h-[200px]']),
                RichEditor::make('scene_test')
                    ->label(__('modules.tasks.form.scene_test.label'))
                    ->helperText(__('modules.tasks.form.scene_test.helpText'))
                    ->placeholder(__('modules.tasks.form.scene_test.placeholder'))
                    ->required()
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'min-h-[200px]']),
                RichEditor::make('ovservations')
                    ->label(__('modules.tasks.form.ovservations.label'))
                    ->helperText(__('modules.tasks.form.ovservations.helpText'))
                    ->placeholder(__('modules.tasks.form.ovservations.placeholder'))
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'min-h-[200px]']),
            ])
            ->columns(2)
            ->columnSpan(['md' => 12, 'xl' => 8, '2xl' => 7]);
    }

    public static function ToggleButtonTypeTask(): ToggleButtons
    {
        return ToggleButtons::make('type_task')
            ->label(__('modules.tasks.form.task.label'))
            ->options([
                'epic' => 'Epico',
                'task' => 'Task',
                'bug' => 'Bug',
                'feature' => 'Feature',
                'improvement' => 'Melhoria',
            ])
            ->icons([
                'epic' => 'heroicon-m-sparkles',
                'task' => 'heroicon-m-clipboard-document-check',
                'bug' => 'heroicon-m-bug-ant',
                'feature' => 'heroicon-m-sparkles',
                'improvement' => 'heroicon-m-wrench-screwdriver',
            ])
            ->colors([
                'epic' => 'info',
                'task' => 'primary',
                'bug' => 'danger',
                'feature' => 'success',
                'improvement' => 'warning',
            ])
            ->inline()
            ->required()
            ->grouped()
            ->default('task')
            ->extraAttributes(['class' => 'flex-wrap'])
            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                $set('parent_id', null);
            })->rules([
                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                    $parentId = $get('parent_id');

                    if (! $parentId) {
                        return;
                    }

                    $parent = Task::query()->find($parentId);

                    if (! $parent) {
                        return;
                    }
                    // Regra 1: EPIC só pode ter FEATURE como filha
                    if ($parent->type_task === 'epic' && $value !== 'feature') {
                        $fail('Uma EPIC só pode ter tarefas do tipo FEATURE como filhas diretas.');
                    }
                    // Regra 2: FEATURE só pode ter TASK, BUG ou IMPROVEMENT como filha
                    if ($parent->type_task === 'feature' && ! in_array($value, ['task', 'bug', 'improvement'])) {
                        $fail('Uma FEATURE só pode ter tarefas do tipo TASK, BUG ou MELHORIA como filhas.');
                    }
                    // Regra 3: TASK não pode ter EPIC nem FEATURE como filha
                    if ($parent->type_task === 'task' && in_array($value, ['epic', 'feature'])) {
                        $fail('Uma TASK não pode ter EPIC ou FEATURE como filhas. Apenas outras TASKS, BUGS ou MELHORIAS.');
                    }
                },
            ]);
    }

    public static function ToggleButtonPriority(): ToggleButtons
    {
        return ToggleButtons::make('priority')
            ->label('Prioridade')
            ->options([
                'low' => 'Baixa',
                'medium' => 'Média',
                'high' => 'Alta',
                'urgent' => 'Urgente',
            ])
            ->icons([
                'low' => 'heroicon-m-arrow-down',
                'medium' => 'heroicon-m-minus',
                'high' => 'heroicon-m-arrow-up',
                'urgent' => 'heroicon-m-fire',
            ])
            ->colors([
                'low' => 'gray',
                'medium' => 'info',
                'high' => 'warning',
                'urgent' => 'danger',
            ])
            ->grouped()
            ->default('medium')
            ->required();
    }

    public static function ToggleButtonStatus(): ToggleButtons
    {
        return ToggleButtons::make('status')
            ->label('Status Inicial')
            ->options([
                'backlog' => 'Backlog',
                'refinement' => 'Refinamento',
                'todo' => 'To Do',
            ])
            ->icons([
                'backlog' => 'heroicon-m-inbox-stack',
                'refinement' => 'heroicon-m-sparkles',
                'todo' => 'heroicon-m-clipboard-document-check',
            ])
            ->colors([
                'backlog' => 'gray',
                'refinement' => 'info',
                'todo' => 'warning',
            ])
            ->required()->grouped()
            ->default('backlog');
    }

    public static function VinculeAndResponsible(bool $isModal = false): Section
    {
        return Section::make('Vinculação e Responsáveis')
            ->icon('heroicon-o-user-group')
            ->description('Defina a sprint, responsáveis e dependências')
            ->collapsible()
            ->schema([
                Select::make('project_id')
                    ->label(__('modules.tasks.form.project_id.label'))
                    ->prefixIcon('heroicon-o-briefcase')
                    ->options(fn () => ListProjectsByLoggedUser::handle())
                    ->preload()
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('sprint_id', null))
                    ->helperText(__('modules.tasks.form.project_id.helpText'))
                    ->columnSpanFull(),

                Select::make('sprint_id')
                    ->label('Sprint')
                    ->prefixIcon('heroicon-o-rocket-launch')
                    ->options(fn (Get $get) => $get('project_id') == null ? [] : ListSprintsByProject::handle($get('project_id')))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn (Get $get) => ! $get('project_id'))
                    ->helperText(__('modules.tasks.form.sprint_id.helpText'))
                    ->live()
                    ->native(false),

                Select::make('parent_id')
                    ->label('Task Pai (Dependência)')
                    ->prefixIcon('heroicon-o-folder')
                    ->options(function (Get $get) {
                        $sprintId = $get('sprint_id');
                        $currentType = $get('type_task');

                        if (! $sprintId) {
                            return [];
                        }

                        // Busca todas as tasks da mesma sprint
                        $query = Task::query();

                        // Filtra opções baseado no tipo atual
                        if ($currentType === 'epic') {
                            // EPIC não pode ter pai
                            return [];
                        }

                        if ($currentType === 'feature') {
                            // FEATURE só pode ter EPIC como pai
                            $query->where('type_task', 'epic');
                        }

                        if (in_array($currentType, ['task', 'bug', 'improvement'])) {
                            // TASK/BUG/IMPROVEMENT pode ter FEATURE ou TASK como pai
                            $query->whereIn('type_task', ['feature', 'task']);
                        }

                        return $query->get()->mapWithKeys(function ($task) {
                            $typeLabel = match ($task->type_task) {
                                'epic' => '🎯 EPIC',
                                'feature' => '⭐ FEATURE',
                                'task' => '📋 TASK',
                                'bug' => '🐛 BUG',
                                'improvement' => '✨ MELHORIA',
                                default => $task->type_task,
                            };

                            return [$task->id => "{$typeLabel}: {$task->title}"];
                        });
                    })
                    ->searchable()
                    ->preload()
                    ->disabled(fn (Get $get) => ! $get('sprint_id') || $get('type_task') === 'epic')
                    ->helperText(function (Get $get) {
                        $type = $get('type_task');

                        return match ($type) {
                            'epic' => '❌ EPIC é o nível mais alto e não pode ter pai',
                            'feature' => 'FEATURE deve ter uma EPIC como pai',
                            'task', 'bug', 'improvement' => 'Pode ter uma FEATURE ou outra TASK como pai',
                            default => 'Caso esta task dependa de outra (opcional)',
                        };
                    })
                    ->live()
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            if (! $value) {
                                return; // Parent é opcional
                            }

                            $currentType = $get('type_task');
                            $parent = Task::find($value);

                            if (! $parent) {
                                return;
                            }

                            // Validação reversa: verifica se o tipo atual é válido para o pai escolhido

                            // Se o pai é EPIC, só pode ser FEATURE
                            if ($parent->type_task === 'epic' && $currentType !== 'feature') {
                                $fail('Esta EPIC só pode ter FEATUREs como filhas.');
                            }

                            // Se o pai é FEATURE, só pode ser TASK/BUG/IMPROVEMENT
                            if ($parent->type_task === 'feature' && ! in_array($currentType, ['task', 'bug', 'improvement'])) {
                                $fail('Esta FEATURE só pode ter TASKs, BUGs ou MELHORIAs como filhas.');
                            }

                            // Se o pai é TASK, só pode ser TASK/BUG/IMPROVEMENT
                            if ($parent->type_task === 'task' && in_array($currentType, ['epic', 'feature'])) {
                                $fail('Esta TASK não pode ter EPIC ou FEATURE como filha.');
                            }
                        },
                    ]),
                Select::make('collaborator_id')
                    ->label('Colaborador Responsável')
                    ->relationship('collaborator', 'name')
                    ->prefixIcon('heroicon-o-user-circle')
                    ->visible()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function AttachEvidences(): Section
    {
        return Section::make('Evidências e Anexos')
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
                    ->preserveFilenames()
                    ->dehydrated(false)
                    ->afterStateHydrated(function (FileUpload $component, $state, $record) {
                        if ($record) {
                            $component->state(
                                $record->evidences()->pluck('file')->all()
                            );
                        }
                    })
                    ->appendFiles()
                    ->helperText(__('modules.tasks.form.attachments.helpText'))
                    ->columnSpanFull(),
            ])
            ->collapsible()
            ->collapsed();
    }

    public static function getActionsTaskBoard(): array
    {
        return [
            self::ActionPlayStopTask(),
            self::ShowDetailTask(),
        ];
    }

    private static function ActionPlayStopTask(): Action
    {
        return Action::make('acoes')
            ->label('Trabalhar')
            ->icon('heroicon-m-clock')
            ->color('gray')
            ->size('lg')
            ->modalHeading('Cronômetro & Histórico')
            ->modalWidth('2xl')
            ->modalIcon('heroicon-m-clock')
            ->modalSubmitActionLabel(fn ($record) => $record->activeTracking ? 'Pausar' : 'Iniciar')
            ->modalCancelActionLabel('Fechar')
            ->visible(fn($record) => $record->status === TaskStatusEnum::Doing->value)

            ->extraModalFooterActions([
                ModalAction::make('assumir')
                    ->label(function ($record) {
                        $current = $record->collaborator->name ?? null;

                        return $record->collaborator_id ===  Collaborator::query()->where('user_id', Auth::id())->first()->id
                            ? 'Você já é o responsável'
                            : ($current ? "Assumir tarefa (substituir {$current})" : 'Assumir tarefa');
                    })
                    ->icon('heroicon-m-user-plus')
                    ->color('primary')
                    ->disabled(fn ($record) => $record->collaborator_id === Collaborator::query()->where('user_id', Auth::id())->first()->id)
                    ->action(function ($record) {
                        $record->update(['collaborator_id' => Collaborator::query()->where('user_id', Auth::id())->first()->id]);
                        Notification::make()
                            ->success()
                            ->title('Tarefa assumida')
                            ->body('Você agora é o colaborador responsável por esta tarefa.')
                            ->send();
                    }),
            ])
            ->modalContent(function ($record) {
                $tz = auth()->user()->timezone ?? config('app.timezone', 'UTC');

                $rows = TaskTrackingTime::query()
                    ->where('task_id', $record->id)
                    ->orderBy('start_at', 'desc')
                    ->get()
                    ->groupBy(fn ($t) => $t->start_at->timezone($tz)->toDateString())
                    ->map(function ($group) {
                        return $group->sum(function ($t) {
                            $end = $t->stop_at ?? now();

                            return $t->start_at->diffInSeconds($end);
                        });
                    })
                    ->sortKeysDesc();

                $grand = $rows->sum();

                // Status atual
                $activeTracking = $record->activeTracking;
                $now = now();
                $currentSeconds = 0;
                $currentSince = null;

                if ($activeTracking) {
                    $currentSeconds = $activeTracking->start_at->diffInSeconds($now);
                    $currentSince = $activeTracking->start_at->timezone($tz);
                }

                $format = function (int $seconds) {
                    $h = intdiv($seconds, 3600);
                    $m = intdiv(($seconds % 3600), 60);
                    $s = $seconds % 60;

                    return sprintf('%02d:%02d:%02d', $h, $m, $s);
                };

                return view('filament/tasks/partials/tracking-playpause-modal', [
                    'rows' => $rows,              // 'YYYY-MM-DD' => total em segundos
                    'grand' => $grand,             // total geral em segundos
                    'format' => $format,
                    'tz' => $tz,
                    'active' => (bool) $activeTracking,
                    'currentSince' => $currentSince,
                    'currentSeconds' => $currentSeconds,
                    'collaborator' => $record->collaborator->name ?? null,
                    'collaboratorId' => $record->collaborator_id,
                ]);
            })
            ->action(function ($record) {
                $activeTracking = $record->activeTracking;
                if($record->collaborator_id === Collaborator::query()->where('user_id', Auth::id())->first()->id) {
                    if ($activeTracking) {
                        // PAUSAR
                        $activeTracking->update(['stop_at' => now()]);
                        $record->updateTotalTimeWorked();

                        $duration = $activeTracking->duration_in_hours ?? round(
                            ($activeTracking->start_at->diffInSeconds($activeTracking->stop_at) / 3600),
                            2
                        );

                        Notification::make()
                            ->success()
                            ->title('Tempo pausado')
                            ->body("Sessão de {$duration}h registrada. Total acumulado: {$record->total_time_worked}h")
                            ->send();
                    }
                    else
                    {
                        // INICIAR
                        TaskTrackingTime::create([
                            'task_id' => $record->id,
                            'collaborator_id' => auth()->id(),
                            'start_at' => now(),
                            'stop_at' => null,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Tempo iniciado')
                            ->body('Cronômetro em execução')
                            ->send();
                    }
                }else{
                    Notification::make()
                        ->warning()
                        ->title('Ops!')
                        ->body("Você so pode iniciar ou pausar uma tarefa que pertença a você!")
                        ->send();
                }

            });
    }

    private static function ShowDetailTask(): Action
    {
        return Action::make('taskDetails')
            ->modalHeading(fn (Task $record) => "Detalhes — {$record->title}")
            ->icon(Heroicon::OutlinedEye)
            ->modalWidth('5xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Fechar')
            ->label('Detalhes')
            ->modalFooterActions(fn (Task $record) => [
                ModalAction::make('edit_from_details')
                    ->label('Editar')
                    ->icon('heroicon-m-pencil-square')
                    ->color('primary')
                    ->url(fn (Task $record) => route('filament.app.resources.tasks.edit', $record->id).'?redirectTo=taskboard', true),
            ])
            ->schema([
                Grid::make(12)->schema([
                    TextEntry::make('project.project_name')
                        ->label('Projeto')->badge()->color('info')->columnSpan(3),

                    TextEntry::make('sprint.title')
                        ->label('Sprint')->badge()->color('primary')->columnSpan(3),

                    TextEntry::make('collaborator.name')
                        ->label('Responsável')->badge()->icon('heroicon-m-user')->columnSpan(3),

                    TextEntry::make('priority')
                        ->label('Prioridade')->badge()
                        ->icon(function ($state) {
                            if ($state === null) {
                                return '';
                            }
                            $raw = trim((string) $state);

                            return match ($raw) {
                                'low' => 'heroicon-m-arrow-down',
                                'medium' => 'heroicon-m-minus',
                                'high' => 'heroicon-m-arrow-up',
                                'urgent' => 'heroicon-m-fire'
                            };
                        })
                        ->color(function ($state) {
                            if ($state === null) {
                                return '';
                            }
                            $raw = trim((string) $state);

                            return match ($raw) {
                                'low' => 'gray',
                                'medium' => 'info',
                                'high' => 'warning',
                                'urgent' => 'danger'
                            };
                        })
                        ->formatStateUsing(function ($state) {
                            if ($state === null) {
                                return '';
                            }
                            $raw = trim((string) $state);

                            return match ($raw) {
                                'low' => 'Baixa',
                                'medium' => 'Média',
                                'high' => 'Alta',
                                'urgent' => 'Urgente'
                            };
                        })
                        ->columnSpan(3),
                ]),
                Section::make('description')
                    ->heading(__('modules.tasks.form.description.label'))
                    ->collapsible()
                    ->schema([
                        TextEntry::make('description')
                            ->hiddenLabel(true)
                            ->markdown()
                            ->columnSpanFull(),
                    ]),
                Section::make('accept_criteria')
                    ->heading(__('modules.tasks.form.accept_criteria.label'))
                    ->collapsible()
                    ->collapsed(true)
                    ->schema([
                        TextEntry::make('accept_criteria')
                            ->hiddenLabel(true)
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Task $record) => ! is_null($record->accept_criteria)),
                Section::make('scene_test')
                    ->heading(__('modules.tasks.form.scene_test.label'))
                    ->collapsible()
                    ->collapsed(true)
                    ->schema([
                        TextEntry::make('scene_test')
                            ->hiddenLabel(true)
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Task $record) => ! is_null($record->scene_test)),
                Section::make('ovservations')
                    ->heading(__('modules.tasks.form.ovservations.label'))
                    ->collapsible()
                    ->collapsed(true)
                    ->schema([
                        TextEntry::make('scene_test')
                            ->hiddenLabel(true)
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Task $record) => ! is_null($record->ovservations)),
                RepeatableEntry::make('evidences')
                    ->emptyTooltip('Nenhuma evidencia fornecida')
                    ->table([
                        TableColumn::make('Arquivo'),
                        TableColumn::make('Download'),
                    ])
                    ->schema([
                        TextEntry::make('file')
                            ->label('Arquivo')
                            ->formatStateUsing(fn ($state) => Str::of((string) $state)->afterLast('/')),
                        TextEntry::make('file')
                            ->label('Download')
                            ->formatStateUsing(function ($record, $state) {
                                $url = \URL::temporarySignedRoute(
                                    'force_download',
                                    now()->addMinutes(2),
                                    ['evidence' => $record->id]
                                );

                                return new HtmlString(
                                    "<a href=\"{$url}\" target=\"_blank\" class=\"inline-flex items-center gap-1 text-primary-600 hover:underline\">
                                            <span>Download</span>
                                            </a>"
                                );
                            }),
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
