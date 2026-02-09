<?php

namespace App\Traits;

use App\Exceptions\UnauthorizedException;
use App\Services\Authorization\ResourceAuthorizationService;
use Illuminate\Http\Request;

trait AuthorizesResource
{
    /**
     * Authorization service instance
     */
    protected ?ResourceAuthorizationService $authorizationService = null;

    /**
     * Get the authorization service instance
     */
    protected function getAuthorizationService(): ResourceAuthorizationService
    {
        if (! $this->authorizationService) {
            $this->authorizationService = app(ResourceAuthorizationService::class);
        }

        return $this->authorizationService;
    }

    /**
     * Authorize a resource using the specified method
     *
     * @param  string  $method  The authorization method (role, permission, ownership, etc.)
     * @param  array  $options  Additional options for authorization
     *
     * @throws UnauthorizedException
     *
     * @example
     * // Role-based authorization
     * $this->authorizeResource($request, 'role', ['route_name' => 'asset.index']);
     *
     * // Permission-based authorization
     * $this->authorizeResource($request, 'permission', [
     *     'permissions' => ['asset.view', 'asset.edit']
     * ]);
     *
     * // Ownership-based authorization
     * $this->authorizeResource($request, 'ownership', [
     *     'model' => AssetType::class,
     *     'resource_id' => $id
     * ]);
     */
    protected function authorizeResource(Request $request, string $method, array $options = []): bool
    {
        return $this->getAuthorizationService()->authorize($request, $method, $options);
    }

    /**
     * Authorize using multiple methods with OR logic (passes if any method passes)
     *
     * @throws UnauthorizedException
     *
     * @example
     * $this->authorizeResourceAny($request, ['role', 'permission'], [
     *     'route_name' => 'asset.index',
     *     'permissions' => ['asset.view']
     * ]);
     */
    protected function authorizeResourceAny(Request $request, array $methods, array $options = []): bool
    {
        return $this->getAuthorizationService()->authorizeAny($request, $methods, $options);
    }

    /**
     * Authorize using multiple methods with AND logic (passes only if all methods pass)
     *
     * @throws UnauthorizedException
     *
     * @example
     * $this->authorizeResourceAll($request, ['role', 'ownership'], [
     *     'route_name' => 'asset.update',
     *     'model' => $asset
     * ]);
     */
    protected function authorizeResourceAll(Request $request, array $methods, array $options = []): bool
    {
        return $this->getAuthorizationService()->authorizeAll($request, $methods, $options);
    }

    /**
     * Check if authorization would pass without throwing exception
     *
     *
     * @example
     * if ($this->canAuthorizeResource($request, 'permission', ['permissions' => ['asset.delete']])) {
     *     // User can delete
     * }
     */
    protected function canAuthorizeResource(Request $request, string $method, array $options = []): bool
    {
        return $this->getAuthorizationService()->check($request, $method, $options);
    }

    /**
     * Get all available authorization methods
     */
    protected function getAvailableAuthorizationMethods(): array
    {
        return $this->getAuthorizationService()->getAvailableMethods();
    }
}
