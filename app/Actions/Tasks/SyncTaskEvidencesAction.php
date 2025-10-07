<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\{Task, TaskEvidence};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SyncTaskEvidencesAction
{
    /**
     * Sincroniza a tabela task_evidences com base nos caminhos fornecidos pelo FileUpload.
     *
     * @param  Task|Model  $task  A task alvo
     * @param  array  $paths  Array de caminhos (ex.: ['task-attachments/a.pdf', ...])
     * @param  string  $disk  Disk do Storage (ex.: 'public')
     * @param  bool  $deleteMissingFiles  Se true, remove os arquivos do Storage removidos do form
     */
    public static function handle(Task|Model $task, array $paths, bool $deleteMissingFiles = false): void
    {
        $paths = array_values(array_filter(\Arr::wrap($paths)));
        $current = $task->evidences()->pluck('file')->all();

        $toAdd = array_diff($paths, $current);
        $toDelete = array_diff($current, $paths);

        foreach ($toAdd as $file) {
            TaskEvidence::firstOrCreate([
                'task_id' => $task->id,
                'file'    => $file,
            ]);
        }

        if (! empty($toDelete)) {
            $evidences = TaskEvidence::query()
                ->where('task_id', $task->id)
                ->whereIn('file', $toDelete)
                ->get();

            foreach ($evidences as $evidence) {
                if ($deleteMissingFiles) {
                    Storage::delete($evidence->file);
                }
                $evidence->delete();
            }
        }
    }
}
