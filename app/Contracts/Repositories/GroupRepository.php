<?php

namespace Lockd\Contracts\Repositories;
use Illuminate\Database\Eloquent\Collection;
use Lockd\Models\Group;
use Lockd\Models\User;

/**
 * Interface GroupRepository
 *
 * @package Lockd\Contracts\Repositories
 * @author Iain Earl <synapse791@gmail.com>
 */
interface GroupRepository
{
    /**
     * Find a set of groups based on provided parameters
     *
     * @param array $parameters
     * @return Collection
     */
    public function find(array $parameters = []);

    /**
     * Finds a single group by it's ID
     *
     * @param int $id
     * @return Group|null
     */
    public function findOneById($id);

    /**
     * Finds a single group by it's name
     *
     * @param string $name
     * @return Group|null
     */
    public function findOneByName($name);

    /**
     * Counts the groups in the database
     *
     * @return int
     */
    public function count();
}