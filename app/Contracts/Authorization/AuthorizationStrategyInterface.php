<?php

namespace App\Contracts\Authorization;

use Illuminate\Http\Request;

interface AuthorizationStrategyInterface
{
    /**
     * Authorize the request using the strategy's logic
     *
     * @param Request $request The incoming HTTP request
     * @param string $method The authorization method to use
     * @param array $options Additional options for authorization
     * @return bool Returns true if authorized
     * @throws \App\Exceptions\UnauthorizedException When authorization fails
     */
    public function authorize(Request $request, string $method, array $options = []): bool;

    /**
     * Check if this strategy can handle the given method
     *
     * @param string $method The authorization method
     * @return bool Returns true if this strategy can handle the method
     */
    public function supports(string $method): bool;
}
