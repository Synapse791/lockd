<?php

namespace Lockd\Contracts\Repositories;

/**
 * Interface UserRepository
 *
 * @package Lockd\Contracts\Repositories
 * @author Iain Earl <synapse791@gmail.com>
 */
interface UserRepository
{
    /**
     * Return users that match the specified parameters
     *
     * @param array $parameters
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function find(array $parameters = []);

    /**
     * Find a single user by their ID
     *
     * @param $id
     * @return \Lockd\Models\User|null
     */
    public function findOneById($id);

    /**
     * Find a single user by their email
     *
     * @param $email
     * @return \Lockd\Models\User|null
     */
    public function findOneByEmail($email);

    /**
     * Counts the users in the database
     *
     * @return int
     */
    public function count();
}