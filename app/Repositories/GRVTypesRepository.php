<?php

namespace App\Repositories;

use App\Models\GRVTypes;
use App\Repositories\BaseRepository;

/**
 * Class GRVTypesRepository
 * @package App\Repositories
 * @version June 12, 2018, 6:21 am UTC
 *
 * @method GRVTypes findWithoutFail($id, $columns = ['*'])
 * @method GRVTypes find($id, $columns = ['*'])
 * @method GRVTypes first($columns = ['*'])
*/
class GRVTypesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'idERP_GrvTpes',
        'des'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return GRVTypes::class;
    }
}
