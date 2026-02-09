<?php

namespace App\Repositories;

use App\Models\PurchaseOrderProcessDetails;
use App\Repositories\BaseRepository;

/**
 * Class PurchaseOrderProcessDetailsRepository
 * @package App\Repositories
 * @version April 12, 2018, 4:32 am UTC
 *
 * @method PurchaseOrderProcessDetails findWithoutFail($id, $columns = ['*'])
 * @method PurchaseOrderProcessDetails find($id, $columns = ['*'])
 * @method PurchaseOrderProcessDetails first($columns = ['*'])
*/
class PurchaseOrderProcessDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'POProcessMasterID',
        'purchaseRequestID',
        'purchaseRequestDetailsID',
        'poDeliveryLocation',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'comments',
        'quantityRequested',
        'orderedQty',
        'supplierPOqty',
        'supplierCost',
        'selectedSupplier',
        'catalogueMasterID',
        'catalogueDetailID',
        'partNumber',
        'itemClientReferenceNumberMasterID',
        'clientReferenceNumber',
        'localCurrencyID',
        'companyReportingCurrencyID',
        'companyReportingER',
        'selectedForPO',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'isAccrued',
        'budgetYear',
        'prBelongsYear',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseOrderProcessDetails::class;
    }
}
