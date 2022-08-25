<?php

namespace App\Repositories;

use App\Models\PoCutoffJob;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PoCutoffJobRepository
 * @package App\Repositories
 * @version August 17, 2022, 9:08 am +04
 *
 * @method PoCutoffJob findWithoutFail($id, $columns = ['*'])
 * @method PoCutoffJob find($id, $columns = ['*'])
 * @method PoCutoffJob first($columns = ['*'])
*/
class PoCutoffJobRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'poCount',
        'jobCount'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PoCutoffJob::class;
    }
}
