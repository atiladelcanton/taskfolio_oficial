<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'personal_name',
        'document',
        'email',
        'phone',
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    public function projectsCount()
    {
        return $this->hasMany(Project::class)->count();
    }
    public function clientPayments()
    {
        return $this->hasMany(ClientPayment::class);
    }

    // Scopes
    public function scopeByCompany($query, $companyName)
    {
        return $query->where('company_name', 'like', "%{$companyName}%");
    }

    public function scopeByPersonal($query, $personalName)
    {
        return $query->where('personal_name', 'like', "%{$personalName}%");
    }
}
