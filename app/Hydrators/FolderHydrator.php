<?php

namespace Lockd\Hydrators;

use Lockd\Contracts\Repositories\FolderRepository;
use Lockd\Contracts\Repositories\PasswordRepository;
use Lockd\Models\Folder;

/**
 * Class FolderHydrator
 *
 * @package Lockd\Hydrators
 * @author Iain Earl <synapse791@gmail.com>
 */
class FolderHydrator
{
    /** @var FolderRepository */
    private $folderRepository;

    /** @var PasswordRepository */
    private $passwordRepository;

    /**
     * FolderHydrator constructor
     *
     * @param FolderRepository $folderRepository
     * @param PasswordRepository $passwordRepository
     */
    public function __construct(FolderRepository $folderRepository, PasswordRepository $passwordRepository)
    {
        $this->folderRepository = $folderRepository;
        $this->passwordRepository = $passwordRepository;
    }

    /**
     * Gather all details required for a folder response
     *
     * @param Folder $folder
     * @return array
     */
    public function hydrate(Folder $folder)
    {
        $response = [];

        $response['id'] = $folder->id;
        $response['name'] = $folder->name;
        $response['parent_id'] = $folder->parent->id;
        $response['folder_count'] = $this->folderRepository->countSubFolders($folder);
        $response['password_count'] = $this->passwordRepository->countPasswordsInFolder($folder);

        return $response;
    }
}