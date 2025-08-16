<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesStatus extends Model
{
    protected $table = 'sales_status';
    protected $fillable = ['status_name', 'tenant_id'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class, 'status_id');
    }
}
