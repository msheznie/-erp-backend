<?php

namespace App\Repositories;

use App\Models\EnvelopType;
use App\Repositories\BaseRepository;

/**
 * Class EnvelopTypeRepository
 * @package App\Repositories
 * @version March 10, 2022, 2:10 pm +04
 *
 * @method EnvelopType findWithoutFail($id, $columns = ['*'])
 * @method EnvelopType find($id, $columns = ['*'])
 * @method EnvelopType first($columns = ['*'])
*/
class EnvelopTypeRepository extends BaseRepository
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
        return EnvelopType::class;
    }
}
