<?php

namespace App\Repositories;

use App\Models\HrmsDepartmentMaster;
use App\Repositories\BaseRepository;

/**
 * Class HrmsDepartmentMasterRepository
 * @package App\Repositories
 * @version February 26, 2020, 10:00 am +04
 *
 * @method HrmsDepartmentMaster findWithoutFail($id, $columns = ['*'])
 * @method HrmsDepartmentMaster find($id, $columns = ['*'])
 * @method HrmsDepartmentMaster first($columns = ['*'])
*/
class HrmsDepartmentMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'serviceLineSystemID',
        'DepartmentDescription',
        'isActive',
        'ServiceLineCode',
        'CompanyID',
        'showInCombo',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HrmsDepartmentMaster::class;
    }
}
