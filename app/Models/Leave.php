<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'leave_type_id',
        'reason',
        'status',
        'tenant_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the user that owns the leave.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the leave type (entry type).
     */
    public function leaveType()
    {
        return $this->belongsTo(EntryType::class, 'leave_type_id');
    }

    /**
     * Get the tenant that owns the leave.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope to get leaves for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get leaves for a specific tenant.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }



    /**
     * Scope to get leaves for a date range.
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
