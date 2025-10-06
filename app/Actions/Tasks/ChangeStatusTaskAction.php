<?php

namespace App\Actions\Tasks;

use App\Enums\TaskStatusEnum;
use Filament\Notifications\Notification;

class ChangeStatusTaskAction
{
    public static function handle($record, array $data,$action=null): void
    {
        $from = $record->status instanceof TaskStatusEnum
            ? $record->status
            : (TaskStatusEnum::tryFrom((string) $record->status) ?? TaskStatusEnum::Backlog);

        $to = $data['status'] instanceof TaskStatusEnum
            ? $data['status']
            : (TaskStatusEnum::tryFrom((string) $data['status']) ?? TaskStatusEnum::Backlog);

        if (! $from->canTransitionTo($to)) {
            Notification::make()->danger()
                ->title('Transição inválida')
                ->body('De ' . $from->label() . ' para ' . $to->label() . '.')
                ->send();
            return;
        }

        $record->status = $to->value;
        $record->save();


        if(!is_null($action)){
            Notification::make()->success()
                ->title('Status atualizado')
                ->body('De ' . $from->label() . ' para ' . $to->label() . '.')
                ->send();
            $action->getLivewire()->dispatch('$refresh');
        }

    }
}
