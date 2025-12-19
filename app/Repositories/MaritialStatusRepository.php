<?php

namespace App\Repositories;

use App\Models\MaritialStatus;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class MaritialStatusRepository
 * @package App\Repositories
 * @version August 28, 2019, 4:16 pm +04
 *
 * @method MaritialStatus findWithoutFail($id, $columns = ['*'])
 * @method MaritialStatus find($id, $columns = ['*'])
 * @method MaritialStatus first($columns = ['*'])
*/
class MaritialStatusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'code',
        'description',
        'description_O',
        'noOfkids',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MaritialStatus::class;
    }
}
