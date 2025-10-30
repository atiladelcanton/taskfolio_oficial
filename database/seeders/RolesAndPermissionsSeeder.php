<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission, Role};

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        $permissions = [
            // Projects
            'project.view', 'project.create', 'project.update', 'project.delete',

            // Sprints
            'sprint.view', 'sprint.create', 'sprint.update', 'sprint.delete',

            // Tasks
            'task.view', 'task.create', 'task.update', 'task.delete',

            // TaskBoard
            'task.move',

            // Clients
            'client.view', 'client.create', 'client.update', 'client.delete',

            // Projects
            'project.view', 'project.create', 'project.update', 'clieproject.delete',

            // Collaborators
            'collaborator.view', 'collaborator.create', 'collaborator.update', 'collaborator.delete', 'collaborator.attach', 'collaborator.detach',
        ];

        foreach ($permissions as $name) {
            Permission::query()->firstOrCreate(['name' => $name, 'guard_name' => $guard]);
        }

        // ROLES
        $admin = Role::query()->firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => $guard]);
        $client = Role::query()->firstOrCreate(['name' => 'CLIENT', 'guard_name' => $guard]);
        $collab = Role::query()->firstOrCreate(['name' => 'COLLABORATOR', 'guard_name' => $guard]);

        // ADMIN: tudo
        $admin->syncPermissions(Permission::pluck('name'));

        // CLIENT:
        // - Vê seus projetos (policy restringe ao "seus")
        // - NÃO cria/edita/deleta projetos
        // - Vê e PODE criar sprints e tasks
        // - NÃO pode mover tasks no board
        $client->syncPermissions([
            'project.view',
            'sprint.view', 'sprint.create', 'sprint.update',
            'task.view', 'task.create', 'task.update',
        ]);

        $collab->syncPermissions([
            'sprint.view',
            'task.view', 'task.create', 'task.update',
            'task.move',
        ]);
    }
}
