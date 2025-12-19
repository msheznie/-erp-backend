<?php

namespace App\Repositories;

use App\Models\WarehouseRights;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class WarehouseRightsRepository
 * @package App\Repositories
 * @version February 20, 2020, 9:51 am +04
 *
 * @method WarehouseRights findWithoutFail($id, $columns = ['*'])
 * @method WarehouseRights find($id, $columns = ['*'])
 * @method WarehouseRights first($columns = ['*'])
*/
class WarehouseRightsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'timestamp',
        'modifiedDateTime',
        'modifiedPcID',
        'modifiedUserSystemID',
        'createdDateTime',
        'createdPcID',
        'createdUserSystemID',
        'wareHouseSystemCode',
        'companySystemID',
        'employeeSystemID',
        'companyrightsID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return WarehouseRights::class;
    }
}
