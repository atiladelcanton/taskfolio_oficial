<?php

namespace App\Filament\Resources\Collaborators\Pages;

use App\Filament\Resources\Collaborators\CollaboratorResource;
use App\Models\Collaborator;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EditCollaborator extends EditRecord
{
    protected static string $resource = CollaboratorResource::class;
//    protected function mutateFormDataBeforeSave(array $data): array
//    {
//
//        $collaboratorId = data_get($data, 'id');
//        $userId = Collaborator::query()->findOrFail($collaboratorId)->user_id;
//        $user = User::query()->findOrFail($userId);
//        if($user->email !== $data['email']){
//            $user->email = $data['email'];
//        }
//        if($data['user']['type'] != $user->type){
//            $user->type = $data['user']['type'];
//        }
//        $user->save();
//        return $data;
//    }
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
