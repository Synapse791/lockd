<?php

namespace Lockd\Services;
use Lockd\Models\Folder;

/**
 * Class FolderManager
 *
 * @package Lockd\Services
 * @author Iain Earl <synapse791@gmail.com>
 */
class FolderManager extends BaseService
{
    /**
     * Create a new Folder inside the provided parent Folder
     *
     * @param Folder $parentFolder
     * @param $name
     * @return bool|Folder
     */
    public function create(Folder $parentFolder, $name)
    {
        if (empty($name))
            return $this->setBadRequestError('Please provide a name');

        $folder = new Folder();

        $folder->name = $name;

        if (!$this->associateEntities($folder->parent(), $parentFolder))
            return false;

        if (!$this->saveEntity($folder))
            return false;

        return $folder;
    }

    /**
     * Updates a Folder based on the provided data
     *
     * @param Folder $folder
     * @param array $data
     * @return bool|Folder
     */
    public function update(Folder $folder, $data = [])
    {
        if (empty($data))
            return $folder;

        if (isset($data['name']) && !empty($data['name']))
            $folder->name = $data['name'];

        if (isset($data['parentFolder']) && $data['parentFolder'] instanceof Folder)
            if (!$this->associateEntities($folder->parent(), $data['parentFolder']))
                return false;

        if (!$this->saveEntity($folder))
            return false;

        return $folder;
    }
}