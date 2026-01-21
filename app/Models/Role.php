<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];

    /**
     * Many-to-Many relations with User
     */
    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class);
    }

    /**
     * Many-to-Many relations with Permission
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}