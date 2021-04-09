<?php

namespace App\Repositories;

use App\Models\EmployeeDesignation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EmployeeDesignationRepository
 * @package App\Repositories
 * @version April 9, 2021, 12:41 pm +04
 *
 * @method EmployeeDesignation findWithoutFail($id, $columns = ['*'])
 * @method EmployeeDesignation find($id, $columns = ['*'])
 * @method EmployeeDesignation first($columns = ['*'])
*/
class EmployeeDesignationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'EmpID',
        'DesignationID',
        'startDate',
        'endDate',
        'PrincipalCategoryID',
        'SectionID',
        'DepartmentID',
        'isMajor',
        'SubjectID',
        'ClassID',
        'GroupID',
        'Erp_companyID',
        'SchMasterID',
        'BranchID',
        'AcademicYearID',
        'DateFrom',
        'DateTo',
        'isActive',
        'CreatedUserName',
        'CreatedDate',
        'CreatedPC',
        'ModifiedUserName',
        'Timestamp',
        'ModifiedPC',
        'DesignationTypeID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EmployeeDesignation::class;
    }
}
