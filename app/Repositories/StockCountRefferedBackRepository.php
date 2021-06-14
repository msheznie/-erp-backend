<?php

namespace App\Repositories;

use App\Models\StockCountRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StockCountRefferedBackRepository
 * @package App\Repositories
 * @version June 14, 2021, 2:02 pm +04
 *
 * @method StockCountRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method StockCountRefferedBack find($id, $columns = ['*'])
 * @method StockCountRefferedBack first($columns = ['*'])
*/
class StockCountRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'stockCountAutoID',
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
        'stockCountCode',
        'refNo',
        'stockCountDate',
        'location',
        'comment',
        'stockCountType',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
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
        return StockCountRefferedBack::class;
    }
}
