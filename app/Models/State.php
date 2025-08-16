<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['state_name'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class);
    }

    public function prospectuses()
    {
        return $this->hasMany(Prospectus::class);
    }
}
