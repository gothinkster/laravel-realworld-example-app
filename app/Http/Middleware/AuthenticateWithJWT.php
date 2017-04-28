<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class AuthenticateWithJWT extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param bool $optional
     * @return mixed
     */
    public function handle($request, Closure $next, $optional = null)
    {
        $this->auth->setRequest($request);

        try {
            if (! $user = $this->auth->parseToken('token')->authenticate()) {
                return $this->respondError('User not found', 404);
            }
        } catch (TokenExpiredException $e) {
            return $this->respondError('Token has expired', $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return $this->respondError('Token is invalid', $e->getStatusCode());
        } catch (JWTException $e) {
            if ($optional === null) {
                return $this->respondError('Token is absent', $e->getStatusCode());
            }
        }

        return $next($request);
    }

    /**
     * Respond with json error message.
     *
     * @param $message
     * @param $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondError($message, $statusCode)
    {
        return response()->json([
            'error' => [
                'message' => $message,
                'status_code' => $statusCode
            ]
        ], $statusCode);
    }
}
