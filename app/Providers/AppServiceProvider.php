<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Collaborator;
use App\Models\Project;
use App\Models\Task;
use App\Policies\ClientPolicy;
use App\Policies\CollaboratorPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Collaborator::class, CollaboratorPolicy::class);
    }
}
