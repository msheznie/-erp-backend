<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Response;

/**
 * ServiceResponse - Standardized response wrapper for the Service layer.
 *
 * Usage in a Service:
 *   return ServiceResponse::success($data, 'Record created successfully');
 *   return ServiceResponse::failure('Validation failed', ['field' => 'error']);
 *
 * Usage in a Controller:
 *   $response = $this->invoiceService->create($input);
 *   if (!$response->isSuccess()) {
 *       return $this->sendError($response->getMessage(), $response->getStatusCode());
 *   }
 *   return $this->sendResponse($response->getData(), $response->getMessage());
 */
class ServiceResponse
{
    private function __construct(
        private readonly bool $success,
        private readonly string $message,
        private readonly mixed $data,
        private readonly int $statusCode,
        private readonly array $errors
    ) {}

    // -----------------------------------------------------------------------
    // Static factory methods
    // -----------------------------------------------------------------------

    /**
     * Operation completed successfully.
     */
    public static function success(mixed $data = null, string $message = 'Operation successful', int $statusCode = Response::HTTP_OK): self
    {
        return new self(true, $message, $data, $statusCode, []);
    }

    /**
     * Operation failed.
     *
     * @param array<string, string|string[]> $errors  Field-level error messages.
     */
    public static function failure(string $message = 'Operation failed', array $errors = [], int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY): self
    {
        return new self(false, $message, null, $statusCode, $errors);
    }

    // -----------------------------------------------------------------------
    // Accessors
    // -----------------------------------------------------------------------

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<string, string|string[]>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    // -----------------------------------------------------------------------
    // Serialization
    // -----------------------------------------------------------------------

    /**
     * Convert to a plain array (useful for logging or passing to other services).
     *
     * @return array{success: bool, message: string, data: mixed, errors?: array}
     */
    public function toArray(): array
    {
        $result = [
            'success' => $this->success,
            'message' => $this->message,
            'data'    => $this->data,
        ];

        if (!empty($this->errors)) {
            $result['errors'] = $this->errors;
        }

        return $result;
    }
}
