<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Paginate\Paginator;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    protected $transformer = null;

    protected function respond($data, $statusCode = 200, $headers = [])
    {
        return response()->json($data, $statusCode, $headers);
    }

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

    protected function respondWithPagination($paginator, $statusCode = 200, $headers = [])
    {
        $this->checkPaginator($paginator);

        $this->checkTransformer();

        $data = $this->transformer->paginate($paginator);

        return $this->respond($data, $statusCode, $headers);
    }

    protected function respondSuccess()
    {
        return $this->respond(null);
    }

    protected function respondCreated($data)
    {
        return $this->respond($data, 201);
    }

    protected function respondNoContent()
    {
        return $this->respond(null, 204);
    }

    protected function respondError($message, $statusCode)
    {
        return $this->respond([
            'error' => [
                'message' => $message,
                'status_code' => $statusCode
            ]
        ], $statusCode);
    }

    protected function respondUnauthorized($message = 'Unauthorized')
    {
        return $this->respondError($message, 401);
    }

    protected function respondForbidden($message = 'Forbidden')
    {
        return $this->respondError($message, 403);
    }

    protected function respondNotFound($message = 'Not Found')
    {
        return $this->respondError($message, 404);
    }

    protected function respondInternalError($message = 'Internal Error')
    {
        return $this->respondError($message, 500);
    }

    private function checkTransformer()
    {
        if ($this->transformer === null) {
            throw new Exception('Data transformer not set.');
        }
    }

    private function checkPaginator($paginator)
    {
        if (! $paginator instanceof Paginator) {
            throw new Exception('Expected instance of Paginator.');
        }
    }
}