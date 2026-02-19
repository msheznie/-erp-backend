<?php

namespace App\Services\Authorization;

use App\Contracts\Authorization\AuthorizationStrategyInterface;
use App\Exceptions\UnauthorizedException;
use App\Services\Authorization\Strategies\RoleBasedAuthorizationStrategy;
use App\Services\Authorization\Strategies\PermissionBasedAuthorizationStrategy;
use App\Services\Authorization\Strategies\OwnershipAuthorizationStrategy;
use Illuminate\Http\Request;

class ResourceAuthorizationService
{
    /**
     * Registered authorization strategies
     *
     * @var array<AuthorizationStrategyInterface>
     */
    protected array $strategies = [];

    /**
     * Create a new authorization service instance
     */
    public function __construct()
    {
        $this->registerDefaultStrategies();
    }

    /**
     * Register default authorization strategies
     */
    protected function registerDefaultStrategies(): void
    {
        $this->registerStrategy(new RoleBasedAuthorizationStrategy());
        $this->registerStrategy(new PermissionBasedAuthorizationStrategy());
        $this->registerStrategy(new OwnershipAuthorizationStrategy());
    }

    /**
     * Register a custom authorization strategy
     *
     * @param AuthorizationStrategyInterface $strategy
     * @return self
     */
    public function registerStrategy(AuthorizationStrategyInterface $strategy): self
    {
        $this->strategies[] = $strategy;

        return $this;
    }

    /**
     * Authorize a request using the specified method
     *
     * @param Request $request
     * @param string $method
     * @param array $options
     * @return bool
     * @throws UnauthorizedException
     */
    public function authorize(Request $request, string $method, array $options = []): bool
    {
        // Check if authorization is enabled
        if (!$this->isAuthorizationEnabled()) {
            return true;
        }

        // Find a strategy that supports this method
        $strategy = $this->findStrategy($method);

        if (!$strategy) {
            throw new UnauthorizedException(
                "No authorization strategy found for method: {$method}",
                $method,
                ['available_methods' => $this->getAvailableMethods()]
            );
        }

        // Execute the authorization strategy
        return $strategy->authorize($request, $method, $options);
    }

    /**
     * Find a strategy that supports the given method
     *
     * @param string $method
     * @return AuthorizationStrategyInterface|null
     */
    protected function findStrategy(string $method): ?AuthorizationStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($method)) {
                return $strategy;
            }
        }

        return null;
    }

    /**
     * Check if authorization is enabled
     *
     * @return bool
     */
    protected function isAuthorizationEnabled(): bool
    {
        return config('auth.authorization_enabled', env('ENABLE_AUTHORIZATION', false));
    }

    /**
     * Get all available authorization methods
     *
     * @return array
     */
    public function getAvailableMethods(): array
    {
        $methods = [];

        foreach ($this->strategies as $strategy) {
            $reflection = new \ReflectionClass($strategy);
            $property = $reflection->getProperty('supportedMethods');
            $property->setAccessible(true);
            $methods = array_merge($methods, $property->getValue($strategy));
        }

        return array_unique($methods);
    }

    /**
     * Authorize multiple methods (OR logic - passes if any method passes)
     *
     * @param Request $request
     * @param array $methods Array of method names
     * @param array $options
     * @return bool
     * @throws UnauthorizedException
     */
    public function authorizeAny(Request $request, array $methods, array $options = []): bool
    {
        $lastException = null;

        foreach ($methods as $method) {
            try {
                if ($this->authorize($request, $method, $options)) {
                    return true;
                }
            } catch (UnauthorizedException $e) {
                $lastException = $e;
                continue;
            }
        }

        throw $lastException ?? new UnauthorizedException(
            'None of the authorization methods passed',
            'any',
            ['methods' => $methods]
        );
    }

    /**
     * Authorize multiple methods (AND logic - passes only if all methods pass)
     *
     * @param Request $request
     * @param array $methods Array of method names
     * @param array $options
     * @return bool
     * @throws UnauthorizedException
     */
    public function authorizeAll(Request $request, array $methods, array $options = []): bool
    {
        foreach ($methods as $method) {
            $this->authorize($request, $method, $options);
        }

        return true;
    }

    /**
     * Check if authorization would pass without throwing exception
     *
     * @param Request $request
     * @param string $method
     * @param array $options
     * @return bool
     */
    public function check(Request $request, string $method, array $options = []): bool
    {
        try {
            return $this->authorize($request, $method, $options);
        } catch (UnauthorizedException $e) {
            return false;
        }
    }
}
