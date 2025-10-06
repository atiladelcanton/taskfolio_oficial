<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tasks\Pages;

use App\Actions\Tasks\SyncTaskEvidencesAction;
use App\Filament\Resources\Tasks\TaskResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use JetBrains\PhpStorm\NoReturn;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;
    #[NoReturn]
    protected function afterSave(): void
    {
        $attachments = $this->form->getState()['attachments'] ?? [];

        SyncTaskEvidencesAction::handle($this->record, $attachments, 'public', false);
    }
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
