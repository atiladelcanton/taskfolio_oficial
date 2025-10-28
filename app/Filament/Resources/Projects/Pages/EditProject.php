<?php

declare(strict_types=1);

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\ProjectPayment;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {

        $paymentDays = $this->data['paymentDays'];
        ProjectPayment::query()->where('project_id', $this->record->id)->delete();
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

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Projeto atualizado com sucesso!';
    }
}
