<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'company_name',
        'tenant_id'
    ];

    public function customerProjects()
    {
        return $this->hasMany(CustomerProject::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
