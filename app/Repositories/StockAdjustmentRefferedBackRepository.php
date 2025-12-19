<?php

namespace App\Repositories;

use App\Models\StockAdjustmentRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StockAdjustmentRefferedBackRepository
 * @package App\Repositories
 * @version February 6, 2019, 11:23 am +04
 *
 * @method StockAdjustmentRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method StockAdjustmentRefferedBack find($id, $columns = ['*'])
 * @method StockAdjustmentRefferedBack first($columns = ['*'])
*/
class StockAdjustmentRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'stockAdjustmentAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'serialNo',
        'stockAdjustmentCode',
        'refNo',
        'stockAdjustmentDate',
        'location',
        'comment',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'refferedBackYN',
        'timesReferred',
        'createdDateTime',
        'createdUserGroup',
        'createdPCid',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp',
        'RollLevForApp_curr'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockAdjustmentRefferedBack::class;
    }
}
