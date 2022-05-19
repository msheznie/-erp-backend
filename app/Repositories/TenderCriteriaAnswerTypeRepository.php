<?php

namespace App\Repositories;

use App\Models\TenderCriteriaAnswerType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderCriteriaAnswerTypeRepository
 * @package App\Repositories
 * @version April 22, 2022, 10:25 am +04
 *
 * @method TenderCriteriaAnswerType findWithoutFail($id, $columns = ['*'])
 * @method TenderCriteriaAnswerType find($id, $columns = ['*'])
 * @method TenderCriteriaAnswerType first($columns = ['*'])
*/
class TenderCriteriaAnswerTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'type'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderCriteriaAnswerType::class;
    }
}
