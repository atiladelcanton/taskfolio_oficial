<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SprintStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    /** @use HasFactory<\Database\Factories\SprintFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'start_at',
        'end_at',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'status' => 'string',
        ];
    }

    // Relacionamentos
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Acessors
    public function getIsActiveAttribute()
    {
        return $this->status === SprintStatusEnum::ACTIVE;
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === SprintStatusEnum::COMPLETED;
    }

    public function getDurationInDaysAttribute()
    {
        if ($this->start_at && $this->end_at) {
            return $this->start_at->diffInDays($this->end_at);
        }

        return null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', SprintStatusEnum::ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', SprintStatusEnum::COMPLETED);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public static function hasOverlappingSprintInProject(
        int $projectId,
        string $startDate,
        string $endDate,
        ?int $excludeSprintId = null
    ): ?self {
        $query = self::where('project_id', $projectId)
            ->where(function ($query) use ($startDate, $endDate) {
                // Verifica todas as possibilidades de sobreposição
                $query->where(function ($q) use ($startDate, $endDate) {
                    // Nova sprint começa durante sprint existente
                    $q->whereBetween('start_at', [$startDate, $endDate]);
                })
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        // Nova sprint termina durante sprint existente
                        $q->whereBetween('end_at', [$startDate, $endDate]);
                    })
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        // Nova sprint engloba sprint existente completamente
                        $q->where('start_at', '>=', $startDate)
                            ->where('end_at', '<=', $endDate);
                    })
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        // Sprint existente engloba nova sprint completamente
                        $q->where('start_at', '<=', $startDate)
                            ->where('end_at', '>=', $endDate);
                    });
            });

        if ($excludeSprintId) {
            $query->where('id', '!=', $excludeSprintId);
        }

        return $query->first();
    }
}
