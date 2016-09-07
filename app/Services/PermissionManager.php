<?php

namespace Lockd\Services;

use Lockd\Models\Folder;
use Lockd\Models\Group;
use Lockd\Models\Password;
use Lockd\Models\User;

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

    /**
     * Checks if a user is in a group
     *
     * @param User $user
     * @param Group $group
     * @return bool
     */
    public function checkUserIsInGroup(User $user, Group $group)
    {
        $userGroups = $user->groups;

        return $userGroups->contains($group);
    }

    /**
     * Checks if a user has access to a password
     *
     * @param User $user
     * @param Password $password
     * @return bool
     */
    public function checkUserHasAccessToPassword(User $user, Password $password)
    {
        $folder = $password->folder;

        return $this->checkUserHasAccessToFolder($user, $folder);
    }

    /**
     * Check if a user has access to a folder
     *
     * @param User $user
     * @param Folder $folder
     * @return bool
     */
    public function checkUserHasAccessToFolder(User $user, Folder $folder)
    {
        $folderGroups = $folder->groups;

        foreach ($user->groups as $group)
            if ($folderGroups->contains($group))
                return true;

        return false;
    }
}