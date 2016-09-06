<?php

namespace Lockd\Repositories;

use Lockd\Contracts\Repositories\PasswordRepository;
use Lockd\Models\Folder;
use Lockd\Models\Password;

class DefaultPasswordRepository implements PasswordRepository
{
    public function find(array $parameters = [])
    {
        return empty($parameters)
            ? Password::orderBy('id', 'ASC')->get()
            : Password::where($parameters)->orderBy('id', 'ASC')->get();
    }

    public function findOneById($id)
    {
        return Password::find($id);
    }

    public function findPasswordsInFolder(Folder $folder)
    {
        return $folder->passwords()->get();
    }

    public function findFolder(Password $password)
    {
        return $password->folder;
    }

    public function count()
    {
        return Password::count();
    }

    public function countPasswordsInFolder(Folder $folder)
    {
        return $folder->passwords()->count();
    }
}