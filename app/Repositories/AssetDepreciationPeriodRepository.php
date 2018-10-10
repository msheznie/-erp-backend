<?php

namespace App\Repositories;

use App\Models\AssetDepreciationPeriod;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AssetDepreciationPeriodRepository
 * @package App\Repositories
 * @version October 10, 2018, 11:40 am UTC
 *
 * @method AssetDepreciationPeriod findWithoutFail($id, $columns = ['*'])
 * @method AssetDepreciationPeriod find($id, $columns = ['*'])
 * @method AssetDepreciationPeriod first($columns = ['*'])
*/
class AssetDepreciationPeriodRepository extends BaseRepository
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
        return AssetDepreciationPeriod::class;
    }
}
