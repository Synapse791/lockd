<?php

namespace Lockd\Contracts\Repositories;
use Illuminate\Database\Eloquent\Collection;
use Lockd\Models\Folder;
use Lockd\Models\User;

/**
 * Interface FolderRepository
 *
 * @package Lockd\Contracts\Repositories
 * @author Iain Earl <synapse791@gmail.com>
 */
interface FolderRepository
{
    /**
     * Return folders that match the specified parameters
     *
     * @param array $parameters
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function find(array $parameters = []);

    /**
     * Find a single folder by it's ID
     *
     * @param int $id
     * @return Folder|null
     */
    public function findOneById($id);

    /**
     * Find all folders contained in the provided folder
     *
     * @param Folder $folder
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function findSubFolders(Folder $folder);

    /**
     * Finds all sub folders a user has access to
     *
     * @param User $user
     * @param Folder $folder
     * @return Collection
     */
    public function findUsersSubFolders(User $user, Folder $folder);

    /**
     * Finds the parent folder of the provided folder
     *
     * @param Folder $folder
     * @return \Lockd\Models\Folder|null
     */
    public function findParent(Folder $folder);

    /**
     * Counts all the folders
     *
     * @return int
     */
    public function count();

    /**
     * Counts the folders inside the provided folder
     *
     * @param Folder $folder
     * @return int
     */
    public function countSubFolders(Folder $folder);
}