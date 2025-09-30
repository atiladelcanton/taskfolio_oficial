<?php

declare(strict_types=1);

namespace App\Filament\Resources\Collaborators\RelationManagers;

use App\Filament\Resources\Collaborators\CollaboratorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $relatedResource = CollaboratorResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
