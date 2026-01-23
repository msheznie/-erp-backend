<?php

namespace App\Repositories;

use App\Models\Route;
use App\Repositories\BaseRepository;

/**
 * Class RouteRepository
 * @package App\Repositories
 * @version October 28, 2022, 9:27 am +04
 *
 * @method Route findWithoutFail($id, $columns = ['*'])
 * @method Route find($id, $columns = ['*'])
 * @method Route first($columns = ['*'])
*/
class RouteRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'method',
        'action',
        'uri'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Route::class;
    }
}
