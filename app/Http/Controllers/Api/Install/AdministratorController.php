<?php

namespace Lockd\Http\Controllers\Api\Install;

use Lockd\Contracts\Repositories\GroupRepository;
use Lockd\Contracts\Repositories\UserRepository;
use Lockd\Http\Controllers\Api\BaseApiController;
use Lockd\Services\GroupManager;
use Lockd\Services\UserManager;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;

/**
 * Class AdministratorController
 *
 * @package Lockd\Http\Controllers\Api\Install
 * @author Iain Earl <synapse791@gmail.com>
 * @codeCoverageIgnore
 */
class AdministratorController extends BaseApiController
{
    private $filesystemManager;

    public function __construct(FilesystemManager $filesystemManager)
    {
        $this->filesystemManager = $filesystemManager;
    }

    public function create(
        Request $request,
        Factory $validationFactory,
        UserManager $userManager,
        UserRepository $userRepository,
        GroupManager $groupManager,
        GroupRepository $groupRepository
    ) {
        if ($userRepository->count() > 0)
            return $this->jsonResponse([], 500, 'internal_server', 'A user is already present in the database');

        $validation = $validationFactory->make($request->all(), [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        if ($validation->fails())
            return $this->jsonValidationBadRequest($validation);

        $user = $userManager->create(
            $request->get('firstName'),
            $request->get('lastName'),
            $request->get('email'),
            $request->get('password')
        );

        if (!$user)
            return $this->jsonResponseFromService($userManager);

        if (!$groupManager->addUserToGroup($user, $groupRepository->findOneByName('Administrators')))
            return $this->jsonResponseFromService($groupManager);

        $this->lockSetup();

        return $this->jsonResponse('Successfully created user');
    }

    private function lockSetup()
    {
        $this->filesystemManager->disk()->put('setup.lock', '1');
    }
}