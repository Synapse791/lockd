<?php

namespace Lockd\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Factory;
use Lockd\Contracts\Repositories\GroupRepository;
use Lockd\Services\GroupManager;

/**
 * Class GroupController
 *
 * @package Lockd\Http\Controllers\Api
 * @author Iain Earl <synapse791@gmail.com>
 */
class GroupController extends BaseApiController
{
    /** @var GroupRepository */
    private $repository;

    /** @var GroupManager */
    private $manager;

    /**
     * GroupController constructor
     *
     * @param GroupRepository $userRepository
     * @param GroupManager $groupManager
     */
    public function __construct(GroupRepository $userRepository, GroupManager $groupManager)
    {
        $this->repository = $userRepository;
        $this->manager = $groupManager;
    }

    /**
     * Retrieve either a single group by it's ID or name, or all groups
     *
     * @param Factory $validationFactory
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Factory $validationFactory, Request $request)
    {
        $validation = $validationFactory->make($request->all(), [
            'id' => 'integer',
            'name' => 'string',
        ]);

        if ($validation->fails())
            return $this->jsonValidationBadRequest($validation);

        if ($request->query('id', false))
            $response = $this->repository->findOneById($request->query('id'));
        else if ($request->query('name', false))
            $response = $this->repository->findOneByName($request->query('name'));
        else
            $response = $this->repository->find();

        if (!$response)
            return $this->jsonNotFound('Group not found');

        return $this->jsonResponse($response);
    }

    /**
     * Creates a new Group
     *
     * @param Factory $validationFactory
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Factory $validationFactory, Request $request)
    {
        $validation = $validationFactory->make($request->input(), [
            'name' => 'required|string',
        ]);

        if ($validation->fails())
            return $this->jsonValidationBadRequest($validation);

        if (!$this->manager->create($request->input('name')))
            return $this->jsonResponseFromService($this->manager);

        return $this->jsonResponse('Group created successfully', 201);
    }

    /**
     * Updates a Group's details
     *
     * @param Factory $validationFactory
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Factory $validationFactory, Request $request, $id)
    {
        $validation = $validationFactory->make($request->input(), [
            'name' => 'required|string',
        ]);

        if ($validation->fails())
            return $this->jsonValidationBadRequest($validation);

        $group = $this->repository->findOneById($id);

        if (!$group)
            return $this->jsonNotFound("Group with ID {$id} not found");

        if (!$this->manager->update($group, $request->all()))
            return $this->jsonResponseFromService($this->manager);

        return $this->jsonResponse('Group updated successfully', 200);
    }
}