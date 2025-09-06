<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Permission extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'module_id',
        'name',
        'slug',
        'is_active'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    // Relationships
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }
}
