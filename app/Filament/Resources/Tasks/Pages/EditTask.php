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

    protected function afterSave(): void
    {
        $attachments = $this->form->getComponent('attachments')->getState() ?? [];

        SyncTaskEvidencesAction::handle($this->record, $attachments);
    }
    protected function getSavedNotificationMessage(): ?string
    {
        return 'Task atualizada com sucesso!';
    }
    protected function getRedirectUrl(): string
    {

        return route('filament.app.pages.task-board');
    }
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
