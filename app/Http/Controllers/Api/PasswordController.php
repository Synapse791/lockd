<?php

namespace Lockd\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Factory;
use Lockd\Contracts\Repositories\FolderRepository;
use Lockd\Contracts\Repositories\PasswordRepository;
use Lockd\Hydrators\PasswordHydrator;
use Lockd\Services\PasswordManager;

class PasswordController extends BaseApiController
{
    /** @var PasswordRepository */
    private $repository;

    /** @var FolderRepository */
    private $folderRepository;

    /** @var PasswordHydrator */
    private $hydrator;

    /** @var PasswordManager */
    private $manager;

    /**
     * PasswordController constructor
     *
     * @param PasswordRepository $repository
     * @param FolderRepository $folderRepository
     * @param PasswordHydrator $hydrator
     * @param PasswordManager $manager
     */
    public function __construct(
        PasswordRepository $repository,
        FolderRepository $folderRepository,
        PasswordHydrator $hydrator,
        PasswordManager $manager
    ) {
        $this->repository = $repository;
        $this->folderRepository = $folderRepository;
        $this->hydrator = $hydrator;
        $this->manager = $manager;
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

    /**
     * Creates a new password in the provided folder
     *
     * @param Factory $factory
     * @param Request $request
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Factory $factory, Request $request, $id)
    {
        $validation = $factory->make($request->input(), [
            'name' => 'required|string',
            'url' => 'string',
            'user' => 'string',
            'password' => 'required|confirmed',
        ]);

        if ($validation->fails())
            return $this->jsonValidationBadRequest($validation);

        $folder = $this->folderRepository->findOneById($id);

        if (!$folder)
            return $this->jsonNotFound("Folder with ID {$id} not found");

        if (!$this->manager->create(
            $folder,
            $request->input('name'),
            $request->input('password'),
            $request->input('url'),
            $request->input('user')
        ))
            return $this->jsonResponseFromService($this->manager);

        return $this->jsonResponse('Password created successfully', 201);
    }
}