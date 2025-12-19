<?php

namespace App\Repositories;

use App\Models\FreeBilling;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FreeBillingRepository
 * @package App\Repositories
 * @version September 17, 2018, 12:33 pm UTC
 *
 * @method FreeBilling findWithoutFail($id, $columns = ['*'])
 * @method FreeBilling find($id, $columns = ['*'])
 * @method FreeBilling first($columns = ['*'])
*/
class FreeBillingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'billProcessNo',
        'TicketNo',
        'motID',
        'mitID',
        'AssetUnitID',
        'assetSerialNo',
        'unitID',
        'rateCurrencyID',
        'StandardTimeOnLoc',
        'StandardTimeOnLocInitial',
        'standardRate',
        'operationTimeOnLoc',
        'operationTimeOnLocInitial',
        'operationRate',
        'UsageTimeOnLoc',
        'UsageTimeOnLocInitial',
        'usageRate',
        'lostInHoleYN',
        'lostInHoleYNinitial',
        'lostInHoleRate',
        'lihDate',
        'dbrYN',
        'dbrYNinitial',
        'dbrRate',
        'performaInvoiceNo',
        'InvoiceNo',
        'usedYN',
        'usedYNinitial',
        'ContractDetailID',
        'lihInspectionStartedYN',
        'dbrInspectionStartedYN',
        'mitQty',
        'assetDescription',
        'motDate',
        'mitDate',
        'rentalStartDate',
        'rentalEndDate',
        'assetDescriptionAmend',
        'amendHistory',
        'stdGLcode',
        'operatingGLcode',
        'usageGLcode',
        'lihGLcode',
        'dbrGLcode',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'qtyServiceProduct',
        'opPerformaCaptionLink',
        'timeStamp',
        'unitOP',
        'unitUsage',
        'unitLIH',
        'unitDBR',
        'companyID',
        'serviceLine',
        'UsageLinkID',
        'subContDetID',
        'subContDetails',
        'usageType',
        'usageTypeDes',
        'ticketDetDes',
        'groupOnRptYN',
        'isConsumable',
        'motDetailID',
        'freeBillingComment',
        'StbHrRate',
        'OpHrRate'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FreeBilling::class;
    }
}
