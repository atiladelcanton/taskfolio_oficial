<?php

declare(strict_types=1);

namespace App\Filament\Resources\Sprints\Pages;

use App\Filament\Resources\Sprints\SprintResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;

class CreateSprint extends CreateRecord
{
    protected static string $resource = SprintResource::class;

    protected static ?string $title = 'Nova Sprint';

    /**
     * @throws Halt
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Valida sobreposição antes de salvar
        $overlapping = \App\Models\Sprint::hasOverlappingSprintInProject(
            $data['project_id'],
            $data['start_at'],
            $data['end_at']
        );

        if ($overlapping) {
            Notification::make()
                ->danger()
                ->title('Conflito de Datas!')
                ->body("Já existe a sprint \"{$overlapping->title}\" no período de "
                    .\Carbon\Carbon::parse($overlapping->start_at)->format('d/m/Y')
                    .' até '
                    .\Carbon\Carbon::parse($overlapping->end_at)->format('d/m/Y'))
                ->persistent()
                ->send();

            $this->halt();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Sprint criada com sucesso!';
    }
}
