<?php

namespace App\Repositories;

use App\Models\WorkflowConfigurationHodAction;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class WorkflowConfigurationHodActionRepository
 * @package App\Repositories
 * @version July 24, 2025, 1:25 pm +04
 *
 * @method WorkflowConfigurationHodAction findWithoutFail($id, $columns = ['*'])
 * @method WorkflowConfigurationHodAction find($id, $columns = ['*'])
 * @method WorkflowConfigurationHodAction first($columns = ['*'])
*/
class WorkflowConfigurationHodActionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workflowConfigurationID',
        'hodActionID',
        'parent',
        'child'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return WorkflowConfigurationHodAction::class;
    }
}
