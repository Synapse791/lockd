<?php

namespace Lockd\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Factory;
use Lockd\Contracts\Repositories\UserRepository;
use Lockd\Services\UserManager;

/**
 * Class UserController
 *
 * @package Lockd\Http\Controllers\Api
 * @author Iain Earl <synapse791@gmail.com>
 */
class UserController extends BaseApiController
{
    /** @var UserRepository */
    private $repository;

    /** @var UserManager */
    private $manager;

    /**
     * UserController constructor
     *
     * @param UserRepository $userRepository
     * @param UserManager $userManager
     */
    public function __construct(UserRepository $userRepository, UserManager $userManager)
    {
        $this->repository = $userRepository;
        $this->manager = $userManager;
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
            $response = $this->repository->findOneById($request->query('id'));
        else if ($request->query('email', false))
            $response = $this->repository->findOneByEmail($request->query('email'));
        else
            $response = $this->repository->find();

        if (!$response)
            return $this->jsonNotFound('User not found');

        return $this->jsonResponse($response);
    }

    /**
     * Create a new User
     *
     * @param Factory $validationFactory
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Factory $validationFactory, Request $request)
    {
        // TODO Tests!
        $validation = $validationFactory->make($request->input(), [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validation->fails())
            return $this->jsonValidationBadRequest($validation);

        if (!$this->manager->create(
            $request->input('firstName'),
            $request->input('lastName'),
            $request->input('email'),
            $request->input('password')
        ))
            return $this->jsonResponseFromService($this->manager);

        return $this->jsonResponse('User created successfully', 201);
    }
}