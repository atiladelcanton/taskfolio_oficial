<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Enums\Enums\UserTypeEnum;
use App\Models\Project;
use Illuminate\Support\Collection;

class ListProjectsByLoggedUser
{
    public static function handle(): Collection
    {
        $userId = auth()->id();
        if (!auth()->user()->hasRole('ADMINISTRATOR')) {
            $owned = Project::query()->whereHas('client', fn ($q) => $q->where('user_id', $userId))->get();
            $collab = Project::query()->whereHas('collaborators', fn ($q) => $q->where('user_id', $userId))->get();
            $all = $owned->merge($collab)->unique('id');
        } else {
            $all = Project::query()->orderBy('project_name')->get();
        }

        return $all->mapWithKeys(function ($project) {
            return [$project->id => "({$project->client->company_name}) {$project->project_name}"];
        });
    }
}
