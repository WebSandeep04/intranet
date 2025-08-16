<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'working_hours',
        'description',
        'tenant_id'
    ];

    public function worklogs()
    {
        return $this->hasMany(Worklog::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
