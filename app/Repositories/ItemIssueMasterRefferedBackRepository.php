<?php

namespace App\Repositories;

use App\Models\ItemIssueMasterRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class ItemIssueMasterRefferedBackRepository
 * @package App\Repositories
 * @version December 3, 2018, 10:39 am UTC
 *
 * @method ItemIssueMasterRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method ItemIssueMasterRefferedBack find($id, $columns = ['*'])
 * @method ItemIssueMasterRefferedBack first($columns = ['*'])
*/
class ItemIssueMasterRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemIssueAutoID',
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
        'itemIssueCode',
        'issueType',
        'issueDate',
        'wareHouseFrom',
        'wareHouseFromCode',
        'wareHouseFromDes',
        'contractUIID',
        'contractID',
        'jobNo',
        'workOrderNo',
        'purchaseOrderNo',
        'networkNo',
        'itemDeliveredOnSiteDate',
        'customerSystemID',
        'customerID',
        'issueRefNo',
        'reqDocID',
        'reqByID',
        'reqByName',
        'reqDate',
        'reqComment',
        'wellLocationFieldID',
        'fieldShortCode',
        'fieldName',
        'wellNO',
        'comment',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'directReqByID',
        'directReqByName',
        'product',
        'volume',
        'strength',
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
        'contRefNo',
        'is_closed',
        'RollLevForApp_curr',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemIssueMasterRefferedBack::class;
    }
}
