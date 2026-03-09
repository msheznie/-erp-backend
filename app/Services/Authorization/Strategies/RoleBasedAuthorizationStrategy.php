<?php

namespace App\Services\Authorization\Strategies;

use App\Contracts\Authorization\AuthorizationStrategyInterface;
use App\Exceptions\UnauthorizedException;
use App\Models\EmployeeNavigation;
use App\Models\RoleRoute;
use App\helper\Helper;
use Illuminate\Http\Request;

class RoleBasedAuthorizationStrategy implements AuthorizationStrategyInterface
{
    /**
     * Supported authorization methods
     */
    protected array $supportedMethods = [
        'role',
        'route_role',
        'user_group',
    ];

    /**
     * Authorize based on user roles and route access
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

        // Get route name from options or request
        $routeName = $options['route_name'] ?? $request->route()?->getName();

        if (!$routeName) {
            throw new UnauthorizedException(
                'Route name not found',
                $method,
                ['route' => $request->route()?->uri()]
            );
        }

        // Get user groups for the employee
        $userGroups = EmployeeNavigation::where('employeeSystemID', $employeeSystemID)->get();
        $userGroupIDs = $userGroups->isNotEmpty()
            ? $userGroups->pluck('userGroupID')->toArray()
            : [];

        if (empty($userGroupIDs)) {
            throw new UnauthorizedException(
                'No user groups assigned to employee',
                $method,
                [
                    'employee_id' => $employeeSystemID,
                    'route_name' => $routeName,
                ]
            );
        }

        // Check if any of the user's groups have access to this route
        $hasAccess = RoleRoute::whereIn('userGroupID', $userGroupIDs)
            ->where('routeName', $routeName)
            ->exists();

        if (!$hasAccess) {
            // Check for specific roles if provided in options
            if (isset($options['required_roles'])) {
                $hasAccess = $this->checkSpecificRoles($userGroupIDs, $options['required_roles']);
            }
        }

        if (!$hasAccess) {
            throw new UnauthorizedException(
                'User does not have required role to access this resource',
                $method,
                [
                    'employee_id' => $employeeSystemID,
                    'route_name' => $routeName,
                    'user_groups' => $userGroupIDs,
                ]
            );
        }

        return true;
    }

    /**
     * Check if user has specific roles
     *
     * @param array $userGroupIDs
     * @param array $requiredRoles
     * @return bool
     */
    protected function checkSpecificRoles(array $userGroupIDs, array $requiredRoles): bool
    {
        // Implementation depends on your role structure
        // This is a placeholder for custom role checking logic
        return !empty(array_intersect($userGroupIDs, $requiredRoles));
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
