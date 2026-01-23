<?php

namespace App\Repositories;

use App\Models\POSTransErrorLog;
use App\Repositories\BaseRepository;

/**
 * Class POSTransErrorLogRepository
 * @package App\Repositories
 * @version July 20, 2022, 1:29 pm +04
 *
 * @method POSTransErrorLog findWithoutFail($id, $columns = ['*'])
 * @method POSTransErrorLog find($id, $columns = ['*'])
 * @method POSTransErrorLog first($columns = ['*'])
*/
class POSTransErrorLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'log_id',
        'error'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSTransErrorLog::class;
    }
}
