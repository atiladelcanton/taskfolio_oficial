<?php

declare(strict_types=1);

namespace App\Actions\Sprints;

use App\Enums\Enums\SprintStatusEnum;
use App\Models\Sprint;
use Carbon\Carbon;

class ListSprintsByProject
{
    public static function handle(int $projectId)
    {
        if (! $projectId) {
            return [];
        }

        return Sprint::query()->where('project_id', $projectId)
            ->whereNotIn('status', [
                SprintStatusEnum::CANCELLED->value,
                SprintStatusEnum::COMPLETED->value,
                SprintStatusEnum::PAUSED->value,
            ])
            ->orderBy('start_at', 'asc')
            ->get()
            ->mapWithKeys(fn ($s) => [
                $s->id => $s->title.' ('.
                   Carbon::parse($s->start_at)->format('d/m/Y').' - '.
                   Carbon::parse($s->end_at)->format('d/m/Y').')',
            ]);
    }
}
