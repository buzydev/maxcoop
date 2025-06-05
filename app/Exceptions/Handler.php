<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Entry for ' . str_replace('App\\', '', $exception->getModel()) . ' not found',
                'status' => 404,
            ], 404);
        }

        if ($exception instanceof  NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Resource Not Found',
                // 'message' => 'Entry for ' . str_replace('App\\', '', $exception->getModel()) . ' not found',
                'status' => 404,
            ], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'status' => $exception->getCode(),
            ], $exception->getCode());
        }

        // if ($exception->getCode() == 0) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => $exception->getMessage(),
        //         'status' => $exception->getCode(),
        //     ], 500);
        // }

        return parent::render($request, $exception);
    }
}
