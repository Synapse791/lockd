<?php

namespace Lockd\Services;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /**
     * Shortcut to set 500 Internal Server error
     *
     * @param string|array $message
     * @return bool
     */
    public function setInternalServerError($message)
    {
        $this
            ->setError('internal_server_error')
            ->setErrorCode(500)
            ->setErrorDescription($message);

        return false;
    }

    /**
     * Tries to save an entity
     *
     * @param Model $entity
     * @param null $conflictMessage
     * @return bool
     */
    public function saveEntity($entity, $conflictMessage = null)
    {
        try {
            $entity->save();
            return true;
        } catch (\PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry'))
                return is_null($conflictMessage)
                    ? $this->setConflictError(get_class($entity) . ' already exists')
                    : $this->setConflictError($conflictMessage);

            return $this->setInternalServerError($e->getMessage());
        }
    }

    /**
     * Tries to associate an entity with a relationship
     *
     * @param BelongsTo $relationship
     * @param Model $entity
     * @return bool
     */
    public function associateEntities(BelongsTo $relationship, Model $entity)
    {
        try {
            $relationship->associate($entity);
            return true;
        } catch (\PDOException $e) {
            return $this->setInternalServerError($e->getMessage());
        }
    }

    /**
     * Tries to dissociate a relationship
     *
     * @param BelongsTo $relationship
     * @return bool
     */
    public function dissociateEntities(BelongsTo $relationship)
    {
        try {
            $relationship->dissociate();
            return true;
        } catch (\PDOException $e) {
            return $this->setInternalServerError($e->getMessage());
        }
    }

    /**
     * Tries to attach an entity to a relationship
     *
     * @param BelongsToMany $relationship
     * @param Model $entity
     * @return bool
     */
    public function attachEntities(BelongsToMany $relationship, Model $entity)
    {
        try {
            $relationship->attach($entity);
            return true;
        } catch (\PDOException $e) {
            return $this->setInternalServerError($e->getMessage());
        }
    }

    /**
     * Tries to detach an entity to a relationship
     *
     * @param BelongsToMany $relationship
     * @param Model $entity
     * @return bool
     */
    public function detachEntities(BelongsToMany $relationship, Model $entity)
    {
        try {
            $relationship->detach($entity);
            return true;
        } catch (\PDOException $e) {
            return $this->setInternalServerError($e->getMessage());
        }
    }
}