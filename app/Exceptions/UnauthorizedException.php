<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UnauthorizedException extends Exception
{
    /**
     * The authorization method that failed
     */
    protected string $authMethod;

    /**
     * Additional context for the failure
     */
    protected array $context;

    /**
     * Create a new unauthorized exception instance
     *
     * @param  string  $message  The error message
     * @param  string  $authMethod  The authorization method that failed
     * @param  array  $context  Additional context
     * @param  int  $code  The HTTP status code (default 403)
     */
    public function __construct(
        string $message = 'Unauthorized access',
        string $authMethod = '',
        array $context = [],
        int $code = 403
    ) {
        parent::__construct($message, $code);
        $this->authMethod = $authMethod;
        $this->context = $context;
    }

    /**
     * Get the authorization method that failed
     */
    public function getAuthMethod(): string
    {
        return $this->authMethod;
    }

    /**
     * Get the context
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Render the exception as an HTTP response
     */
    public function render(): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $this->getMessage(),
        ];

        if (config('app.debug')) {
            $response['debug'] = [
                'method' => $this->authMethod,
                'context' => $this->context,
            ];
        }

        return response()->json($response, $this->getCode());
    }

    /**
     * Report the exception
     */
    public function report(): void
    {
        \Log::channel('authorization')->warning('Authorization failed', [
            'message' => $this->getMessage(),
            'method' => $this->authMethod,
            'context' => $this->context,
            'route' => request()->route()?->uri(),
            'user_id' => auth()->id(),
        ]);
    }
}
