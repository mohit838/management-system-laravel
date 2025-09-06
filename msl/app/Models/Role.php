<?php

namespace App\Models;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'description',
        'is_active',
        'is_system',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }
}
