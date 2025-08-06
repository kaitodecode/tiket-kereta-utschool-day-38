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
        if ($request->expectsJson()) {
            if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return $this->json(null, "Data not found", 404);
            }

            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return $this->json(null, "Route not found", 404);
            }

            // Tambahkan pengecekan error lainnya jika perlu
        }

        // Tambahkan pengecekan error lainnya jika perlu

        return parent::render($request, $exception);
    }
}
