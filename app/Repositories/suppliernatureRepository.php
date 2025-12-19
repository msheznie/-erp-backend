<?php

namespace App\Repositories;

use App\Models\suppliernature;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class suppliernatureRepository
 * @package App\Repositories
 * @version February 28, 2018, 4:18 am UTC
 *
 * @method suppliernature findWithoutFail($id, $columns = ['*'])
 * @method suppliernature find($id, $columns = ['*'])
 * @method suppliernature first($columns = ['*'])
*/
class suppliernatureRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'natureDescription'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return suppliernature::class;
    }
}
