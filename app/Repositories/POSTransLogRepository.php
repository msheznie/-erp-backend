<?php

namespace App\Repositories;

use App\Models\POSTransLog;
use App\Repositories\BaseRepository;

/**
 * Class POSTransLogRepository
 * @package App\Repositories
 * @version July 19, 2022, 9:59 am +04
 *
 * @method POSTransLog findWithoutFail($id, $columns = ['*'])
 * @method POSTransLog find($id, $columns = ['*'])
 * @method POSTransLog first($columns = ['*'])
*/
class POSTransLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'pos_mapping_id',
        'created_by',
        'created_date',
        'updated_by',
        'updated_date',
        'status'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSTransLog::class;
    }
}
