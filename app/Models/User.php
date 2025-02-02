<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Helpers\AppHelper;
use App\Traits\PermissionsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, PermissionsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'status',
        'position',
        'role_id',
        'branch_id',
        'profile_photo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    public function dept()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function scopeDeptUsers(Builder $query)
    {
        $query->where('role', AppHelper::DEPARTMENT);
    }

    public function character()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function company()
    {
        return $this->hasMany(Company::class, 'sales_user_id', 'id');
    }

    public function events()
    {
        return $this->hasMany(CustomerEvent::class, 'created_by', 'id');
    }
    public function salesleads()
    {
        return $this->hasMany(SalesLead::class, 'created_by', 'id');
    }

    public function ticketAssignedMe()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    public function ticketCreatedByMe()
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    public function ticketCountWithStatus($status = 0)
    {
        return Ticket::where(function ($query) {
            $query->where('user_id', $this->id)
                ->orWhere('created_by', $this->id);
        })
            ->where('status', $status)
            ->count();
    }

    public function scopeWithoutTjt(Builder $query)
    {
        $query->where('role', '!=', 'TJT');
    }
}
