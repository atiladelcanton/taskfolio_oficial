<?php

declare(strict_types=1);

namespace App\Filament\Resources\Sprints\Pages;

use App\Filament\Resources\Sprints\SprintResource;
use Filament\Resources\Pages\EditRecord;

class EditSprint extends EditRecord
{
    protected static string $resource = SprintResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Sprint atualizada com sucesso!';
    }
}
