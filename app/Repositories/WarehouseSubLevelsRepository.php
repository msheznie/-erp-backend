<?php

namespace App\Repositories;

use App\Models\WarehouseSubLevels;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class WarehouseSubLevelsRepository
 * @package App\Repositories
 * @version April 2, 2020, 1:25 pm +04
 *
 * @method WarehouseSubLevels findWithoutFail($id, $columns = ['*'])
 * @method WarehouseSubLevels find($id, $columns = ['*'])
 * @method WarehouseSubLevels first($columns = ['*'])
*/
class WarehouseSubLevelsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'warehouse_id',
        'level',
        'parent_id',
        'name',
        'description',
        'isFinalLevel',
        'created_by',
        'created_pc',
        'updated_by',
        'updated_pc'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return WarehouseSubLevels::class;
    }
}
