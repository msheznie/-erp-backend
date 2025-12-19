<?php

namespace App\Repositories;

use App\Models\HrDepartmentMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HrDepartmentMasterRepository
 * @package App\Repositories
 * @version June 30, 2023, 12:32 pm +04
 *
 * @method HrDepartmentMaster findWithoutFail($id, $columns = ['*'])
 * @method HrDepartmentMaster find($id, $columns = ['*'])
 * @method HrDepartmentMaster first($columns = ['*'])
*/
class HrDepartmentMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'BranchID',
        'created_by',
        'CreatedDate',
        'CreatedPC',
        'CreatedUserName',
        'DepartmentDes',
        'Erp_companyID',
        'hod_id',
        'isActive',
        'ModifiedPC',
        'ModifiedUserName',
        'SchMasterID',
        'SortOrder',
        'Timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HrDepartmentMaster::class;
    }
}
