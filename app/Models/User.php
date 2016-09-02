<?php

namespace Lockd\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'au_user';

    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'au_user_groups');
    }
}
