<?php

namespace App\Repositories;

use App\Models\Priority;
use App\Repositories\BaseRepository;

/**
 * Class PriorityRepository
 * @package App\Repositories
 * @version March 26, 2018, 10:51 am UTC
 *
 * @method Priority findWithoutFail($id, $columns = ['*'])
 * @method Priority find($id, $columns = ['*'])
 * @method Priority first($columns = ['*'])
*/
class PriorityRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'priorityDescription'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Priority::class;
    }
}
