<?php

namespace App\Support;

use App\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ApiExceptionRenderer
{
    use ApiResponse;

    public function render(Throwable $exception): JsonResponse
    {
        if ($exception instanceof ValidationException) {
            return $this->problemResponse(
                'Unprocessable Content',
                422,
                'The request data failed validation.',
                extensions: ['errors' => $exception->errors()],
            );
        }

        if ($exception instanceof AuthenticationException) {
            return $this->problemResponse('Unauthorized', 401, 'Authentication is required.');
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->problemResponse('Not Found', 404, 'The requested resource was not found.');
        }

        if ($exception instanceof AuthorizationException) {
            return $this->problemResponse('Forbidden', 403, 'You are not allowed to perform this action.');
        }

        if ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();

            return $this->problemResponse(
                match ($status) {
                    400 => 'Bad Request',
                    404 => 'Not Found',
                    405 => 'Method Not Allowed',
                    409 => 'Conflict',
                    429 => 'Too Many Requests',
                    default => $status >= 500 ? 'Internal Server Error' : 'HTTP Error',
                },
                $status,
                $status === 404
                    ? 'The requested resource was not found.'
                    : 'The request could not be completed.',
            );
        }

        return $this->problemResponse(
            'Internal Server Error',
            500,
            'An unexpected error occurred.',
        );
    }
}
