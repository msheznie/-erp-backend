<?php

namespace App\Repositories;

use App\Models\HrEmpDepartments;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HrEmpDepartmentsRepository
 * @package App\Repositories
 * @version June 30, 2023, 8:07 am +04
 *
 * @method HrEmpDepartments findWithoutFail($id, $columns = ['*'])
 * @method HrEmpDepartments find($id, $columns = ['*'])
 * @method HrEmpDepartments first($columns = ['*'])
*/
class HrEmpDepartmentsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'AcademicYearID',
        'BranchID',
        'CreatedDate',
        'CreatedPC',
        'CreatedUserName',
        'date_from',
        'date_to',
        'DepartmentMasterID',
        'EmpID',
        'Erp_companyID',
        'isActive',
        'isPrimary',
        'ModifiedPC',
        'ModifiedUserName',
        'SchMasterID',
        'Timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HrEmpDepartments::class;
    }
}
