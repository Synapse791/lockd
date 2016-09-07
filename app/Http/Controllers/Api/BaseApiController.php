<?php

namespace Lockd\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Validator;
use Lockd\Services\BaseService;

/**
 * Class BaseApiController
 *
 * @package Lockd\Http\Controllers\Api
 * @author Iain Earl <synapse791@gmail.com>
 */
class BaseApiController extends Controller
{
    /**
     * Return a new JSON response
     *
     * @param mixed $data
     * @param int $statusCode
     * @param string|null $error
     * @param string|null $errorDescription
     * @return JsonResponse
     */
    public function jsonResponse($data, $statusCode = 200, $error = null, $errorDescription = null)
    {
        return new JsonResponse([
            'data' => $data,
            'error' => $error,
            'errorDescription' => $errorDescription,
        ], $statusCode);
    }

    /**
     * Shortcut to return bad request response
     *
     * @param string|array $message
     * @return JsonResponse
     */
    public function jsonBadRequest($message)
    {
        return $this->jsonResponse([], 400, 'bad_request', $message);
    }

    /**
     * Returns a Bad Request created from validation errors
     *
     * @param Validator $validation
     * @return JsonResponse
     */
    public function jsonValidationBadRequest(Validator $validation)
    {
        return $this->jsonBadRequest($validation->errors()->all());
    }

    /**
     * Shortcut to return unauthorized response
     *
     * @param string $message
     * @return JsonResponse
     */
    public function jsonUnauthorized($message)
    {
        return $this->jsonResponse(
            [],
            401,
            'unauthorized',
            $message
        );
    }

    /**
     * Shortcut to return not found response
     *
     * @param string|array $message
     * @return JsonResponse
     */
    public function jsonNotFound($message)
    {
        return $this->jsonResponse([], 404, 'not_found', $message);
    }

    /**
     * Returns an error response using data from a server
     *
     * @param BaseService $service
     * @return JsonResponse
     */
    public function jsonResponseFromService(BaseService $service)
    {
        return $this->jsonResponse([], $service->getErrorCode(), $service->getError(), $service->getErrorDescription());
    }
}