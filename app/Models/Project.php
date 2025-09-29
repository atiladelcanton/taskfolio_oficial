<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'payment_type',
        'payment_method',
        'payment_day',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'payment_type' => 'string',
            'payment_method' => 'string',
            'status' => 'string',
        ];
    }

    // Relacionamentos
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function collaborators()
    {
        return $this->belongsToMany(Collaborator::class, 'project_collaborators')
            ->withPivot('collaborator_value')
            ->withTimestamps();
    }

    public function sprints()
    {
        return $this->hasMany(Sprint::class);
    }

    public function monthlyPayments()
    {
        return $this->hasMany(MonthlyPayment::class);
    }

    public function clientPayments()
    {
        return $this->hasMany(ClientPayment::class);
    }

    // Acessors
    public function getIsActiveAttribute()
    {
        return in_array($this->status, ['pending', 'doing']);
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === 'finished';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'doing']);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentType($query, $type)
    {
        return $query->where('payment_type', $type);
    }
}
