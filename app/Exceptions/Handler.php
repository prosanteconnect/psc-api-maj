<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param $request
     * @param Throwable $e
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e): JsonResponse
    {
        if($e instanceof ValidationException) {
            return $this->errorResponse($e->validator->errors()->first(), 500);
        }

        $rendered = parent::render($request, $e);

        return response()->json([
            'status'=> 'Error',
            'method'=> $request->method(),
            'uri' => $request->path(),
            'message' => $e->getMessage(),
            'code' => $rendered->getStatusCode()
        ], $rendered->getStatusCode());
    }

    private function errorResponse($message, $code, $data = null): JsonResponse
    {
        return response()->json([
            'status'=>'Error',
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
