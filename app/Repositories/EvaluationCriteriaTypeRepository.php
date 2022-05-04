<?php

namespace App\Repositories;

use App\Models\EvaluationCriteriaType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EvaluationCriteriaTypeRepository
 * @package App\Repositories
 * @version April 22, 2022, 9:33 am +04
 *
 * @method EvaluationCriteriaType findWithoutFail($id, $columns = ['*'])
 * @method EvaluationCriteriaType find($id, $columns = ['*'])
 * @method EvaluationCriteriaType first($columns = ['*'])
*/
class EvaluationCriteriaTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'criteria',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EvaluationCriteriaType::class;
    }
}
