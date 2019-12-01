<?php

namespace App\Repositories;

use App\Models\employeeDepartmentDelegation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class employeeDepartmentDelegationRepository
 * @package App\Repositories
 * @version November 26, 2019, 1:34 pm +04
 *
 * @method employeeDepartmentDelegation findWithoutFail($id, $columns = ['*'])
 * @method employeeDepartmentDelegation find($id, $columns = ['*'])
 * @method employeeDepartmentDelegation first($columns = ['*'])
*/
class employeeDepartmentDelegationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'departmentSystemID',
        'departmentID',
        'documentSystemID',
        'documentID',
        'empSystemID',
        'empID',
        'employeeName',
        'empEmailID',
        'sendEmailNotificationForPayment',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return employeeDepartmentDelegation::class;
    }
}
