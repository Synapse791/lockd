<?php

namespace Lockd\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Group
 *
 * @package Lockd\Models
 * @author Iain Earl <synapse791@gmail.com>
 */
class Group extends Model
{
    protected $table = 'au_group';

    protected $fillable = ['name'];

    public $timestamps = false;

    /**
     * Relationship with Users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'au_user_groups');
    }

    /**
     * Relationship with Folders
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function folders()
    {
        return $this->belongsToMany(Folder::class, 'au_group_folders');
    }
}