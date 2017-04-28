<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    protected $transformer = null;

    public function respond($data, $statusCode = 200, $headers = [])
    {
        return response()->json($data, $statusCode, $headers);
    }

    public function respondWithTransformer($data, $statusCode = 200, $headers = [])
    {
        if ($this->transformer !== null) {
            $data = $this->transformer->item($data);
        }

        return $this->respond($data, $statusCode, $headers);
    }

    protected function respondCreated($data)
    {
        return $this->respond($data, 201);
    }

    protected function respondNoContent()
    {
        return $this->respond([], 204);
    }

    public function respondError($message, $statusCode)
    {
        return $this->respond([
            'error' => [
                'message' => $message,
                'status_code' => $statusCode
            ]
        ], $statusCode);
    }

    public function respondUnauthorized($message = 'Unauthorized')
    {
        return $this->respondError($message, 401);
    }

    public function respondForbidden($message = 'Forbidden')
    {
        return $this->respondError($message, 403);
    }

    public function respondNotFound($message = 'Not Found')
    {
        return $this->respondError($message, 404);
    }

    public function respondInternalError($message = 'Internal Error')
    {
        return $this->respondError($message, 500);
    }
}