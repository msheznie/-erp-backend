<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Repositories\BaseRepository;

/**
 * Class EmployeeRepository
 * @package App\Repositories
 * @version February 13, 2018, 8:41 am UTC
 *
 * @method Employee findWithoutFail($id, $columns = ['*'])
 * @method Employee find($id, $columns = ['*'])
 * @method Employee first($columns = ['*'])
*/
class EmployeeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'empID',
        'serial',
        'empLeadingText',
        'empUserName',
        'empTitle',
        'empInitial',
        'empName',
        'empName_O',
        'empFullName',
        'empSurname',
        'empSurname_O',
        'empFirstName',
        'empFirstName_O',
        'empFamilyName',
        'empFamilyName_O',
        'empFatherName',
        'empFatherName_O',
        'empManagerAttached',
        'empDateRegistered',
        'empTelOffice',
        'empTelMobile',
        'empLandLineNo',
        'extNo',
        'empFax',
        'empEmail',
        'empLocation',
        'empDateTerminated',
        'empLoginActive',
        'empActive',
        'userGroupID',
        'empCompanyID',
        'religion',
        'isLoggedIn',
        'isLoggedOutFailYN',
        'logingFlag',
        'isSuperAdmin',
        'discharegedYN',
        'hrusergroupID',
        'isConsultant',
        'isTrainee',
        'is3rdParty',
        '3rdPartyCompanyName',
        'gender',
        'designation',
        'nationality',
        'isManager',
        'isApproval',
        'isDashBoard',
        'isAdmin',
        'isBasicUser',
        'ActivationCode',
        'ActivationFlag',
        'isHR_admin',
        'isLock',
        'opRptManagerAccess',
        'isSupportAdmin',
        'isHSEadmin',
        'excludeObjectivesYN',
        'machineID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Employee::class;
    }
}
