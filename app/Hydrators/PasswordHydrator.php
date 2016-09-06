<?php

namespace Lockd\Hydrators;

use Illuminate\Contracts\Encryption\Encrypter;
use Lockd\Models\Password;

/**
 * Class PasswordHydrator
 *
 * @package Lockd\Hydrators
 * @author Iain Earl <synapse791@gmail.com>
 */
class PasswordHydrator
{
    /** @var Encrypter */
    private $encrypter;

    /**
     * PasswordHydrator constructor
     *
     * @param Encrypter $encrypter
     */
    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    /**
     * Returns a formatted view of the provided Password entity
     *
     * @param Password $password
     * @return array
     */
    public function hydrate(Password $password)
    {
        $hydratedPassword = [];

        $hydratedPassword['id'] = $password->id;
        $hydratedPassword['name'] = $password->name;
        $hydratedPassword['url'] = $password->url;
        $hydratedPassword['user'] = $password->user;

        $decryptedPassword = $this->encrypter->decrypt($password->password);
        $hydratedPassword['password'] = base64_encode($decryptedPassword);

        return $hydratedPassword;
    }
}