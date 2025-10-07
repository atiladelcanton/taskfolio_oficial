<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tasks\Pages;

use App\Actions\Tasks\SyncTaskEvidencesAction;
use App\Filament\Resources\Tasks\TaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['applicant_id'] = auth()->user()->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        $attachments = $this->form->getComponent('attachments')->getState() ?? [];

        SyncTaskEvidencesAction::handle($this->record, $attachments);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Task criado com sucesso!';
    }
}
