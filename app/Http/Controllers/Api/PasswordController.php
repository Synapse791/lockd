<?php

namespace Lockd\Http\Controllers\Api;

use Lockd\Contracts\Repositories\FolderRepository;
use Lockd\Contracts\Repositories\PasswordRepository;
use Lockd\Hydrators\PasswordHydrator;

class PasswordController extends BaseApiController
{
    /** @var PasswordRepository */
    private $repository;

    /** @var FolderRepository */
    private $folderRepository;

    /** @var PasswordHydrator */
    private $hydrator;

    /**
     * PasswordController constructor
     *
     * @param PasswordRepository $repository
     * @param FolderRepository $folderRepository
     * @param PasswordHydrator $hydrator
     */
    public function __construct(
        PasswordRepository $repository,
        FolderRepository $folderRepository,
        PasswordHydrator $hydrator
    ) {
        $this->repository = $repository;
        $this->folderRepository = $folderRepository;
        $this->hydrator = $hydrator;
    }

    /**
     * Returns all passwords in the given folder
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFromFolder($id)
    {
        $folder = $this->folderRepository->findOneById($id);

        if (!$folder)
            return $this->jsonNotFound("Folder with ID {$id} not found");

        $passwords = $this->repository->findPasswordsInFolder($folder);

        $data = [];

        foreach ($passwords as $password)
            $data[] = $this->hydrator->hydrate($password);

        return $this->jsonResponse($data);
    }
}