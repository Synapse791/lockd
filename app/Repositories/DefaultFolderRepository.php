<?php

namespace Lockd\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Lockd\Contracts\Repositories\FolderRepository;
use Lockd\Models\Folder;
use Lockd\Models\User;
use Lockd\Services\PermissionManager;

class DefaultFolderRepository implements FolderRepository
{
    /** @var PermissionManager */
    private $permissionManager;

    /**
     * DefaultFolderRepository constructor
     *
     * @param PermissionManager $permissionManager
     */
    public function __construct(PermissionManager $permissionManager)
    {
        $this->permissionManager = $permissionManager;
    }

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

    public function findUsersSubFolders(User $user, Folder $folder)
    {
        $folderCollection = new Collection();

        $subFolders = $folder->folders;

        foreach ($subFolders as $subFolder)
            if ($this->permissionManager->checkUserHasAccessToFolder($user, $subFolder))
                $folderCollection->add($subFolder);

        return $folderCollection;
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