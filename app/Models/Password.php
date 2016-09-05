<?php

namespace Lockd\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Password
 *
 * @package Lockd\Models
 * @author Iain Earl <synapse791@gmail.com>
 */
class Password extends Model
{
    protected $table = 'da_password';

    protected $fillable = [
        'name',
        'url',
        'user',
        'password',
    ];

    /**
     * Relationship for the containing folder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
