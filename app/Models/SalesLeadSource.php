<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesLeadSource extends Model
{
    protected $table = 'sales_lead_sources';
    protected $fillable = ['source_name', 'tenant_id'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class, 'lead_source_id');
    }
}
