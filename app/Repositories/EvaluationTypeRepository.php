<?php

namespace App\Repositories;

use App\Models\EvaluationType;
use App\Repositories\BaseRepository;

/**
 * Class EvaluationTypeRepository
 * @package App\Repositories
 * @version March 15, 2022, 9:53 am +04
 *
 * @method EvaluationType findWithoutFail($id, $columns = ['*'])
 * @method EvaluationType find($id, $columns = ['*'])
 * @method EvaluationType first($columns = ['*'])
*/
class EvaluationTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EvaluationType::class;
    }
}
