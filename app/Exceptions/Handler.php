<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * @var string[]
     */
    protected $dontReport = [
        HttpException::class,
        ValidationException::class,
        AuthorizationException::class,
        ModelNotFoundException::class,
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
    public function render($request, Throwable $exception): Response
    {
        return parent::render($request, $exception);
    }
}
