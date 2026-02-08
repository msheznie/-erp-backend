<?php

namespace App\Services\Authorization\Strategies;

use App\Contracts\Authorization\AuthorizationStrategyInterface;
use App\Exceptions\UnauthorizedException;
use App\helper\Helper;
use Illuminate\Http\Request;

class PermissionBasedAuthorizationStrategy implements AuthorizationStrategyInterface
{
    /**
     * Supported authorization methods
     */
    protected array $supportedMethods = [
        'permission',
        'can',
        'ability',
    ];

    /**
     * Authorize based on specific permissions
     *
     * @param Request $request
     * @param string $method
     * @param array $options
     * @return bool
     * @throws UnauthorizedException
     */
    public function authorize(Request $request, string $method, array $options = []): bool
    {
        $employeeSystemID = Helper::getEmployeeSystemID();

        if (!$employeeSystemID) {
            throw new UnauthorizedException(
                'Employee not found',
                $method,
                ['employee_id' => null]
            );
        }

        // Get required permissions from options
        $requiredPermissions = $options['permissions'] ?? [];

        if (empty($requiredPermissions)) {
            throw new UnauthorizedException(
                'No permissions specified for authorization',
                $method,
                ['employee_id' => $employeeSystemID]
            );
        }

        // Normalize permissions to array
        if (!is_array($requiredPermissions)) {
            $requiredPermissions = [$requiredPermissions];
        }

        // Check if user has all required permissions (AND logic)
        $requireAll = $options['require_all'] ?? true;

        $hasPermission = $requireAll
            ? $this->hasAllPermissions($employeeSystemID, $requiredPermissions, $options)
            : $this->hasAnyPermission($employeeSystemID, $requiredPermissions, $options);

        if (!$hasPermission) {
            throw new UnauthorizedException(
                'User does not have required permission(s) to access this resource',
                $method,
                [
                    'employee_id' => $employeeSystemID,
                    'required_permissions' => $requiredPermissions,
                    'require_all' => $requireAll,
                ]
            );
        }

        return true;
    }

    /**
     * Check if user has all required permissions
     *
     * @param int $employeeSystemID
     * @param array $permissions
     * @param array $options
     * @return bool
     */
    protected function hasAllPermissions(int $employeeSystemID, array $permissions, array $options): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->checkPermission($employeeSystemID, $permission, $options)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has any of the required permissions
     *
     * @param int $employeeSystemID
     * @param array $permissions
     * @param array $options
     * @return bool
     */
    protected function hasAnyPermission(int $employeeSystemID, array $permissions, array $options): bool
    {
        foreach ($permissions as $permission) {
            if ($this->checkPermission($employeeSystemID, $permission, $options)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check a specific permission
     * This method should be customized based on your permission storage structure
     *
     * @param int $employeeSystemID
     * @param string $permission
     * @param array $options
     * @return bool
     */
    protected function checkPermission(int $employeeSystemID, string $permission, array $options): bool
    {
        // Example implementation - customize based on your permission system
        // You might have a UserPermission model or similar

        // Option 1: Check via Laravel Gate
        if (method_exists(auth()->user(), 'can')) {
            return auth()->user()->can($permission);
        }

        // Option 2: Custom permission checking logic
        // Uncomment and implement based on your permission structure
        /*
        $hasPermission = \App\Models\UserPermission::where('employeeSystemID', $employeeSystemID)
            ->where('permission', $permission)
            ->where('companySystemID', Helper::getCompanySystemID())
            ->exists();

        return $hasPermission;
        */

        // Default: Return true (implement your own logic)
        return true;
    }

    /**
     * Check if this strategy supports the given method
     *
     * @param string $method
     * @return bool
     */
    public function supports(string $method): bool
    {
        return in_array($method, $this->supportedMethods);
    }
}
