<?php

namespace Lockd\Services;

/**
 * Class BaseService
 *
 * @package Lockd\Services
 * @author Iain Earl <synapse791@gmail.com>
 */
class BaseService
{
    /** @var string */
    private $error;

    /** @var int */
    private $errorCode;

    /** @var string|array */
    private $errorDescription;

    /**
     * Sets the error string
     *
     * @param string $error
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Returns the error string
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Sets the error code
     *
     * @param int $errorCode
     * @return $this
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * Returns the error code
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Sets the error description
     *
     * @param string|array $errorDescription
     * @return $this
     */
    public function setErrorDescription($errorDescription)
    {
        $this->errorDescription = $errorDescription;

        return $this;
    }

    /**
     * Returns the error description
     *
     * @return array|string
     */
    public function getErrorDescription()
    {
        return $this->errorDescription;
    }

    /**
     * Shortcut to set 400 Bad Request error
     *
     * @param string|array $message
     * @return bool
     */
    public function setBadRequestError($message)
    {
        $this
            ->setError('bad_request')
            ->setErrorCode(400)
            ->setErrorDescription($message);

        return false;
    }

    /**
     * Shortcut to set 404 Not Found error
     *
     * @param string|array $message
     * @return bool
     */
    public function setNotFoundError($message)
    {
        $this
            ->setError('not_found')
            ->setErrorCode(404)
            ->setErrorDescription($message);

        return false;
    }

    /**
     * Shortcut to set 409 Conflict error
     *
     * @param string|array $message
     * @return bool
     */
    public function setConflictError($message)
    {
        $this
            ->setError('conflict')
            ->setErrorCode(409)
            ->setErrorDescription($message);

        return false;
    }
}