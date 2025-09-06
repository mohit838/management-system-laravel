<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Module extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'description',
        'is_active'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    // Relationships
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
