<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    protected $fillable = [
        'remark_date',
        'remark',
        'sales_remark_id',
        'tenant_id'
    ];

    protected $casts = [
        'remark_date' => 'date'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function salesRecord()
    {
        return $this->belongsTo(SalesRecord::class, 'sales_remark_id');
    }
}
