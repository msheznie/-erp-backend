<?php

namespace App\Repositories;

use App\Models\DepreciationPeriodsReferredHistory;
use App\Repositories\BaseRepository;

/**
 * Class DepreciationPeriodsReferredHistoryRepository
 * @package App\Repositories
 * @version December 7, 2018, 5:59 am UTC
 *
 * @method DepreciationPeriodsReferredHistory findWithoutFail($id, $columns = ['*'])
 * @method DepreciationPeriodsReferredHistory find($id, $columns = ['*'])
 * @method DepreciationPeriodsReferredHistory first($columns = ['*'])
*/
class DepreciationPeriodsReferredHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'DepreciationPeriodsID',
        'depMasterAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'faFinanceCatID',
        'faMainCategory',
        'faSubCategory',
        'faID',
        'faCode',
        'assetDescription',
        'depMonth',
        'depPercent',
        'COSTUNIT',
        'costUnitRpt',
        'FYID',
        'depForFYStartDate',
        'depForFYEndDate',
        'FYperiodID',
        'depForFYperiodStartDate',
        'depForFYperiodEndDate',
        'depMonthYear',
        'depAmountLocalCurr',
        'depAmountLocal',
        'depAmountRptCurr',
        'depAmountRpt',
        'depDoneYN',
        'timesReferred',
        'createdUserSystemID',
        'createdBy',
        'createdPCid',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DepreciationPeriodsReferredHistory::class;
    }
}
