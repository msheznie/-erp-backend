<?php

namespace App\Repositories;

use App\Models\Religion;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ReligionRepository
 * @package App\Repositories
 * @version August 28, 2019, 4:18 pm +04
 *
 * @method Religion findWithoutFail($id, $columns = ['*'])
 * @method Religion find($id, $columns = ['*'])
 * @method Religion first($columns = ['*'])
*/
class ReligionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'religionName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Religion::class;
    }
}
