<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesRecord extends Model
{
    protected $table = 'sales_records';
    public $timestamps = false; // Disable automatic timestamps since we have custom columns
    
    protected $fillable = [
        'user_id',
        'leads_name',
        'contact_person',
        'contact_number',
        'address',
        'state_id',
        'city_id',
        'email',
        'business_type_id',
        'lead_source_id',
        'status_id',
        'next_follow_up_date',
        'products_id',
        'prospectus_id',
        'updatedat',
        'update_remark',
        'status_update_remark',
        'status_updatedat',
        'createdat',
        'ticket_value',
        'tenant_id'
    ];

    protected $casts = [
        'next_follow_up_date' => 'date',
        'createdat' => 'date',
        'updatedat' => 'datetime',
        'status_updatedat' => 'datetime',
        'ticket_value' => 'integer'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function businessType()
    {
        return $this->belongsTo(SalesBusinessType::class, 'business_type_id');
    }

    public function leadSource()
    {
        return $this->belongsTo(SalesLeadSource::class, 'lead_source_id');
    }

    public function status()
    {
        return $this->belongsTo(SalesStatus::class, 'status_id');
    }

    public function product()
    {
        return $this->belongsTo(SalesProduct::class, 'products_id');
    }

    public function prospectus()
    {
        return $this->belongsTo(Prospectus::class);
    }

    public function remarks()
    {
        return $this->hasMany(Remark::class, 'sales_remark_id');
    }

    public function latestRemark()
    {
        return $this->hasOne(Remark::class, 'sales_remark_id')
                    ->latest('remark_date');
    }
}
