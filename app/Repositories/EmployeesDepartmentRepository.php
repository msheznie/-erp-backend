<?php

namespace App\Repositories;

use App\Models\EmployeesDepartment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EmployeesDepartmentRepository
 * @package App\Repositories
 * @version April 2, 2018, 11:51 am UTC
 *
 * @method EmployeesDepartment findWithoutFail($id, $columns = ['*'])
 * @method EmployeesDepartment find($id, $columns = ['*'])
 * @method EmployeesDepartment first($columns = ['*'])
*/
class EmployeesDepartmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employeeSystemID',
        'employeeID',
        'employeeGroupID',
        'companySystemID',
        'companyId',
        'documentSystemID',
        'documentID',
        'departmentID',
        'ServiceLineSystemID',
        'ServiceLineID',
        'warehouseSystemCode',
        'reportingManagerID',
        'isDefault',
        'dischargedYN',
        'approvalDeligated',
        'approvalDeligatedFromEmpID',
        'approvalDeligatedFrom',
        'approvalDeligatedTo',
        'dmsIsUploadEnable',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EmployeesDepartment::class;
    }
}
