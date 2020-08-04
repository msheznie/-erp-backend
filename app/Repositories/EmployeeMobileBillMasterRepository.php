<?php

namespace App\Repositories;

use App\Models\EmployeeMobileBillMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EmployeeMobileBillMasterRepository
 * @package App\Repositories
 * @version July 20, 2020, 2:08 pm +04
 *
 * @method EmployeeMobileBillMaster findWithoutFail($id, $columns = ['*'])
 * @method EmployeeMobileBillMaster find($id, $columns = ['*'])
 * @method EmployeeMobileBillMaster first($columns = ['*'])
*/
class EmployeeMobileBillMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'mobilebillMasterID',
        'companySysID',
        'companyID',
        'employeeSystemID',
        'empID',
        'mobileNo',
        'isSubmited',
        'totalAmount',
        'deductionAmount',
        'exceededAmount',
        'officialAmount',
        'personalAmount',
        'creditLimit',
        'submittedBySysID',
        'submittedby',
        'submittedpc',
        'createDate',
        'createUserID',
        'createPCID',
        'modifiedpc',
        'modifiedUser',
        'timestamp',
        'approvedYN',
        'approvedBySysID',
        'approvedBy',
        'approvedDate',
        'hrApprovedYN',
        'hrApprovedBySystemID',
        'hrApprovedBy',
        'hrApprovedDate',
        'managerApprovedYN',
        'managerApprovedBy',
        'managerApprovedDate',
        'RollLevForApp_curr',
        'isDeductedYN'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EmployeeMobileBillMaster::class;
    }
}
