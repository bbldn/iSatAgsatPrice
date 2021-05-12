<?php

namespace App\Exceptions;

use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * @var string[]
     * @psalm-var class-string[]
     */
    protected $dontReport = [
        \Illuminate\Validation\ValidationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * {@inheritdoc}
     */
    public function render($request, Throwable $e): Response
    {
        return parent::render($request, $e);
    }
}