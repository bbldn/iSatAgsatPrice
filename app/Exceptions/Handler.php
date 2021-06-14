<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * @var string[]
     * 
     * @psalm-var list<class-string>
     */
    protected $dontReport = [
        HttpException::class,
        ValidationException::class,
        AuthorizationException::class,
        ModelNotFoundException::class,
    ];
}