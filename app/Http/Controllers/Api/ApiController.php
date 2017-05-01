<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Paginate\Paginator;
use App\Transformers\Transformer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class ApiController extends Controller
{
    /** \App\Transformers\Transformer
     *
     * @var null
     */
    protected $transformer = null;

    /**
     * Return generic json response with the given data.
     *
     * @param $data
     * @param int $statusCode
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respond($data, $statusCode = 200, $headers = [])
    {
        return response()->json($data, $statusCode, $headers);
    }

    /**
     * Respond with data after applying transformer.
     *
     * @param $data
     * @param int $statusCode
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithTransformer($data, $statusCode = 200, $headers = [])
    {
        $this->checkTransformer();

        if ($data instanceof Collection) {
            $data = $this->transformer->collection($data);
        } else {
            $data = $this->transformer->item($data);
        }

        return $this->respond($data, $statusCode, $headers);
    }

    /**
     * Respond with pagination.
     *
     * @param $paginator
     * @param int $statusCode
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithPagination($paginator, $statusCode = 200, $headers = [])
    {
        $this->checkPaginator($paginator);

        $this->checkTransformer();

        $data = $this->transformer->paginate($paginator);

        return $this->respond($data, $statusCode, $headers);
    }

    /**
     * Respond with success.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondSuccess()
    {
        return $this->respond(null);
    }

    /**
     * Respond with created.
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondCreated($data)
    {
        return $this->respond($data, 201);
    }

    /**
     * Respond with no content.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondNoContent()
    {
        return $this->respond(null, 204);
    }

    /**
     * Respond with error.
     *
     * @param $message
     * @param $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondError($message, $statusCode)
    {
        return $this->respond([
            'errors' => [
                'message' => $message,
                'status_code' => $statusCode
            ]
        ], $statusCode);
    }

    /**
     * Respond with unauthorized.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondUnauthorized($message = 'Unauthorized')
    {
        return $this->respondError($message, 401);
    }

    /**
     * Respond with forbidden.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondForbidden($message = 'Forbidden')
    {
        return $this->respondError($message, 403);
    }

    /**
     * Respond with not found.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondNotFound($message = 'Not Found')
    {
        return $this->respondError($message, 404);
    }

    /**
     * Respond with failed login.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondFailedLogin()
    {
        return $this->respond([
            'errors' => [
                'email or password' => 'is invalid',
            ]
        ], 422);
    }

    /**
     * Respond with internal error.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondInternalError($message = 'Internal Error')
    {
        return $this->respondError($message, 500);
    }

    /**
     * Check if valid transformer is set.
     *
     * @throws Exception
     */
    private function checkTransformer()
    {
        if ($this->transformer === null || ! $this->transformer instanceof Transformer) {
            throw new Exception('Invalid data transformer.');
        }
    }

    /**
     * Check if valid paginator.
     *
     * @param $paginator
     * @throws Exception
     */
    private function checkPaginator($paginator)
    {
        if (! $paginator instanceof Paginator) {
            throw new Exception('Expected instance of Paginator.');
        }
    }
}