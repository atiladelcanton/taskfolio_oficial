<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyPayment extends Model
{
    /** @use HasFactory<\Database\Factories\MonthlyPaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'collaborator_id',
        'project_id',
        'month_year',
        'amount_due',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount_due' => 'decimal:2',
            'status' => 'string',
        ];
    }

    // Relacionamentos
    public function collaborator()
    {
        return $this->belongsTo(Collaborator::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Acessors
    public function getIsPaidAttribute()
    {
        return $this->status === 'paid';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeByMonth($query, $monthYear)
    {
        return $query->where('month_year', $monthYear);
    }
}
