<?php

namespace App\Repositories;

use App\Models\WarehouseMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class WarehouseMasterRepository
 * @package App\Repositories
 * @version March 15, 2018, 7:30 am UTC
 *
 * @method WarehouseMaster findWithoutFail($id, $columns = ['*'])
 * @method WarehouseMaster find($id, $columns = ['*'])
 * @method WarehouseMaster first($columns = ['*'])
*/
class WarehouseMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'wareHouseCode',
        'wareHouseDescription',
        'wareHouseLocation',
        'isActive',
        'companyID',
        'companySystemID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return WarehouseMaster::class;
    }
}
