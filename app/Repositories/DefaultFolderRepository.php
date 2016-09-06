<?php

namespace Lockd\Repositories;

use Lockd\Contracts\Repositories\FolderRepository;
use Lockd\Models\Folder;

class DefaultFolderRepository implements FolderRepository
{
    public function find(array $parameters = [])
    {
        return empty($parameters)
            ? Folder::orderBy('id', 'ASC')->get()
            : Folder::where($parameters)->orderBy('id', 'ASC')->get();
    }

    public function findOneById($id)
    {
        return Folder::find($id);
    }

    public function findSubFolders(Folder $folder)
    {
        return $folder->folders()->get();
    }

    public function findParent(Folder $folder)
    {
        return $folder->parent;
    }

    public function count()
    {
        return Folder::count();
    }

    public function countSubFolders(Folder $folder)
    {
        return $folder->folders()->count();
    }
}