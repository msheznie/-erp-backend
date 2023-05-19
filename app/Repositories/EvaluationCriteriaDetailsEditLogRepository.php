<?php

namespace App\Repositories;

use App\Models\EvaluationCriteriaDetailsEditLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EvaluationCriteriaDetailsEditLogRepository
 * @package App\Repositories
 * @version April 10, 2023, 11:33 am +04
 *
 * @method EvaluationCriteriaDetailsEditLog findWithoutFail($id, $columns = ['*'])
 * @method EvaluationCriteriaDetailsEditLog find($id, $columns = ['*'])
 * @method EvaluationCriteriaDetailsEditLog first($columns = ['*'])
*/
class EvaluationCriteriaDetailsEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'answer_type_id',
        'critera_type_id',
        'description',
        'is_final_level',
        'level',
        'master_id',
        'max_value',
        'min_value',
        'modify_type',
        'parent_id',
        'passing_weightage',
        'ref_log_id',
        'sort_order',
        'tender_id',
        'weightage'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EvaluationCriteriaDetailsEditLog::class;
    }
}
