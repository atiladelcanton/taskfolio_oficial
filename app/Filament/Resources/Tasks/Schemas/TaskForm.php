<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Forms\Components\{Select, TextInput, Textarea};
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sprint_id')
                    ->required()
                    ->numeric(),
                TextInput::make('parent_id')
                    ->numeric(),
                TextInput::make('applicant_id')
                    ->required()
                    ->numeric(),
                TextInput::make('collaborator_id')
                    ->numeric(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Select::make('type_task')
                    ->options(['epic' => 'Epic', 'bug' => 'Bug', 'task' => 'Task'])
                    ->default('task')
                    ->required(),
                TextInput::make('total_time_worked'),
            ]);
    }
}
