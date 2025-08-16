<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'project_id',
        'tenant_id'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function customerProjectModules()
    {
        return $this->hasMany(CustomerProjectModule::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
