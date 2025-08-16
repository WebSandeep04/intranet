<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesProduct extends Model
{
    protected $table = 'sales_products';
    protected $fillable = ['product_name', 'tenant_id'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class, 'products_id');
    }
}
