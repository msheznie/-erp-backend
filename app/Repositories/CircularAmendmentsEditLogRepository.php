<?php

namespace App\Repositories;

use App\Models\CircularAmendmentsEditLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CircularAmendmentsEditLogRepository
 * @package App\Repositories
 * @version April 11, 2023, 1:34 pm +04
 *
 * @method CircularAmendmentsEditLog findWithoutFail($id, $columns = ['*'])
 * @method CircularAmendmentsEditLog find($id, $columns = ['*'])
 * @method CircularAmendmentsEditLog first($columns = ['*'])
*/
class CircularAmendmentsEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'amendment_id',
        'circular_id',
        'master_id',
        'modify_type',
        'ref_log_id',
        'status',
        'tender_id',
        'vesion_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CircularAmendmentsEditLog::class;
    }
}
