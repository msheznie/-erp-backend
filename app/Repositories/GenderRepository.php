<?php

namespace App\Repositories;

use App\Models\Gender;
use App\Repositories\BaseRepository;

/**
 * Class GenderRepository
 * @package App\Repositories
 * @version August 28, 2019, 4:14 pm +04
 *
 * @method Gender findWithoutFail($id, $columns = ['*'])
 * @method Gender find($id, $columns = ['*'])
 * @method Gender first($columns = ['*'])
*/
class GenderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'localizedValue',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Gender::class;
    }
}
