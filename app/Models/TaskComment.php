<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    /** @use HasFactory<\Database\Factories\TaskCommentFactory> */
    use HasFactory;

    protected $fillable = [
        'task_id',
        'owner_id',
        'description',
    ];

    // Relacionamentos
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Scopes
    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }
}
