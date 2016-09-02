<?php

namespace Lockd\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Factory;
use Lockd\Contracts\Repositories\UserRepository;

/**
 * Class UserController
 *
 * @package Lockd\Http\Controllers\Api
 * @author Iain Earl <synapse791@gmail.com>
 */
class UserController extends BaseApiController
{
    /** @var UserRepository */
    private $userRepository;

    /**
     * UserController constructor
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Retrieve either a single user by their ID or email, or all users
     *
     * @param Factory $validationFactory
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Factory $validationFactory, Request $request)
    {
        $validation = $validationFactory->make($request->all(), [
            'id' => 'integer',
            'email' => 'email',
        ]);

        if ($validation->fails())
            return $this->jsonValidationBadRequest($validation);

        if ($request->query('id', false))
            $response = $this->userRepository->findOneById($request->query('id'));
        else if ($request->query('email', false))
            $response = $this->userRepository->findOneByEmail($request->query('email'));
        else
            $response = $this->userRepository->find();

        if (!$response)
            return $this->jsonNotFound('User not found');

        return $this->jsonResponse($response);
    }
}