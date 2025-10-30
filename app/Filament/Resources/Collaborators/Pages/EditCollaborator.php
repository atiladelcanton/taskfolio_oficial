<?php

declare(strict_types=1);

namespace App\Filament\Resources\Collaborators\Pages;

use App\Enums\Enums\UserTypeEnum;
use App\Filament\Resources\Collaborators\CollaboratorResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class EditCollaborator extends EditRecord
{
    protected static string $resource = CollaboratorResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Carrega o type do usuário para o campo user_type
        if ($this->record->user) {
            $data['user_type'] = $this->record->user->role;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['user_type']) && $this->record->user) {
            $this->record->user->assignRole(UserTypeEnum::getRoleName($data['user_type']));
        }

        unset($data['user_type']);

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Colaborador atualizado com sucesso!';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('disable')
                ->label('Desativar Colaborador')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Desativar Colaborador')
                ->modalDescription(fn () => new \Illuminate\Support\HtmlString(
                    'Você está prestes a desativar o colaborador <strong>'
                    .e($this->record->name)
                    .'</strong>.<br><br>'
                    .'<span class="inline-flex items-center rounded-md bg-yellow-50 px-3 py-2 text-sm font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20">'
                    .'⚠️ Ao desativar, o colaborador perderá o acesso ao sistema imediatamente.'
                    .'</span><br><br>'
                    .'Deseja continuar?'
                ))
                ->modalIcon('heroicon-o-exclamation-triangle')
                ->modalIconColor('warning')
                ->modalSubmitActionLabel('Sim, desativar')
                ->modalCancelActionLabel('Cancelar')
                ->action(function () {
                    if ($this->record->user) {
                        $this->record->user->update([
                            'active' => 0,
                        ]);
                    }
                    $this->record->update([
                        'status' => 0,
                    ]);
                    \Filament\Notifications\Notification::make()
                        ->title('Colaborador desativado com sucesso')
                        ->success()
                        ->send();
                })->visible(fn (Model $record): bool => $this->record->status === 1),
            Action::make('reativate')
                ->label('Ativar Colaborador')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Ativar Colaborador')
                ->modalDescription(fn () => new \Illuminate\Support\HtmlString(
                    'Você está prestes a ativar o colaborador <strong>'
                    .e($this->record->name)
                    .'</strong>.<br><br>'
                    .'<span class="inline-flex items-center rounded-md bg-yellow-50 px-3 py-2 text-sm font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20">'
                    .'⚠️ Ao ativar, o colaborador voltara a o acesso ao sistema imediatamente.'
                    .'</span><br><br>'
                    .'Deseja continuar?'
                ))
                ->modalIcon('heroicon-o-exclamation-triangle')
                ->modalIconColor('info')
                ->modalSubmitActionLabel('Sim, ativar')
                ->modalCancelActionLabel('Cancelar')
                ->action(function () {
                    if ($this->record->user) {
                        $this->record->user->update([
                            'active' => 1,
                        ]);
                    }
                    $this->record->update([
                        'status' => 1,
                    ]);
                    \Filament\Notifications\Notification::make()
                        ->title('Colaborador ativado com sucesso')
                        ->success()
                        ->send();
                })->visible(fn (Model $record): bool => $this->record->status === 0),
        ];
    }
}
