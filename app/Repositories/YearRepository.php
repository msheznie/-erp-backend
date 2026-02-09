<?php

namespace App\Repositories;

use App\Models\Year;
use App\Repositories\BaseRepository;

/**
 * Class YearRepository
 * @package App\Repositories
 * @version July 12, 2018, 5:54 am UTC
 *
 * @method Year findWithoutFail($id, $columns = ['*'])
 * @method Year find($id, $columns = ['*'])
 * @method Year first($columns = ['*'])
*/
class YearRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'year',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Year::class;
    }
}
