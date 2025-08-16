<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesBusinessType extends Model
{
    protected $table = 'sales_business_types';
    protected $fillable = ['business_name', 'tenant_id'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class, 'business_type_id');
    }

    public function prospectuses()
    {
        return $this->hasMany(Prospectus::class, 'business_type_id');
    }
}
