<?php

namespace Lockd\Services;
use Lockd\Models\Group;
use Lockd\Models\User;

/**
 * Class GroupManager
 *
 * @package Lockd\Services
 * @author Iain Earl <synapse791@gmail.com>
 */
class GroupManager extends BaseService
{
    /**
     * Creates a new Group
     *
     * @param string $name
     * @return bool|Group
     */
    public function create($name)
    {
        if (empty($name))
            return $this->setBadRequestError('Please provide a name');

        $group = new Group();

        $group->name = $name;

        if (!$this->saveEntity($group, 'A group already exists with that name!'))
            return false;

        return $group;
    }

    /**
     * Updates a group based on the provided data
     *
     * @param Group $group
     * @param array $data
     * @return bool|Group
     */
    public function update(Group $group, $data = [])
    {
        if (empty($data))
            return $group;

        if (isset($data['name']) && !empty($data['name']))
            $group->name = $data['name'];

        if (!$this->saveEntity($group))
            return false;

        return $group;
    }

    /**
     * Adds the provided user to the provided group
     *
     * @param User $user
     * @param Group $group
     * @return bool
     */
    public function addUserToGroup(User $user, Group $group)
    {
         return $this->attachEntities($group->users(), $user);
    }

    /**
     * Removes the provided user from the provided group
     *
     * @param User $user
     * @param Group $group
     * @return bool
     */
    public function removeUserFromGroup(User $user, Group $group)
    {
        return $this->detachEntities($group->users(), $user);
    }
}