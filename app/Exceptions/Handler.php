<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->getJsonResponse($exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Get the json response for the exception.
     *
     * @param \Exception $exception
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function getJsonResponse(Exception $exception)
    {
        if ($exception instanceof ValidationException) {
            $validationErrors = $exception->validator->errors()->getMessages();

            $response = [
                'errors' => $validationErrors
            ];

            return response()->json($response, 422);
        }

        $statusCode = $this->getStatusCode($exception);

        if (! $message = $exception->getMessage()) {
            $message = sprintf('%d %s', $statusCode, Response::$statusTexts[$statusCode]);
        }

        $errors = [
            'message' => $message,
            'status_code' => $statusCode,
        ];

        if (config('app.debug')) {
            $errors['exception'] = get_class($exception);
            $errors['trace'] = explode("\n", $exception->getTraceAsString());
        }

        $response = [
            'error' => $errors
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Get the status code from the exception.
     *
     * @param \Exception $exception
     * @return int
     */
    protected function getStatusCode(Exception $exception)
    {
        return $this->isHttpException($exception) ? $exception->getStatusCode() : 500;
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
