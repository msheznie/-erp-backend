<?php

namespace App\Repositories;

use App\Models\PurchaseOrderDetails;
use App\Repositories\BaseRepository;

/**
 * Class PurchaseOrderDetailsRepository
 * @package App\Repositories
 * @version March 12, 2018, 5:27 am UTC
 *
 * @method PurchaseOrderDetails findWithoutFail($id, $columns = ['*'])
 * @method PurchaseOrderDetails find($id, $columns = ['*'])
 * @method PurchaseOrderDetails first($columns = ['*'])
*/
class PurchaseOrderDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'departmentID',
        'serviceLineCode',
        'purchaseOrderMasterID',
        'POProcessMasterID',
        'WO_purchaseOrderMasterID',
        'WP_purchaseOrderDetailsID',
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
        'supplierPartNumber',
        'unitOfMeasure',
        'itemClientReferenceNumberMasterID',
        'clientReferenceNumber',
        'noQty',
        'noOfDays',
        'unitCost',
        'discountPercentage',
        'discountAmount',
        'netAmount',
        'budgetYear',
        'prBelongsYear',
        'isAccrued',
        'budjetAmtLocal',
        'budjetAmtRpt',
        'comment',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierItemCurrencyID',
        'foreignToLocalER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'addonDistCost',
        'GRVcostPerUnitLocalCur',
        'GRVcostPerUnitSupDefaultCur',
        'GRVcostPerUnitSupTransCur',
        'GRVcostPerUnitComRptCur',
        'addonPurchaseReturnCost',
        'purchaseRetcostPerUnitLocalCur',
        'purchaseRetcostPerUniSupDefaultCur',
        'purchaseRetcostPerUnitTranCur',
        'purchaseRetcostPerUnitRptCur',
        'GRVSelectedYN',
        'goodsRecievedYN',
        'logisticSelectedYN',
        'logisticRecievedYN',
        'isAccruedYN',
        'accrualJVID',
        'timesReferred',
        'totalWHTAmount',
        'WHTBearedBySupplier',
        'WHTBearedByCompany',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'vatSubCategoryID',
        'vatMasterCategoryID',
        'timeStamp',
        'altUnit',
        'altUnitValue'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseOrderDetails::class;
    }
}
