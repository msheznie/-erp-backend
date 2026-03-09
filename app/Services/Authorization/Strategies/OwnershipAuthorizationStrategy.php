<?php

namespace App\Services\Authorization\Strategies;

use App\Contracts\Authorization\AuthorizationStrategyInterface;
use App\Exceptions\UnauthorizedException;
use App\helper\Helper;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class OwnershipAuthorizationStrategy implements AuthorizationStrategyInterface
{
    /**
     * Supported authorization methods
     */
    protected array $supportedMethods = [
        'ownership',
        'owner',
        'belongs_to_user',
    ];

    /**
     * Authorize based on resource ownership
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

        // Get the model instance or ID to check
        $model = $options['model'] ?? null;
        $resourceId = $options['resource_id'] ?? $request->route('id');

        if (!$model && !$resourceId) {
            throw new UnauthorizedException(
                'No resource specified for ownership check',
                $method,
                ['employee_id' => $employeeSystemID]
            );
        }

        // If model is a string (class name), fetch the instance
        if (is_string($model) && $resourceId) {
            $model = $model::find($resourceId);
        }

        if (!$model instanceof Model) {
            throw new UnauthorizedException(
                'Invalid model for ownership check',
                $method,
                [
                    'employee_id' => $employeeSystemID,
                    'resource_id' => $resourceId,
                ]
            );
        }

        // Get ownership field name (default: 'employeeSystemID')
        $ownerField = $options['owner_field'] ?? 'employeeSystemID';

        // Check if user owns the resource
        if (!$this->checkOwnership($model, $employeeSystemID, $ownerField)) {
            throw new UnauthorizedException(
                'User does not own this resource',
                $method,
                [
                    'employee_id' => $employeeSystemID,
                    'resource_id' => $model->getKey(),
                    'resource_type' => get_class($model),
                    'owner_field' => $ownerField,
                ]
            );
        }

        // Additional company scope check for multi-tenancy
        if ($options['check_company_scope'] ?? true) {
            $this->checkCompanyScope($model);
        }

        return true;
    }

    /**
     * Check if user owns the resource
     *
     * @param Model $model
     * @param int $employeeSystemID
     * @param string $ownerField
     * @return bool
     */
    protected function checkOwnership(Model $model, int $employeeSystemID, string $ownerField): bool
    {
        // Check if the model has the ownership field
        if (!isset($model->{$ownerField})) {
            return false;
        }

        return $model->{$ownerField} == $employeeSystemID;
    }

    /**
     * Check company scope for multi-tenancy
     *
     * @param Model $model
     * @return void
     * @throws UnauthorizedException
     */
    protected function checkCompanyScope(Model $model): void
    {
        $companySystemID = Helper::getCompanySystemID();

        if (!$companySystemID) {
            return;
        }

        // Check if model has companySystemID field
        if (isset($model->companySystemID) && $model->companySystemID != $companySystemID) {
            throw new UnauthorizedException(
                'Resource does not belong to current company',
                'ownership',
                [
                    'resource_company_id' => $model->companySystemID,
                    'current_company_id' => $companySystemID,
                ]
            );
        }
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
