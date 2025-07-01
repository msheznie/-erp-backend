<?php

namespace App\Repositories;

use App\Models\EvacuationCriteriaScoreConfigLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EvacuationCriteriaScoreConfigLogRepository
 * @package App\Repositories
 * @version June 21, 2025, 9:33 pm +04
 *
 * @method EvacuationCriteriaScoreConfigLog findWithoutFail($id, $columns = ['*'])
 * @method EvacuationCriteriaScoreConfigLog find($id, $columns = ['*'])
 * @method EvacuationCriteriaScoreConfigLog first($columns = ['*'])
*/
class EvacuationCriteriaScoreConfigLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_by',
        'criteria_detail_id',
        'fromTender',
        'id',
        'is_deleted',
        'label',
        'level_no',
        'score',
        'updated_by',
        'version_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EvacuationCriteriaScoreConfigLog::class;
    }
}
