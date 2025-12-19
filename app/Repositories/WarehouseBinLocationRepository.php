<?php

namespace App\Repositories;

use App\Models\WarehouseBinLocation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class WarehouseBinLocationRepository
 * @package App\Repositories
 * @version September 7, 2018, 11:34 am UTC
 *
 * @method WarehouseBinLocation findWithoutFail($id, $columns = ['*'])
 * @method WarehouseBinLocation find($id, $columns = ['*'])
 * @method WarehouseBinLocation first($columns = ['*'])
*/
class WarehouseBinLocationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'binLocationDes',
        'companySystemID',
        'companyID',
        'wareHouseSystemCode',
        'createdBy',
        'dateCreated',
        'isActive',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return WarehouseBinLocation::class;
    }
}
