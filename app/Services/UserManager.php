<?php

namespace Lockd\Services;

use Illuminate\Hashing\BcryptHasher;
use Lockd\Models\User;

/**
 * Class UserManager
 *
 * @package Lockd\Services
 * @author Iain Earl <synapse791@gmail.com>
 */
class UserManager extends BaseService
{
    /** @var BcryptHasher */
    private $hasher;

    /**
     * UserManager constructor
     *
     * @param BcryptHasher $hasher
     */
    public function __construct(BcryptHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Creates a new User
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $password
     * @return bool|User
     */
    public function create($firstName, $lastName, $email, $password)
    {
        $errors = [];

        if (empty($firstName)) $errors[] = 'Please provide a first name';
        if (empty($lastName)) $errors[] = 'Please provide a last name';
        if (empty($email)) $errors[] = 'Please provide an email';
        if (empty($password) || strlen($password) < 6) $errors[] = 'Please provide a password of at least 6 characters';
        if (!empty($errors))
            return $this->setBadRequestError($errors);

        $user = new User();

        $user->firstName = $firstName;
        $user->lastName = $lastName;
        $user->email = $email;
        $user->password = $this->hasher->make($password);

        if (!$this->saveEntity($user, 'A user already exists with that email!'))
            return false;

        return $user;
    }

    /**
     * Updates a user's data based on what is provided
     *
     * @param User $user
     * @param array $data
     * @return bool|User
     */
    public function update(User $user, $data = [])
    {
        if (empty($data))
            return $user;

        if (isset($data['firstName']) && !empty($data['firstName']))
            $user->firstName = $data['firstName'];

        if (isset($data['lastName']) && !empty($data['lastName']))
            $user->lastName = $data['lastName'];

        if (isset($data['email']) && !empty($data['email']))
            $user->email = $data['email'];

        if (isset($data['password']) && !empty($data['password']))
            $user->password = $this->hasher->make($data['password']);

        if (!$this->saveEntity($user))
            return false;

        return $user;
    }
}