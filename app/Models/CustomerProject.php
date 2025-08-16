<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'project_id',
        'start_date',
        'end_date',
        'status',
        'description',
        'tenant_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

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
