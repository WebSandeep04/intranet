<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prospectus extends Model
{
    use HasFactory;

    protected $table = 'prospectuses';

    protected $fillable = [
        'prospectus_name',
        'contact_person',
        'contact_number',
        'address',
        'state_id',
        'city_id',
        'email',
        'business_type_id',
        'tenant_id'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
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

    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class);
    }
}
