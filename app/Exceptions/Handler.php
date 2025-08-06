<?php

namespace App\Exceptions;

use App\Traits\HasResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use HasResponse;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            $statusCode = 500; // Default status code for server errors

            // Menentukan status code berdasarkan jenis pengecualian
            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                $statusCode = 401;
            } elseif ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
                $statusCode = 403;
            } elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                $statusCode = 404;
            } elseif ($exception instanceof \Illuminate\Validation\ValidationException) {
                $statusCode = 422;

            } elseif ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                $statusCode = 404;
            } elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                $statusCode = 405; // Mengembalikan pesan validasi khusus
            } else {
                $statusCode = $exception->getCode() ?: 500;
            }

            if ($exception instanceof HttpException){
                $statusCode = $exception->getStatusCode();
            }

            return $this->json(null,$exception->getMessage(),$statusCode);
        }

        return parent::render($request, $exception);
    }

}