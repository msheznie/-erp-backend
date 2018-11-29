<?php

namespace App\Repositories;

use App\Models\StockTransferRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StockTransferRefferedBackRepository
 * @package App\Repositories
 * @version November 29, 2018, 5:36 am UTC
 *
 * @method StockTransferRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method StockTransferRefferedBack find($id, $columns = ['*'])
 * @method StockTransferRefferedBack first($columns = ['*'])
*/
class StockTransferRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'stockTransferAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'documentSystemID',
        'documentID',
        'serialNo',
        'stockTransferCode',
        'refNo',
        'tranferDate',
        'comment',
        'companyFromSystemID',
        'companyFrom',
        'companyToSystemID',
        'companyTo',
        'locationTo',
        'locationFrom',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'postedDate',
        'fullyReceived',
        'timesReferred',
        'interCompanyTransferYN',
        'RollLevForApp_curr',
        'refferedBackYN',
        'createdDateTime',
        'createdUserGroup',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUser',
        'modifiedUserSystemID',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockTransferRefferedBack::class;
    }
}
