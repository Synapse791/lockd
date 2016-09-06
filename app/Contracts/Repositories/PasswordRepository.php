<?php

namespace Lockd\Contracts\Repositories;
use Lockd\Models\Folder;
use Lockd\Models\Password;

/**
 * Interface PasswordRepository
 *
 * @package Lockd\Contracts\Repositories
 * @author Iain Earl <synapse791@gmail.com>
 */
interface PasswordRepository
{
    /**
     * Return passwords that match the specified parameters
     *
     * @param array $parameters
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function find(array $parameters = []);

    /**
     * Find a single password by it's ID
     *
     * @param int $id
     * @return Password|null
     */
    public function findOneById($id);

    /**
     * Find all passwords contained in the provided folder
     *
     * @param Folder $folder
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function findPasswordsInFolder(Folder $folder);

    /**
     * Finds the folder (parent) of the provided password
     *
     * @param Password $password
     * @return Folder|null
     */
    public function findFolder(Password $password);

    /**
     * Counts all the passwords
     *
     * @return int
     */
    public function count();

    /**
     * Counts the passwords inside the provided folder
     *
     * @param Folder $folder
     * @return int
     */
    public function countPasswordsInFolder(Folder $folder);
}