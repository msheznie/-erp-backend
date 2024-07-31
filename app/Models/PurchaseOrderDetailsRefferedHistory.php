<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderDetailsRefferedHistory.php
 * -- Project Name : ERP
 * -- Module Name :  PurchaseOrderDetailsRefferedHistory
 * -- Author : Nazir
 * -- Create date : 23 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PurchaseOrderDetailsRefferedHistory",
 *      required={""},
 *      @SWG\Property(
 *          property="poDetailsMasterRefferedID",
 *          description="poDetailsMasterRefferedID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseOrderDetailsID",
 *          description="purchaseOrderDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="departmentID",
 *          description="departmentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="purchaseOrderMasterID",
 *          description="purchaseOrderMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseProcessDetailID",
 *          description="purchaseProcessDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="POProcessMasterID",
 *          description="POProcessMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="WO_purchaseOrderMasterID",
 *          description="WO_purchaseOrderMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="WP_purchaseOrderDetailsID",
 *          description="WP_purchaseOrderDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseRequestDetailsID",
 *          description="purchaseRequestDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseRequestID",
 *          description="purchaseRequestID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemCode",
 *          description="itemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemPrimaryCode",
 *          description="itemPrimaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemDescription",
 *          description="itemDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemFinanceCategoryID",
 *          description="itemFinanceCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemFinanceCategorySubID",
 *          description="itemFinanceCategorySubID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodebBSSystemID",
 *          description="financeGLcodebBSSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodebBS",
 *          description="financeGLcodebBS",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodePLSystemID",
 *          description="financeGLcodePLSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodePL",
 *          description="financeGLcodePL",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="includePLForGRVYN",
 *          description="includePLForGRVYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierPartNumber",
 *          description="supplierPartNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasure",
 *          description="unitOfMeasure",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemClientReferenceNumberMasterID",
 *          description="itemClientReferenceNumberMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="clientReferenceNumber",
 *          description="clientReferenceNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="requestedQty",
 *          description="requestedQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="noQty",
 *          description="noQty",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="balanceQty",
 *          description="balanceQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="noOfDays",
 *          description="noOfDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unitCost",
 *          description="unitCost",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discountPercentage",
 *          description="discountPercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discountAmount",
 *          description="discountAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="netAmount",
 *          description="netAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="budgetYear",
 *          description="budgetYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="prBelongsYear",
 *          description="prBelongsYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAccrued",
 *          description="isAccrued",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="budjetAmtLocal",
 *          description="budjetAmtLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="budjetAmtRpt",
 *          description="budjetAmtRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultCurrencyID",
 *          description="supplierDefaultCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultER",
 *          description="supplierDefaultER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supplierItemCurrencyID",
 *          description="supplierItemCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="foreignToLocalER",
 *          description="foreignToLocalER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingER",
 *          description="companyReportingER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyID",
 *          description="localCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyER",
 *          description="localCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="addonDistCost",
 *          description="addonDistCost",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVcostPerUnitLocalCur",
 *          description="GRVcostPerUnitLocalCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVcostPerUnitSupDefaultCur",
 *          description="GRVcostPerUnitSupDefaultCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVcostPerUnitSupTransCur",
 *          description="GRVcostPerUnitSupTransCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVcostPerUnitComRptCur",
 *          description="GRVcostPerUnitComRptCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="addonPurchaseReturnCost",
 *          description="addonPurchaseReturnCost",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="purchaseRetcostPerUnitLocalCur",
 *          description="purchaseRetcostPerUnitLocalCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="purchaseRetcostPerUniSupDefaultCur",
 *          description="purchaseRetcostPerUniSupDefaultCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="purchaseRetcostPerUnitTranCur",
 *          description="purchaseRetcostPerUnitTranCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="purchaseRetcostPerUnitRptCur",
 *          description="purchaseRetcostPerUnitRptCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="receivedQty",
 *          description="receivedQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVSelectedYN",
 *          description="GRVSelectedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="goodsRecievedYN",
 *          description="goodsRecievedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="logisticSelectedYN",
 *          description="logisticSelectedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="logisticRecievedYN",
 *          description="logisticRecievedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAccruedYN",
 *          description="isAccruedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accrualJVID",
 *          description="accrualJVID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="totalWHTAmount",
 *          description="totalWHTAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="WHTBearedBySupplier",
 *          description="WHTBearedBySupplier",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="WHTBearedByCompany",
 *          description="WHTBearedByCompany",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="VATPercentage",
 *          description="VATPercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="VATAmount",
 *          description="VATAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="VATAmountLocal",
 *          description="VATAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="VATAmountRpt",
 *          description="VATAmountRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="manuallyClosed",
 *          description="manuallyClosed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="manuallyClosedByEmpSystemID",
 *          description="manuallyClosedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="manuallyClosedByEmpID",
 *          description="manuallyClosedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="manuallyClosedByEmpName",
 *          description="manuallyClosedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="manuallyClosedComment",
 *          description="manuallyClosedComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      )
 * )
 */
class PurchaseOrderDetailsRefferedHistory extends Model
{

    public $table = 'erp_purchaseorderdetailsrefferedhistory';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'poDetailsMasterRefferedID';

    public $fillable = [
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
        'markupPercentage',
        'markupTransactionAmount',
        'markupLocalAmount',
        'markupReportingAmount',
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
        'timeStamp',
        'madeLocallyYN',
        'altUnit',
        'altUnitValue',
        'detail_project_id',
        'contractID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'poDetailsMasterRefferedID' => 'integer',
        'purchaseOrderDetailsID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'departmentID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'purchaseOrderMasterID' => 'integer',
        'purchaseProcessDetailID' => 'integer',
        'POProcessMasterID' => 'integer',
        'WO_purchaseOrderMasterID' => 'integer',
        'WP_purchaseOrderDetailsID' => 'integer',
        'purchaseRequestDetailsID' => 'integer',
        'purchaseRequestID' => 'integer',
        'itemCode' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'itemFinanceCategoryID' => 'integer',
        'itemFinanceCategorySubID' => 'integer',
        'financeGLcodebBSSystemID' => 'integer',
        'financeGLcodebBS' => 'string',
        'financeGLcodePLSystemID' => 'integer',
        'financeGLcodePL' => 'string',
        'includePLForGRVYN' => 'integer',
        'supplierPartNumber' => 'string',
        'unitOfMeasure' => 'integer',
        'itemClientReferenceNumberMasterID' => 'integer',
        'clientReferenceNumber' => 'string',
        'requestedQty' => 'float',
        'noQty' => 'integer',
        'balanceQty' => 'float',
        'noOfDays' => 'integer',
        'unitCost' => 'float',
        'discountPercentage' => 'float',
        'discountAmount' => 'float',
        'netAmount' => 'float',
        'markupPercentage' => 'float',
        'markupTransactionAmount' => 'float',
        'markupLocalAmount' => 'float',
        'markupReportingAmount',
        'budgetYear' => 'integer',
        'prBelongsYear' => 'integer',
        'isAccrued' => 'integer',
        'budjetAmtLocal' => 'float',
        'budjetAmtRpt' => 'float',
        'comment' => 'string',
        'supplierDefaultCurrencyID' => 'integer',
        'supplierDefaultER' => 'float',
        'supplierItemCurrencyID' => 'integer',
        'foreignToLocalER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'addonDistCost' => 'float',
        'GRVcostPerUnitLocalCur' => 'float',
        'GRVcostPerUnitSupDefaultCur' => 'float',
        'GRVcostPerUnitSupTransCur' => 'float',
        'GRVcostPerUnitComRptCur' => 'float',
        'addonPurchaseReturnCost' => 'float',
        'purchaseRetcostPerUnitLocalCur' => 'float',
        'purchaseRetcostPerUniSupDefaultCur' => 'float',
        'purchaseRetcostPerUnitTranCur' => 'float',
        'purchaseRetcostPerUnitRptCur' => 'float',
        'receivedQty' => 'float',
        'GRVSelectedYN' => 'integer',
        'goodsRecievedYN' => 'integer',
        'logisticSelectedYN' => 'integer',
        'logisticRecievedYN' => 'integer',
        'isAccruedYN' => 'integer',
        'accrualJVID' => 'integer',
        'timesReferred' => 'integer',
        'totalWHTAmount' => 'float',
        'WHTBearedBySupplier' => 'float',
        'WHTBearedByCompany' => 'float',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'manuallyClosed' => 'integer',
        'manuallyClosedByEmpSystemID' => 'integer',
        'manuallyClosedByEmpID' => 'string',
        'manuallyClosedByEmpName' => 'string',
        'manuallyClosedComment' => 'string',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'madeLocallyYN' => 'integer',
        'altUnit'  => 'integer',
        'altUnitValue'  => 'float',
        'detail_project_id' => 'integer',
        'contractID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function unit(){
        return $this->belongsTo('App\Models\Unit','unitOfMeasure','UnitID');
    }

    
}
