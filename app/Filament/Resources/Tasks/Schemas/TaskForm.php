<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tasks\Schemas;

use App\Support\Tasks\ComponentsHelper;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\{Group};
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([

                ComponentsHelper::BasicInformationsSection(),

                Group::make([
                    ComponentsHelper::VinculeAndResponsible()->columns(2),
                    ComponentsHelper::AttachEvidences(),
                ])
                    ->columnSpan(['md' => 12, 'xl' => 4, '2xl' => 5])
                    ->extraAttributes(['class' => 'xl:sticky xl:top-6']),
            ]);
    }
}
