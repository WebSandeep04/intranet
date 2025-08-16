<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProjectModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_project_id',
        'module_id',
        'status',
        'start_date',
        'end_date',
        'description',
        'tenant_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function customerProject()
    {
        return $this->belongsTo(CustomerProject::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
