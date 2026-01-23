<?php

namespace App\Repositories;

use App\Models\FixedAssetDepreciationPeriod;
use App\Repositories\BaseRepository;

/**
 * Class FixedAssetDepreciationPeriodRepository
 * @package App\Repositories
 * @version September 27, 2018, 6:53 am UTC
 *
 * @method FixedAssetDepreciationPeriod findWithoutFail($id, $columns = ['*'])
 * @method FixedAssetDepreciationPeriod find($id, $columns = ['*'])
 * @method FixedAssetDepreciationPeriod first($columns = ['*'])
*/
class FixedAssetDepreciationPeriodRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        return FixedAssetDepreciationPeriod::class;
    }
}
