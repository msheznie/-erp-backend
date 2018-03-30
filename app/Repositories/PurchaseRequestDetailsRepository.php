<?php

namespace App\Repositories;

use App\Models\PurchaseRequestDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PurchaseRequestDetailsRepository
 * @package App\Repositories
 * @version March 29, 2018, 11:41 am UTC
 *
 * @method PurchaseRequestDetails findWithoutFail($id, $columns = ['*'])
 * @method PurchaseRequestDetails find($id, $columns = ['*'])
 * @method PurchaseRequestDetails first($columns = ['*'])
*/
class PurchaseRequestDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'purchaseRequestID',
        'companySystemID',
        'companyID',
        'itemCategoryID',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'partNumber',
        'quantityRequested',
        'estimatedCost',
        'totalCost',
        'budgetYear',
        'budjetAmtLocal',
        'budjetAmtRpt',
        'quantityOnOrder',
        'comments',
        'unitOfMeasure',
        'itemClientReferenceNumberMasterID',
        'clientReferenceNumber',
        'quantityInHand',
        'maxQty',
        'minQty',
        'poQuantity',
        'specificationGrade',
        'jobNo',
        'technicalDataSheetAttachment',
        'selectedForPO',
        'prClosedYN',
        'fullyOrdered',
        'poTrackingID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseRequestDetails::class;
    }
}
