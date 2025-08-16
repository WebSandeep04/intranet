<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;



class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'tenant_id',
        'is_worklog',
        'is_manager'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
{
    return $this->belongsTo(Role::class);
}

public function tenant()
{
    return $this->belongsTo(Tenant::class);
}

public function salesRecords()
{
    return $this->hasMany(SalesRecord::class);
}

public function worklogs()
{
    return $this->hasMany(Worklog::class);
}

public function manager()
{
    return $this->belongsTo(User::class, 'is_manager');
}

public function subordinates()
{
    return $this->hasMany(User::class, 'is_manager');
}

public function attendances()
{
    return $this->hasMany(Attendance::class);
}

public function leaves()
{
    return $this->hasMany(Leave::class);
}
}
