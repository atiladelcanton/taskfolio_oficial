<?php

namespace App\Models;

use Database\Factories\ProjectCollaboratorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCollaborator extends Model
{
    /** @use HasFactory<ProjectCollaboratorFactory> */
    use HasFactory;

    public $incrementing = true;

    protected $table = 'project_collaborators';

    protected $fillable = [
        'project_id',
        'collaborator_id',
        'collaborator_value',
        'payment_type'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relacionamentos

    public function collaborator()
    {
        return $this->belongsTo(Collaborator::class);
    }

    protected function casts(): array
    {
        return [
            'collaborator_value' => 'decimal:2',
        ];
    }
}
