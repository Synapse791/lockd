<?php

namespace Lockd\Repositories;

use Lockd\Contracts\Repositories\UserRepository;
use Lockd\Models\User;

/**
 * Class DefaultUserRepository
 *
 * @package Lockd\Repositories
 * @author Iain Earl <synapse791@gmail.com>
 */
class DefaultUserRepository implements UserRepository
{
    public function find(array $parameters = [])
    {
        return empty($parameters)
            ? User::orderBy('id', 'ASC')->get()
            : User::where($parameters)->orderBy('id', 'ASC')->get();
    }

    public function findOneById($id)
    {
        return User::find($id);
    }

    public function findOneByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function count()
    {
        return User::count();
    }
}