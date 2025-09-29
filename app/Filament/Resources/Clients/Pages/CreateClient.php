<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected static ?string $title = 'Novo Cliente';
    private  ?User $user;


    protected static bool $canCreateAnother = false;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $password = Str::random(8);
        $user = User::query()
            ->create([
                'name' => $data['personal_name'],
                'email' => $data['email'],
                'password' => Hash::make( $password),
            ]);
        $data['user_id'] = $user->id;
        $this->user = $user;
        return $data;
    }
    protected function afterCreate(): void
    {
        if($this->user){
            dd('Disarear evento para enviar email de boas vindas');
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
