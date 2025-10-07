<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Enums\UserTypeEnum;
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
        'project_id',
        'title',
        'description',
        'accept_criteria',
        'scene_test',
        'ovservations',
        'type_task',
        'total_time_worked',
        'priority',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type_task' => 'string',
            'priority' => 'string',
        ];
    }

    // Relacionamentos
    public function sprint()
    {
        return $this->belongsTo(Sprint::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function collaborator()
    {
        return $this->belongsTo(User::class, 'collaborator_id')->where('type', UserTypeEnum::COLLABORATOR->value);
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

    public function activeTracking()
    {
        return $this->hasOne(TaskTrackingTime::class)
            ->whereNotNull('start_at')
            ->whereNull('stop_at')
            ->latest();
    }

    public function updateTotalTimeWorked(): void
    {
        $totalSeconds = $this->trackingTimes()
            ->completed()
            ->get()
            ->sum('duration_in_seconds');

        $totalHours = round($totalSeconds / 3600, 2);

        $this->update(['total_time_worked' => $totalHours]);
    }

    // Total em segundos (inclui sessão ativa)
    public function getTotalSpentSecondsAttribute(): int
    {
        return (int) $this->trackingTimes()
            ->selectRaw('COALESCE(SUM(TIMESTAMPDIFF(SECOND, start_at, COALESCE(stop_at, NOW()))), 0) AS seconds')
            ->value('seconds');
    }

    // Total formatado HH:MM (sem segundos para ficar compacto no card)
    public function getTotalSpentFormattedAttribute(): string
    {
        $s = $this->total_spent_seconds;
        $h = intdiv($s, 3600);
        $m = intdiv($s % 3600, 60);

        return sprintf('%02d:%02d', $h, $m);
    }
}
