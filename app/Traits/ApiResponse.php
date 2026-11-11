<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * @param  array<string, mixed>  $meta
     * @param  array<string, mixed>  $links
     */
    protected function successResponse(
        mixed $data,
        string $message = 'OK',
        int $status = 200,
        array $meta = [],
        array $links = [],
    ): JsonResponse {
        $payload = ['data' => $data, 'message' => $message];

        if ($meta !== []) {
            $payload['meta'] = $meta;
        }

        if ($links !== []) {
            $payload['links'] = $links;
        }

        return response()->json($payload, $status);
    }

    protected function createdResponse(mixed $data, string $location, string $message = 'Created'): JsonResponse
    {
        return $this->successResponse($data, $message, 201)
            ->header('Location', $location);
    }

    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * @param  array<string, mixed>  $extensions
     */
    protected function problemResponse(
        string $title,
        int $status,
        string $detail,
        string $type = 'about:blank',
        array $extensions = [],
    ): JsonResponse {
        $problem = [
            'type' => $type,
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'instance' => request()->getRequestUri(),
        ];

        return response()->json(
            array_merge($problem, array_diff_key($extensions, $problem)),
            $status,
        )->header('Content-Type', 'application/problem+json');
    }
}
