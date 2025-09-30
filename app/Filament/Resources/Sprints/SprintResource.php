<?php

declare(strict_types=1);

namespace App\Filament\Resources\Sprints;

use App\Filament\Resources\Sprints\Pages\{CreateSprint, EditSprint, ListSprints};
use App\Filament\Resources\Sprints\Schemas\SprintForm;
use App\Filament\Resources\Sprints\Tables\SprintsTable;
use App\Models\Sprint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SprintResource extends Resource
{
    protected static ?string $model = Sprint::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return SprintForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SprintsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSprints::route('/'),
            'create' => CreateSprint::route('/create'),
            'edit' => EditSprint::route('/{record}/edit'),
        ];
    }
}
