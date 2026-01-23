<?php

namespace App\Repositories;

use App\Models\JobErrorLog;
use App\Repositories\BaseRepository;

/**
 * Class JobErrorLogRepository
 * @package App\Repositories
 * @version May 12, 2022, 2:42 pm +04
 *
 * @method JobErrorLog findWithoutFail($id, $columns = ['*'])
 * @method JobErrorLog find($id, $columns = ['*'])
 * @method JobErrorLog first($columns = ['*'])
*/
class JobErrorLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentSystemCode',
        'tag',
        'errorType',
        'errorMessage',
        'error',
        'status',
        'updatedBy'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return JobErrorLog::class;
    }
}
