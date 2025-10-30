<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPayment extends Model
{
    protected $fillable = [
        'project_id',
        'payment_day',
        'payment_type',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
