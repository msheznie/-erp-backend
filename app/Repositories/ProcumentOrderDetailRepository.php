<?php

namespace App\Repositories;

use App\Models\ProcumentOrderDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ProcumentOrderDetailRepository
 * @package App\Repositories
 * @version March 30, 2018, 10:52 am UTC
 *
 * @method ProcumentOrderDetail findWithoutFail($id, $columns = ['*'])
 * @method ProcumentOrderDetail find($id, $columns = ['*'])
 * @method ProcumentOrderDetail first($columns = ['*'])
*/
class ProcumentOrderDetailRepository extends BaseRepository
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
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ProcumentOrderDetail::class;
    }
}
