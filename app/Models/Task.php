<?php

namespace App\Models;

use App\Enums\TaskType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'sprint_id',
        'parent_id',
        'applicant_id',
        'collaborator_id',
        'title',
        'description',
        'type_task',
        'total_time_worked',
    ];

    protected function casts(): array
    {
        return [
            'type_task' => 'string',
        ];
    }

    // Relacionamentos
    public function sprint()
    {
        return $this->belongsTo(Sprint::class);
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function collaborator()
    {
        return $this->belongsTo(User::class, 'collaborator_id');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function evidences()
    {
        return $this->hasMany(TaskEvidence::class);
    }

    public function trackingTimes()
    {
        return $this->hasMany(TaskTrackingTime::class);
    }

    // Acessors
    public function getIsEpicAttribute()
    {
        return $this->type_task === TaskType::EPIC;
    }

    public function getIsBugAttribute()
    {
        return $this->type_task === TaskType::BUG;
    }

    public function getIsTaskAttribute()
    {
        return $this->type_task === TaskType::TASK;
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type_task', $type);
    }

    public function scopeEpics($query)
    {
        return $query->where('type_task', TaskType::EPIC);
    }

    public function scopeBugs($query)
    {
        return $query->where('type_task', TaskType::BUG);
    }

    public function scopeTasks($query)
    {
        return $query->where('type_task', TaskType::TASK);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('collaborator_id', $userId);
    }
}
