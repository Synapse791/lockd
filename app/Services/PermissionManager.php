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
     * Grant access for a group to a folder
     *
     * @param Folder $folder
     * @param Group $group
     * @return bool
     */
    public function grantGroupAccessToFolder(Group $group, Folder $folder)
    {
        if (!$this->attachEntities($group->folders(), $folder)) {
            if (str_contains($this->getErrorDescription(), 'Duplicate entry'))
                return true;
            return false;
        }

        return true;
    }

    /**
     * Removes access for a group from a folder
     *
     * @param Group $group
     * @param Folder $folder
     * @return bool
     */
    public function removeGroupAccessFromFolder(Group $group, Folder $folder)
    {
        return $this->detachEntities($group->folders(), $folder);
    }
}