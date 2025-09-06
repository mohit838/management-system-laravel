<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;


class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    protected $fillable = [
        'id',
        'full_name',
        'email',
        'phone',
        'password',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->roles->map->permissions->flatten()->unique('id');
    }

    // Mutators
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Boot method to assign default role
    protected static function booted()
    {
        static::created(function ($user) {
            $defaultRole = Role::where('slug', 'user')->first();
            if ($defaultRole) {
                $user->roles()->attach($defaultRole->id);
            }
        });
    }
}
