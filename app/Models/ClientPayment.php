<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPayment extends Model
{
    /** @use HasFactory<\Database\Factories\ClientPaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
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
    public function client()
    {
        return $this->belongsTo(Client::class);
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
