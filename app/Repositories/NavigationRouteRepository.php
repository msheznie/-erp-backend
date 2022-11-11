<?php

namespace App\Repositories;

use App\Models\NavigationRoute;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class NavigationRouteRepository
 * @package App\Repositories
 * @version October 28, 2022, 3:49 pm +04
 *
 * @method NavigationRoute findWithoutFail($id, $columns = ['*'])
 * @method NavigationRoute find($id, $columns = ['*'])
 * @method NavigationRoute first($columns = ['*'])
*/
class NavigationRouteRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'navigationID',
        'routeName',
        'action'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return NavigationRoute::class;
    }
}
