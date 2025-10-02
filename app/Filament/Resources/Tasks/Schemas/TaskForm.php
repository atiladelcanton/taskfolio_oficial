<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tasks\Schemas;

use App\Actions\Projects\ListProjectsByLoggedUser;
use App\Actions\Sprints\ListSprintsByProject;
use App\Models\{Task};
use Filament\Actions\Action;
use Filament\Forms\Components\{FileUpload, RichEditor, Select, TextInput, ToggleButtons};
use Filament\Schemas\Components\{Group, Section};
use Filament\Schemas\Components\Utilities\{Get, Set};
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->columns(12)
            ->components([

                Section::make('Informações Básicas')
                    ->afterHeader([
                        Action::make('help')
                            ->label('Ajuda')
                            ->icon(Heroicon::OutlinedQuestionMarkCircle)
                            ->modalSubmitAction(false)
                            ->modalContent(new \Illuminate\Support\HtmlString('
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
                    ->icon('heroicon-o-clipboard-document-list')
                    ->description('Defina o título, descrição e tipo da task')
                    ->schema([
                        ToggleButtons::make('type_task')
                            ->label(__('modules.tasks.form.task.label'))
                            ->options([
                                'epic'       => 'Epico',
                                'task'        => 'Task',
                                'bug'         => 'Bug',
                                'feature'     => 'Feature',
                                'improvement' => 'Melhoria',
                            ])
                            ->icons([
                                'epic'        => 'heroicon-m-sparkles',
                                'task'        => 'heroicon-m-clipboard-document-check',
                                'bug'         => 'heroicon-m-bug-ant',
                                'feature'     => 'heroicon-m-sparkles',
                                'improvement' => 'heroicon-m-wrench-screwdriver',
                            ])
                            ->colors([
                                'epic'        => 'info',
                                'task'        => 'primary',
                                'bug'         => 'danger',
                                'feature'     => 'success',
                                'improvement' => 'warning',
                            ])
                            ->inline()
                            ->required()
                            ->grouped()
                            ->default('task')
                            ->extraAttributes(['class' => 'flex-wrap gap-2'])
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $set('parent_id', null);
                            })->rules([
                                fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
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
                            ])->columnSpanFull(),
                        ToggleButtons::make('priority')
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
                            ->required(),
                        ToggleButtons::make('status')
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
                            ->default('backlog'),

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
                    ->columnSpan(['md' => 12, 'xl' => 8, '2xl' => 7]),
                Group::make([
                    Section::make('Vinculação e Responsáveis')
                        ->icon('heroicon-o-user-group')
                        ->description('Defina a sprint, responsáveis e dependências')
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
                                    fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
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
                                ->searchable()
                                ->preload(),
                        ])
                        ->columns(2)
                        ->collapsible(),

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
                                ->previewable()
                                ->openable()
                                ->reorderable()
                                ->appendFiles()
                                ->helperText(__('modules.tasks.form.attachments.helpText'))
                                ->columnSpanFull()
                                ->imagePreviewHeight('250'),
                        ])
                        ->collapsible()
                        ->collapsed(),
                ])
                    ->columnSpan(['md' => 12, 'xl' => 4, '2xl' => 5])
                    ->extraAttributes(['class' => 'xl:sticky xl:top-6']),
            ]);
    }
}
