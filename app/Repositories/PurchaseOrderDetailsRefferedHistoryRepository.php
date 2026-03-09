<?php

namespace App\Repositories;

use App\Models\PurchaseOrderDetailsRefferedHistory;
use App\Repositories\BaseRepository;

/**
 * Class PurchaseOrderDetailsRefferedHistoryRepository
 * @package App\Repositories
 * @version July 23, 2018, 11:50 am UTC
 *
 * @method PurchaseOrderDetailsRefferedHistory findWithoutFail($id, $columns = ['*'])
 * @method PurchaseOrderDetailsRefferedHistory find($id, $columns = ['*'])
 * @method PurchaseOrderDetailsRefferedHistory first($columns = ['*'])
*/
class PurchaseOrderDetailsRefferedHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'purchaseOrderDetailsID',
        'companySystemID',
        'companyID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLineCode',
        'purchaseOrderMasterID',
        'purchaseProcessDetailID',
        'POProcessMasterID',
        'WO_purchaseOrderMasterID',
        'WP_purchaseOrderDetailsID',
        'purchaseRequestDetailsID',
        'purchaseRequestID',
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
        'requestedQty',
        'noQty',
        'balanceQty',
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
        'receivedQty',
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
        'manuallyClosed',
        'manuallyClosedByEmpSystemID',
        'manuallyClosedByEmpID',
        'manuallyClosedByEmpName',
        'manuallyClosedDate',
        'manuallyClosedComment',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseOrderDetailsRefferedHistory::class;
    }
}
