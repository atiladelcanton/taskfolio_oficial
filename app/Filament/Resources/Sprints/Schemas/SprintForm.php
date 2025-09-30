<?php

declare(strict_types=1);

namespace App\Filament\Resources\Sprints\Schemas;

use Filament\Forms\Components\{DateTimePicker, Select, TextInput, Textarea};
use Filament\Schemas\Schema;

class SprintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('project_id')
                    ->required()
                    ->numeric(),
                TextInput::make('title')
                    ->required(),
                DateTimePicker::make('start_at'),
                DateTimePicker::make('end_at'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'PLANNING' => 'P l a n n i n g',
                        'ACTIVE' => 'A c t i v e',
                        'PAUSED' => 'P a u s e d',
                        'COMPLETED' => 'C o m p l e t e d',
                        'CANCELLED' => 'C a n c e l l e d',
                    ])
                    ->default('PLANNING')
                    ->required(),
            ]);
    }
}
