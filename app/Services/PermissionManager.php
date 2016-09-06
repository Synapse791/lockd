<?php

namespace Lockd\Services;

use Lockd\Models\Folder;
use Lockd\Models\Group;

/**
 * Class PermissionManager
 *
 * @package Lockd\Services
 * @author Iain Earl <synapse791@gmail.com>
 */
class PermissionManager extends BaseService
{
    /**
     * Adds a folder to a group
     *
     * @param Folder $folder
     * @param Group $group
     * @return bool
     */
    public function addFolderToGroup(Folder $folder, Group $group)
    {
        return $this->attachEntities($group->folders(), $folder);
    }

    /**
     * Removes a folder from a group
     *
     * @param Folder $folder
     * @param Group $group
     * @return bool
     */
    public function removeFolderFromGroup(Folder $folder, Group $group)
    {
        return $this->detachEntities($group->folders(), $folder);
    }
}