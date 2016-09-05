<?php

namespace Lockd\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Folder
 *
 * @package Lockd\Models
 * @author Iain Earl <synapse791@gmail.com>
 */
class Folder extends Model
{
    protected $table = 'da_folder';

    protected $fillable = [ 'name' ];

    public $timestamps = false;

    /**
     * Relationship to the containing folder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    /**
     * Relationship for the contained folders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function folders()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    /**
     * Relationship for the contained passwords
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function passwords()
    {
        return $this->hasMany(Password::class, 'folder_id');
    }

    /**
     * Relationship for the Groups
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'au_group_folders');
    }
}
