<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clients\Pages;

use App\Enums\UserTypeEnum;
use App\Filament\Resources\Clients\ClientResource;
use App\Mail\Welcome;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\{Hash, Mail};
use Illuminate\Support\Str;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected static ?string $title = 'Novo Cliente';

    private ?User $user;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $password = Str::random(8);
        $user = User::query()
            ->create([
                'name' => $data['personal_name'],
                'email' => $data['email'],
                'password' => Hash::make($password),
                'type' => UserTypeEnum::CLIENT,
                'force_renew_password' => true,
            ]);
        $data['user_id'] = $user->id;
        $this->user = $user;

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->user) {
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
