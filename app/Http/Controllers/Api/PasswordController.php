<?php

namespace Lockd\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Factory;
use Lockd\Contracts\Repositories\FolderRepository;
use Lockd\Contracts\Repositories\PasswordRepository;
use Lockd\Hydrators\PasswordHydrator;
use Lockd\Services\PasswordManager;
use Lockd\Services\PermissionManager;

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

    /** @var PermissionManager */
    private $permissionManager;

    /**
     * PasswordController constructor
     *
     * @param PasswordRepository $repository
     * @param FolderRepository $folderRepository
     * @param PasswordHydrator $hydrator
     * @param PasswordManager $manager
     * @param PermissionManager $permissionManager
     */
    public function __construct(
        PasswordRepository $repository,
        FolderRepository $folderRepository,
        PasswordHydrator $hydrator,
        PasswordManager $manager,
        PermissionManager $permissionManager
    ) {
        $this->repository = $repository;
        $this->folderRepository = $folderRepository;
        $this->hydrator = $hydrator;
        $this->manager = $manager;
        $this->permissionManager = $permissionManager;
    }

    /**
     * Returns all passwords in the given folder
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFromFolder(Request $request, $id)
    {
        $folder = $this->folderRepository->findOneById($id);

        if (!$folder)
            return $this->jsonNotFound("Folder with ID {$id} not found");

        if (!$this->permissionManager->checkUserHasAccessToFolder($request->user(), $folder))
            return $this->jsonUnauthorized("You do not have access to that folder");

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
        $folder = $this->folderRepository->findOneById($id);

        if (!$folder)
            return $this->jsonNotFound("Folder with ID {$id} not found");

        if (!$this->permissionManager->checkUserHasAccessToFolder($request->user(), $folder))
            return $this->jsonUnauthorized("You do not have access to that folder");

        $validation = $factory->make($request->input(), [
            'name' => 'required|string',
            'url' => 'string',
            'user' => 'string',
            'password' => 'required|confirmed',
        ]);

        if ($validation->fails())
            return $this->jsonValidationBadRequest($validation);

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

    /**
     * Updates a password with the provided details
     *
     * @param Factory $factory
     * @param Request $request
     * @param int $id
     * @param int $passwordId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Factory $factory, Request $request, $id, $passwordId)
    {
        $folder = $this->folderRepository->findOneById($id);

        if (!$folder)
            return $this->jsonNotFound("Folder with ID {$id} not found");

        if (!$this->permissionManager->checkUserHasAccessToFolder($request->user(), $folder))
            return $this->jsonUnauthorized("You do not have access to that folder");

        $password = $this->repository->find([
            ['id', $passwordId],
            ['folder_id', $id],
        ])->first();

        if (!$password)
            return $this->jsonNotFound("Password with ID {$passwordId} not found");

        $validation = $factory->make($request->input(), [
            'name' => 'string',
            'url' => 'string',
            'user' => 'string',
            'password' => 'string|confirmed',
            'folder_id' => 'integer',
        ]);

        if ($validation->fails())
            return $this->jsonValidationBadRequest($validation);

        $data = $request->input();

        if ($request->input('folder_id', false)) {
            $data['folder'] = $this->folderRepository->findOneById($request->input('folder_id'));
            if (!$data['folder'])
                return $this->jsonNotFound("Folder with ID {$request->input('folder_id')} not found");
            unset($data['folder_id']);
        }

        if (!$this->manager->update($password, $data))
            return $this->jsonResponseFromService($this->manager);

        return $this->jsonResponse('Password updated successfully', 200);
    }
}