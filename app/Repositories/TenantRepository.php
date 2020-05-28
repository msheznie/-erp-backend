<?php

namespace App\Repositories;

use App\Models\Tenant;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenantRepository
 * @package App\Repositories
 * @version May 27, 2020, 11:55 am +04
 *
 * @method Tenant findWithoutFail($id, $columns = ['*'])
 * @method Tenant find($id, $columns = ['*'])
 * @method Tenant first($columns = ['*'])
*/
class TenantRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'sub_domain',
        'database'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Tenant::class;
    }
}
