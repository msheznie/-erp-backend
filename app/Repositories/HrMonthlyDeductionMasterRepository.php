<?php

namespace App\Repositories;

use App\Models\HrMonthlyDeductionMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HrMonthlyDeductionMasterRepository
 * @package App\Repositories
 * @version July 29, 2021, 5:15 pm +04
 *
 * @method HrMonthlyDeductionMaster findWithoutFail($id, $columns = ['*'])
 * @method HrMonthlyDeductionMaster find($id, $columns = ['*'])
 * @method HrMonthlyDeductionMaster first($columns = ['*'])
*/
class HrMonthlyDeductionMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'monthlyDeductionCode',
        'serialNo',
        'documentID',
        'payrollGroup',
        'description',
        'currencyID',
        'currency',
        'dateMD',
        'isNonPayroll',
        'isProcessed',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'currentApprovalLevel',
        'approvedYN',
        'approvedDate',
        'currentLevelNo',
        'approvedbyEmpID',
        'approvedbyEmpName',
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
        return HrMonthlyDeductionMaster::class;
    }
}
