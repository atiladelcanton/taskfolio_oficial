<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TaskEvidenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskEvidence extends Model
{
    /** @use HasFactory<TaskEvidenceFactory> */
    use HasFactory;

    protected $table = 'task_evidences';
    protected $fillable = [
        'task_id',
        'file',
    ];

    // Relacionamentos
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Scopes
    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }
}
