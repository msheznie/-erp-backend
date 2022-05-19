<?php

namespace App\Repositories;

use App\Models\EvaluationCriteriaScoreConfig;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EvaluationCriteriaScoreConfigRepository
 * @package App\Repositories
 * @version April 28, 2022, 2:30 pm +04
 *
 * @method EvaluationCriteriaScoreConfig findWithoutFail($id, $columns = ['*'])
 * @method EvaluationCriteriaScoreConfig find($id, $columns = ['*'])
 * @method EvaluationCriteriaScoreConfig first($columns = ['*'])
*/
class EvaluationCriteriaScoreConfigRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'criteria_detail_id',
        'label',
        'score',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EvaluationCriteriaScoreConfig::class;
    }
}
