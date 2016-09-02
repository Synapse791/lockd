<?php

namespace Lockd\Repositories;

use Lockd\Contracts\Repositories\GroupRepository;
use Lockd\Models\Group;

/**
 * Class DefaultGroupRepository
 *
 * @package Lockd\Repositories
 * @author Iain Earl <synapse791@gmail.com>
 */
class DefaultGroupRepository implements GroupRepository
{
    public function find(array $parameters = [])
    {
        return empty($parameters)
            ? Group::orderBy('id', 'ASC')->get()
            : Group::where($parameters)->orderBy('id', 'ASC')->get();
    }

    public function findOneById($id)
    {
        return Group::find($id);
    }

    public function findOneByName($name)
    {
        return Group::where('name', $name)->first();
    }

    public function count()
    {
        return Group::count();
    }
}