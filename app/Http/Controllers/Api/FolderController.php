<?php

namespace Lockd\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Factory;
use Lockd\Contracts\Repositories\FolderRepository;
use Lockd\Services\FolderManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class FolderController
 *
 * @package Lockd\Http\Controllers\Api
 * @author Iain Earl <synapse791@gmail.com>
 */
class FolderController extends BaseApiController
{
    /** @var FolderRepository */
    private $repository;

    /** @var FolderManager */
    private $manager;

    /**
     * FolderController constructor
     *
     * @param FolderRepository $repository
     * @param FolderManager $manager
     */
    public function __construct(FolderRepository $repository, FolderManager $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * Return either a single folder,
     *
     * @param int $id
     * @param string|null $option
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id, $option = null)
    {
        $folder = $this->repository->findOneById($id);

        if (is_null($folder))
            return $this->jsonNotFound("Folder with ID {$id} not found");

        switch ($option) {
            case null:
                $data = $folder;
                break;
            case 'parent':
                $data = $this->repository->findParent($folder);
                break;
            case 'folders':
                $data = $this->repository->findSubFolders($folder);
                break;
            default:
                throw new NotFoundHttpException();
        }

        return $this->jsonResponse($data);
    }

    /**
     * Creates a new folder inside the provided parent Folder
     *
     * @param Factory $validationFactory
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Factory $validationFactory, Request $request)
    {
        $validation = $validationFactory->make($request->all(), [
            'name' => 'required|string',
            'parent_id' => 'required|integer',
        ]);

        if ($validation->fails())
            return $this->jsonValidationBadRequest($validation);

        $parentFolder = $this->repository->findOneById($request->input('parent_id'));

        if (!$parentFolder)
            return $this->jsonNotFound("Parent folder with ID {$request->input('parent_id')} not found");

        if (!$this->manager->create($parentFolder, $request->input('name')))
            return $this->jsonResponseFromService($this->manager);

        return $this->jsonResponse('Folder created successfully', 201);
    }

    /**
     * Updates a folder with the provided details
     *
     * @param Factory $validationFactory
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Factory $validationFactory, Request $request, $id)
    {
        $validation = $validationFactory->make($request->all(), [
            'name' => 'string',
            'parent_id' => 'integer',
        ]);

        if ($validation->fails())
            return $this->jsonValidationBadRequest($validation);

        if (empty($request->input()))
            return $this->jsonBadRequest('Please provide at least one of the following fields: name, parent_id');

        $data = $request->input();

        $folder = $this->repository->findOneById($id);

        if (!$folder)
            return $this->jsonNotFound("Folder with ID {$id} not found");

        if (isset($data['parent_id']) && !empty($data['parent_id'])) {
            $newParent = $this->repository->findOneById($data['parent_id']);
            if (!$newParent)
                return $this->jsonNotFound("Parent folder with ID {$data['parent_id']} not found");
            $data['parentFolder'] = $newParent;
            unset($data['parent_id']);
        }

        if (!$this->manager->update($folder, $data))
            return $this->jsonResponseFromService($this->manager);

        return $this->jsonResponse('Folder updated successfully', 200);
    }
}