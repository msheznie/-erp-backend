<?php

namespace App\Repositories;

use App\Models\StockReceiveRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StockReceiveRefferedBackRepository
 * @package App\Repositories
 * @version November 29, 2018, 11:24 am UTC
 *
 * @method StockReceiveRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method StockReceiveRefferedBack find($id, $columns = ['*'])
 * @method StockReceiveRefferedBack first($columns = ['*'])
*/
class StockReceiveRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'stockReceiveAutoID',
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
        'stockReceiveCode',
        'refNo',
        'receivedDate',
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
        'timesReferred',
        'interCompanyTransferYN',
        'RollLevForApp_curr',
        'createdDateTime',
        'createdUserGroup',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp',
        'refferedBackYN'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockReceiveRefferedBack::class;
    }
}
