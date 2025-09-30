<?php

namespace App\Filament\Resources\Collaborators\Pages;

use App\Enums\UserTypeEnum;
use App\Filament\Resources\Collaborators\CollaboratorResource;
use App\Mail\Welcome;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateCollaborator extends CreateRecord
{
    protected static string $resource = CollaboratorResource::class;

    protected static ?string $title = 'Novo Colaborador';
    private  ?User $user;
    private ?string $password;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $password = Str::random(8);
        $this->password = $password;
        $user = User::query()
            ->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make( $password),
                'type' => $data['type'],
                'force_renew_password' => true,
            ]);
        $data['user_id'] = $user->id;
        $this->user = $user;
        return $data;
    }
    protected function afterCreate(): void
    {
        if($this->user){

            Mail::to($this->user->email)->send(new Welcome($this->user, $this->password));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Cliente criado com sucesso!';
    }
}
