<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $fillable = ['tenant_name', 'tenant_code'];

    protected static function booted()
    {
        static::creating(function ($tenant) {
            if (empty($tenant->tenant_code)) {
                $tenant->tenant_code = 'TEN-' . strtoupper(Str::random(6));
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class);
    }
}
