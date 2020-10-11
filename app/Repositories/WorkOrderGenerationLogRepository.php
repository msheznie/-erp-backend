<?php

namespace App\Repositories;

use App\Models\WorkOrderGenerationLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class WorkOrderGenerationLogRepository
 * @package App\Repositories
 * @version October 11, 2020, 9:04 am +04
 *
 * @method WorkOrderGenerationLog findWithoutFail($id, $columns = ['*'])
 * @method WorkOrderGenerationLog find($id, $columns = ['*'])
 * @method WorkOrderGenerationLog first($columns = ['*'])
*/
class WorkOrderGenerationLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'date',
        'createdUser',
        'companySystemID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return WorkOrderGenerationLog::class;
    }
}
