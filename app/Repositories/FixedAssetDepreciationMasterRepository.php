<?php

namespace App\Repositories;

use App\Models\FixedAssetDepreciationMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FixedAssetDepreciationMasterRepository
 * @package App\Repositories
 * @version October 12, 2018, 6:16 am UTC
 *
 * @method FixedAssetDepreciationMaster findWithoutFail($id, $columns = ['*'])
 * @method FixedAssetDepreciationMaster find($id, $columns = ['*'])
 * @method FixedAssetDepreciationMaster first($columns = ['*'])
*/
class FixedAssetDepreciationMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'depCode',
        'depDate',
        'depMonthYear',
        'depLocalCur',
        'depAmountLocal',
        'depRptCur',
        'depAmountRpt',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByEmpName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'createdUserID',
        'createdPCID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FixedAssetDepreciationMaster::class;
    }
}
