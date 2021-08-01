<?php

namespace App\Repositories;

use App\Models\HrPayrollMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HrPayrollMasterRepository
 * @package App\Repositories
 * @version August 1, 2021, 10:22 am +04
 *
 * @method HrPayrollMaster findWithoutFail($id, $columns = ['*'])
 * @method HrPayrollMaster find($id, $columns = ['*'])
 * @method HrPayrollMaster first($columns = ['*'])
*/
class HrPayrollMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentID',
        'documentCode',
        'documentNo',
        'payrollGroupID',
        'periodID',
        'payrollYear',
        'payrollMonth',
        'processDate',
        'visibleDate',
        'templateID',
        'narration',
        'isBankTransferProcessed',
        'financialYearID',
        'financialPeriodID',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'currentLevelNo',
        'approvedbyEmpName',
        'approvedbyEmpID',
        'companyID',
        'companyCode',
        'segmentID',
        'segmentCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HrPayrollMaster::class;
    }
}
