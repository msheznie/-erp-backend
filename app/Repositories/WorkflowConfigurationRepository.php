<?php

namespace App\Repositories;

use App\Models\WorkflowConfiguration;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class WorkflowConfigurationRepository
 * @package App\Repositories
 * @version July 24, 2025, 1:16 pm +04
 *
 * @method WorkflowConfiguration findWithoutFail($id, $columns = ['*'])
 * @method WorkflowConfiguration find($id, $columns = ['*'])
 * @method WorkflowConfiguration first($columns = ['*'])
*/
class WorkflowConfigurationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workflowName',
        'initiateBudget',
        'method',
        'allocation',
        'finalApproval',
        'isActive'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return WorkflowConfiguration::class;
    }
}
