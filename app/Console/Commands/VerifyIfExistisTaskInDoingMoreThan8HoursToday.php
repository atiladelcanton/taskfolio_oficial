<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class VerifyIfExistisTaskInDoingMoreThan8HoursToday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verify-if-existis-task-in-doing-more-than8-hours-today';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tasks = Task::query()
            ->with([
                'collaborator',
                'trackingTimes' => function ($query) {
                    $query->whereNotNull('start_at')
                        ->whereNull('stop_at');
                },
            ])
            ->where('status', '=', TaskStatusEnum::Doing)
            ->get();

        $tasks->each(function ($task) {
            $task->trackingTimes->each(function ($time) use ($task) {
                $now = Carbon::now();
                $startAt = $time->start_at;
                $diff = $startAt->diffInHours($now);

                if ($diff > 8) {
                    $time->stop_at = $now;
                    $time->save();

                    Notification::make()
                        ->warning()
                        ->title('Tempo excedido')
                        ->body('Colaborador '.$task->collaborator->name.' sua tarefa foi pausada por exceder o limite de 8 horas no dia.')
                        ->sendToDatabase($task->collaborator->user)->send();
                }
            });
        });
    }
}
