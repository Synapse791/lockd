<?php

namespace Lockd\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Factory;
use Lockd\Contracts\Repositories\GroupRepository;

/**
 * Class GroupController
 *
 * @package Lockd\Http\Controllers\Api
 * @author Iain Earl <synapse791@gmail.com>
 */
class GroupController extends BaseApiController
{
    /** @var GroupRepository */
    private $groupRepository;

    /**
     * GroupController constructor
     *
     * @param GroupRepository $userRepository
     */
    public function __construct(GroupRepository $userRepository)
    {
        $this->groupRepository = $userRepository;
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
            $response = $this->groupRepository->findOneById($request->query('id'));
        else if ($request->query('name', false))
            $response = $this->groupRepository->findOneByName($request->query('name'));
        else
            $response = $this->groupRepository->find();

        if (!$response)
            return $this->jsonNotFound('Group not found');

        return $this->jsonResponse($response);
    }
}