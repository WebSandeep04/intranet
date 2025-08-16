<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worklog extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_date',
        'entry_type_id',
        'customer_id',
        'project_id',
        'module_id',
        'hours',
        'minutes',
        'description',
        'status',
        'user_id',
        'tenant_id'
    ];

    protected $casts = [
        'work_date' => 'date'
    ];

    public function entryType()
    {
        return $this->belongsTo(EntryType::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Get total minutes for this worklog entry
    public function getTotalMinutesAttribute()
    {
        return ($this->hours * 60) + $this->minutes;
    }

    // Get formatted time
    public function getFormattedTimeAttribute()
    {
        return sprintf('%02d:%02d', $this->hours, $this->minutes);
    }
}
