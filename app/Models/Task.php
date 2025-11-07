<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\{TypeTaskEnum};
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
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
        'deadline',
    ];

    public function sprint()
    {
        return $this->belongsTo(Sprint::class);
    }

    // Relacionamentos

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany|self
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(Collaborator::class, 'collaborator_id');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function evidences()
    {
        return $this->hasMany(TaskEvidence::class);
    }

    public function getIsEpicAttribute(): bool
    {
        return $this->type_task === TypeTaskEnum::EPIC;
    }

    public function getIsBugAttribute()
    {
        return $this->type_task === TypeTaskEnum::BUG;
    }

    // Acessors

    public function getIsTaskAttribute()
    {
        return $this->type_task === TypeTaskEnum::TASK;
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type_task', $type);
    }

    public function scopeEpics($query)
    {
        return $query->where('type_task', TypeTaskEnum::EPIC);
    }

    // Scopes

    public function scopeBugs($query)
    {
        return $query->where('type_task', TypeTaskEnum::BUG);
    }

    public function scopeTasks($query)
    {
        return $query->where('type_task', TypeTaskEnum::TASK);
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

    public function trackingTimes()
    {
        return $this->hasMany(TaskTrackingTime::class);
    }

    public function getTotalSpentSecondsAttribute(): int
    {
        return (int) $this->trackingTimes()
            ->selectRaw('COALESCE(SUM(TIMESTAMPDIFF(SECOND, start_at, COALESCE(stop_at, NOW()))), 0) AS seconds')
            ->value('seconds');
    }

    // Total em segundos (inclui sessão ativa)

    public function getTotalSpentFormattedAttribute(): string
    {
        $s = $this->total_spent_seconds;
        $h = intdiv($s, 3600);
        $m = intdiv($s % 3600, 60);

        return sprintf('%02d:%02d', $h, $m);
    }

    // Total formatado HH:MM (sem segundos para ficar compacto no card)

    protected function casts(): array
    {
        return [
            'type_task' => 'string',
            'priority' => 'string',
            'deadline' => 'date',
        ];
    }
}
