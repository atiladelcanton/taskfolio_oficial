<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    /** @use HasFactory<\Database\Factories\CollaboratorFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'document',
        'cellphone',
        'address',
        'payment_method',
        'pix_key',
        'bb_account',
        'bb_agency',
        'payment_day',
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => 'string',
            'payment_day' => 'integer',
        ];
    }

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_collaborators')
            ->withPivot('collaborator_value')
            ->withTimestamps();
    }

    public function monthlyPayments()
    {
        return $this->hasMany(MonthlyPayment::class);
    }

    // Scopes
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByPaymentDay($query, $day)
    {
        return $query->where('payment_day', $day);
    }
}
