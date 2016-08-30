<?php

namespace Lockd\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'au_group';

    protected $fillable = ['name'];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'au_user_groups');
    }
}