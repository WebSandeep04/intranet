<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'tenant_id'
    ];

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function customerProjects()
    {
        return $this->hasMany(CustomerProject::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
