<?php

namespace App\Repositories;

use App\Models\PurchaseRequestReferred;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PurchaseRequestReferredRepository
 * @package App\Repositories
 * @version August 1, 2018, 6:00 am UTC
 *
 * @method PurchaseRequestReferred findWithoutFail($id, $columns = ['*'])
 * @method PurchaseRequestReferred find($id, $columns = ['*'])
 * @method PurchaseRequestReferred first($columns = ['*'])
*/
class PurchaseRequestReferredRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'purchaseRequestID',
        'companySystemID',
        'companyID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'companyJobID',
        'serialNumber',
        'purchaseRequestCode',
        'comments',
        'location',
        'priority',
        'deliveryLocation',
        'PRRequestedDate',
        'docRefNo',
        'invoiceNumber',
        'currency',
        'buyerEmpID',
        'buyerEmpSystemID',
        'buyerEmpName',
        'buyerEmpEmail',
        'supplierCodeSystem',
        'supplierName',
        'supplierAddress',
        'supplierTransactionCurrencyID',
        'supplierCountryID',
        'financeCategory',
        'PRConfirmedYN',
        'PRConfirmedBy',
        'PRConfirmedDate',
        'isActive',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp',
        'selectedForPO',
        'approved',
        'timesReferred',
        'prClosedYN'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseRequestReferred::class;
    }
}
