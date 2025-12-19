<?php

namespace App\Repositories;

use App\Models\RoleRoute;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RoleRouteRepository
 * @package App\Repositories
 * @version October 31, 2022, 1:29 pm +04
 *
 * @method RoleRoute findWithoutFail($id, $columns = ['*'])
 * @method RoleRoute find($id, $columns = ['*'])
 * @method RoleRoute first($columns = ['*'])
*/
class RoleRouteRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'routeName',
        'userGroupID',
        'companySystemID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RoleRoute::class;
    }
}
