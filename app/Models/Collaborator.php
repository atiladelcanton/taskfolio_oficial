<?php

namespace App\Models;

use Database\Factories\CollaboratorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collaborator extends Model
{
    /** @use HasFactory<CollaboratorFactory> */
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    // Relacionamentos

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_collaborators')
            ->withPivot('collaborator_value', 'payment_type')
            ->withTimestamps();
    }

    public function monthlyPayments()
    {
        return $this->hasMany(MonthlyPayment::class);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // Scopes

    public function scopeByPaymentDay($query, $day)
    {
        return $query->where('payment_day', $day);
    }

    protected function casts(): array
    {
        return [
            'payment_method' => 'string',
            'payment_day' => 'integer',
        ];
    }
}
