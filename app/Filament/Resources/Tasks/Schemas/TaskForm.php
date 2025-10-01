<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tasks\Schemas;

use App\Actions\Projects\ListProjectsByLoggedUser;
use App\Actions\Sprints\ListSprintsByProject;
use App\Models\Project;
use Filament\Forms\Components\{FileUpload, RichEditor, Select, TextInput, ToggleButtons};
use Filament\Schemas\Components\{Group, Section};
use Filament\Schemas\Components\Utilities\{Get, Set};
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->columns(12)
            ->components([

                Section::make('Informações Básicas')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->description('Defina o título, descrição e tipo da task')
                    ->schema([
                        ToggleButtons::make('type_task')
                            ->label(__('modules.tasks.form.task.label'))
                            ->options([
                                'task'        => 'Task',
                                'bug'         => 'Bug',
                                'feature'     => 'Feature',
                                'improvement' => 'Melhoria',
                            ])
                            ->icons([
                                'task'        => 'heroicon-m-clipboard-document-check',
                                'bug'         => 'heroicon-m-bug-ant',
                                'feature'     => 'heroicon-m-sparkles',
                                'improvement' => 'heroicon-m-wrench-screwdriver',
                            ])
                            ->colors([
                                'task'        => 'primary',
                                'bug'         => 'danger',
                                'feature'     => 'success',
                                'improvement' => 'warning',
                            ])
                            ->inline()
                            ->required()
                            ->default('task')
                            ->extraAttributes(['class' => 'flex-wrap gap-2']),
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
                    ->columnSpan(['md' => 12, 'xl' => 8, '2xl' => 7])
                    ->collapsible(),

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
                                ->relationship('parent', 'title')
                                ->prefixIcon('heroicon-o-folder')
                                ->searchable()
                                ->preload(),

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
