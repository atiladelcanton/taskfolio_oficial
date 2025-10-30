<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttachUsersToSpecificRole extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->get();

        foreach ($users as $user) {
            $roleByType = [
                UserTypeEnum::COLLABORATOR->value => 'COLLABORATOR',
                UserTypeEnum::ADMINISTRADOR->value => 'ADMINISTRADOR',
                UserTypeEnum::CLIENT->value => 'CLIENT',
            ];

            $user->assignRole($roleByType[$user->type]);
        }
    }
}
