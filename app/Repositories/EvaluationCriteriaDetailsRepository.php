<?php

namespace App\Repositories;

use App\Models\EvaluationCriteriaDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EvaluationCriteriaDetailsRepository
 * @package App\Repositories
 * @version April 22, 2022, 9:29 am +04
 *
 * @method EvaluationCriteriaDetails findWithoutFail($id, $columns = ['*'])
 * @method EvaluationCriteriaDetails find($id, $columns = ['*'])
 * @method EvaluationCriteriaDetails first($columns = ['*'])
*/
class EvaluationCriteriaDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'parent_id',
        'description',
        'critera_type_id',
        'answer_type_id',
        'level',
        'is_final_level',
        'weightage',
        'passing_weightage',
        'sort_order',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EvaluationCriteriaDetails::class;
    }
}
