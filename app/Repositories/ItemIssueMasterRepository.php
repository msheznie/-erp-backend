<?php

namespace App\Repositories;

use App\Models\ItemIssueMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItemIssueMasterRepository
 * @package App\Repositories
 * @version June 20, 2018, 4:23 am UTC
 *
 * @method ItemIssueMaster findWithoutFail($id, $columns = ['*'])
 * @method ItemIssueMaster find($id, $columns = ['*'])
 * @method ItemIssueMaster first($columns = ['*'])
*/
class ItemIssueMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'contractID',
        'jobNo',
        'workOrderNo',
        'purchaseOrderNo',
        'networkNo',
        'itemDeliveredOnSiteDate',
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
        'onfirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'directReqByID',
        'directReqByName',
        'product',
        'volume',
        'strength',
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
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemIssueMaster::class;
    }
}
