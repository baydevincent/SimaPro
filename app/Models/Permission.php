<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];

    /**
     * Many-to-Many relations with Role
     */
    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class);
    }
}