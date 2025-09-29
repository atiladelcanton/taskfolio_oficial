<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTrackingTime extends Model
{
    /** @use HasFactory<\Database\Factories\TaskTrackingTimeFactory> */
    use HasFactory;

    protected $fillable = [
        'task_id',
        'collaborator_id',
        'start_at',
        'stop_at',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'stop_at' => 'datetime',
        ];
    }

    // Relacionamentos
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function collaborator()
    {
        return $this->belongsTo(User::class, 'collaborator_id');
    }

    // Acessors
    public function getDurationInSecondsAttribute()
    {
        if ($this->start_at && $this->stop_at) {
            return $this->start_at->diffInSeconds($this->stop_at);
        }

        return null;
    }

    public function getDurationInMinutesAttribute()
    {
        if ($this->start_at && $this->stop_at) {
            return $this->start_at->diffInMinutes($this->stop_at);
        }

        return null;
    }

    public function getDurationInHoursAttribute()
    {
        if ($this->start_at && $this->stop_at) {
            return round($this->start_at->diffInSeconds($this->stop_at) / 3600, 2);
        }

        return null;
    }

    public function getIsActiveAttribute()
    {
        return $this->start_at && ! $this->stop_at;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotNull('start_at')->whereNull('stop_at');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('start_at')->whereNotNull('stop_at');
    }

    public function scopeByPeriod($query, $start, $end)
    {
        return $query->whereBetween('start_at', [$start, $end]);
    }
}
