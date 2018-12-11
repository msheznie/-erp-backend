<?php

namespace App\Repositories;

use App\Models\DepreciationMasterReferredHistory;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DepreciationMasterReferredHistoryRepository
 * @package App\Repositories
 * @version December 7, 2018, 5:20 am UTC
 *
 * @method DepreciationMasterReferredHistory findWithoutFail($id, $columns = ['*'])
 * @method DepreciationMasterReferredHistory find($id, $columns = ['*'])
 * @method DepreciationMasterReferredHistory first($columns = ['*'])
*/
class DepreciationMasterReferredHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'depMasterAutoID',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'depCode',
        'depDate',
        'depMonthYear',
        'depLocalCur',
        'depAmountLocal',
        'depRptCur',
        'depAmountRpt',
        'timesReferred',
        'refferedBackYN',
        'RollLevForApp_curr',
        'isDepProcessingYN',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByEmpName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'createdUserID',
        'createdUserSystemID',
        'createdPCID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DepreciationMasterReferredHistory::class;
    }
}
