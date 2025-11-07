<?php

declare(strict_types=1);

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\ProjectPayment;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function afterCreate(): void
    {
        $paymentDays = $this->data['paymentDays'];
        foreach ($paymentDays as $day) {
            ProjectPayment::query()->create([
                'project_id' => $this->record->id,
                'payment_type' => $this->data['payment_type'],
                'payment_day' => $day,
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Projeto criado com sucesso!';
    }
}
