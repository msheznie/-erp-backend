<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderMasterRefferedHistory.php
 * -- Project Name : ERP
 * -- Module Name :  PurchaseOrderMasterRefferedHistory
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
 *      definition="PurchaseOrderMasterRefferedHistory",
 *      required={""},
 *      @SWG\Property(
 *          property="poMasterRefferedID",
 *          description="poMasterRefferedID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseOrderID",
 *          description="purchaseOrderID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="poProcessId",
 *          description="poProcessId",
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
 *          property="serviceLine",
 *          description="serviceLine",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyAddress",
 *          description="companyAddress",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="purchaseOrderCode",
 *          description="purchaseOrderCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNumber",
 *          description="serialNumber",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierID",
 *          description="supplierID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierPrimaryCode",
 *          description="supplierPrimaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierName",
 *          description="supplierName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierAddress",
 *          description="supplierAddress",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierTelephone",
 *          description="supplierTelephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierFax",
 *          description="supplierFax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierEmail",
 *          description="supplierEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="creditPeriod",
 *          description="creditPeriod",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="narration",
 *          description="narration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="poLocation",
 *          description="poLocation",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeCategory",
 *          description="financeCategory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="referenceNumber",
 *          description="referenceNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="shippingAddressID",
 *          description="shippingAddressID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="shippingAddressDescriprion",
 *          description="shippingAddressDescriprion",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invoiceToAddressID",
 *          description="invoiceToAddressID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceToAddressDescription",
 *          description="invoiceToAddressDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="soldToAddressID",
 *          description="soldToAddressID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="soldToAddressDescriprion",
 *          description="soldToAddressDescriprion",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="paymentTerms",
 *          description="paymentTerms",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="deliveryTerms",
 *          description="deliveryTerms",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="panaltyTerms",
 *          description="panaltyTerms",
 *          type="string"
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
 *          property="supplierTransactionCurrencyID",
 *          description="supplierTransactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionER",
 *          description="supplierTransactionER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="poConfirmedYN",
 *          description="poConfirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="poConfirmedByEmpSystemID",
 *          description="poConfirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="poConfirmedByEmpID",
 *          description="poConfirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="poConfirmedByName",
 *          description="poConfirmedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="poCancelledYN",
 *          description="poCancelledYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="poCancelledBySystemID",
 *          description="poCancelledBySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="poCancelledBy",
 *          description="poCancelledBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="poCancelledByName",
 *          description="poCancelledByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="cancelledComments",
 *          description="cancelledComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="poTotalComRptCurrency",
 *          description="poTotalComRptCurrency",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="poTotalLocalCurrency",
 *          description="poTotalLocalCurrency",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="poTotalSupplierDefaultCurrency",
 *          description="poTotalSupplierDefaultCurrency",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="poTotalSupplierTransactionCurrency",
 *          description="poTotalSupplierTransactionCurrency",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="poDiscountPercentage",
 *          description="poDiscountPercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="poDiscountAmount",
 *          description="poDiscountAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supplierVATEligible",
 *          description="supplierVATEligible",
 *          type="integer",
 *          format="int32"
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
 *          property="shipTocontactPersonID",
 *          description="shipTocontactPersonID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="shipTocontactPersonTelephone",
 *          description="shipTocontactPersonTelephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="shipTocontactPersonFaxNo",
 *          description="shipTocontactPersonFaxNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="shipTocontactPersonEmail",
 *          description="shipTocontactPersonEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invoiceTocontactPersonID",
 *          description="invoiceTocontactPersonID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invoiceTocontactPersonTelephone",
 *          description="invoiceTocontactPersonTelephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invoiceTocontactPersonFaxNo",
 *          description="invoiceTocontactPersonFaxNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invoiceTocontactPersonEmail",
 *          description="invoiceTocontactPersonEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="soldTocontactPersonID",
 *          description="soldTocontactPersonID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="soldTocontactPersonTelephone",
 *          description="soldTocontactPersonTelephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="soldTocontactPersonFaxNo",
 *          description="soldTocontactPersonFaxNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="soldTocontactPersonEmail",
 *          description="soldTocontactPersonEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="priority",
 *          description="priority",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approved",
 *          description="approved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserID",
 *          description="approvedByUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserSystemID",
 *          description="approvedByUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="addOnPercent",
 *          description="addOnPercent",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="addOnDefaultPercent",
 *          description="addOnDefaultPercent",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVTrackingID",
 *          description="GRVTrackingID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="logisticDoneYN",
 *          description="logisticDoneYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="poClosedYN",
 *          description="poClosedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvRecieved",
 *          description="grvRecieved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoicedBooked",
 *          description="invoicedBooked",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
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
 *          property="poType",
 *          description="poType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="poType_N",
 *          description="poType_N",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="docRefNo",
 *          description="docRefNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sentToSupplier",
 *          description="sentToSupplier",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sentToSupplierByEmpSystemID",
 *          description="sentToSupplierByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sentToSupplierByEmpID",
 *          description="sentToSupplierByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="sentToSupplierByEmpName",
 *          description="sentToSupplierByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="budgetBlockYN",
 *          description="budgetBlockYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="budgetYear",
 *          description="budgetYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="hidePOYN",
 *          description="hidePOYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="hideByEmpSystemID",
 *          description="hideByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="hideByEmpID",
 *          description="hideByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="hideByEmpName",
 *          description="hideByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="hideComments",
 *          description="hideComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="WO_purchaseOrderID",
 *          description="WO_purchaseOrderID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="WO_NoOfAutoGenerationTimes",
 *          description="WO_NoOfAutoGenerationTimes",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="WO_NoOfGeneratedTimes",
 *          description="WO_NoOfGeneratedTimes",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="WO_fullyGenerated",
 *          description="WO_fullyGenerated",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="WO_amendYN",
 *          description="WO_amendYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="WO_amendRequestedByEmpID",
 *          description="WO_amendRequestedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="WO_amendRequestedByEmpSystemID",
 *          description="WO_amendRequestedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="WO_confirmedYN",
 *          description="WO_confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="WO_confirmedByEmpID",
 *          description="WO_confirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="WO_terminateYN",
 *          description="WO_terminateYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="WO_terminatedByEmpID",
 *          description="WO_terminatedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="WO_terminateComments",
 *          description="WO_terminateComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="partiallyGRVAllowed",
 *          description="partiallyGRVAllowed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="logisticsAvailable",
 *          description="logisticsAvailable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatRegisteredYN",
 *          description="vatRegisteredYN",
 *          type="integer",
 *          format="int32"
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
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
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
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isSelected",
 *          description="isSelected",
 *          type="boolean"
 *      )
 * )
 */
class PurchaseOrderMasterRefferedHistory extends Model
{

    public $table = 'erp_purchaseordermasterrefferedhistory';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'poMasterRefferedID';

    public $fillable = [
        'purchaseOrderID',
        'poProcessId',
        'companySystemID',
        'companyID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLine',
        'companyAddress',
        'documentSystemID',
        'documentID',
        'purchaseOrderCode',
        'serialNumber',
        'supplierID',
        'supplierPrimaryCode',
        'supplierName',
        'supplierAddress',
        'supplierTelephone',
        'supplierFax',
        'supplierEmail',
        'creditPeriod',
        'expectedDeliveryDate',
        'narration',
        'poLocation',
        'financeCategory',
        'referenceNumber',
        'shippingAddressID',
        'shippingAddressDescriprion',
        'invoiceToAddressID',
        'invoiceToAddressDescription',
        'soldToAddressID',
        'soldToAddressDescriprion',
        'paymentTerms',
        'deliveryTerms',
        'panaltyTerms',
        'localCurrencyID',
        'localCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierTransactionCurrencyID',
        'supplierTransactionER',
        'poConfirmedYN',
        'poConfirmedByEmpSystemID',
        'poConfirmedByEmpID',
        'poConfirmedByName',
        'poConfirmedDate',
        'poCancelledYN',
        'poCancelledBySystemID',
        'poCancelledBy',
        'poCancelledByName',
        'poCancelledDate',
        'cancelledComments',
        'poTotalComRptCurrency',
        'poTotalLocalCurrency',
        'poTotalSupplierDefaultCurrency',
        'poTotalSupplierTransactionCurrency',
        'poDiscountPercentage',
        'poDiscountAmount',
        'supplierVATEligible',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'shipTocontactPersonID',
        'shipTocontactPersonTelephone',
        'shipTocontactPersonFaxNo',
        'shipTocontactPersonEmail',
        'invoiceTocontactPersonID',
        'invoiceTocontactPersonTelephone',
        'invoiceTocontactPersonFaxNo',
        'invoiceTocontactPersonEmail',
        'soldTocontactPersonID',
        'soldTocontactPersonTelephone',
        'soldTocontactPersonFaxNo',
        'soldTocontactPersonEmail',
        'priority',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'addOnPercent',
        'addOnDefaultPercent',
        'GRVTrackingID',
        'logisticDoneYN',
        'poClosedYN',
        'grvRecieved',
        'invoicedBooked',
        'refferedBackYN',
        'timesReferred',
        'poType',
        'poType_N',
        'docRefNo',
        'RollLevForApp_curr',
        'sentToSupplier',
        'sentToSupplierByEmpSystemID',
        'sentToSupplierByEmpID',
        'sentToSupplierByEmpName',
        'sentToSupplierDate',
        'budgetBlockYN',
        'budgetYear',
        'hidePOYN',
        'hideByEmpSystemID',
        'hideByEmpID',
        'hideByEmpName',
        'hideDate',
        'hideComments',
        'WO_purchaseOrderID',
        'WO_PeriodFrom',
        'WO_PeriodTo',
        'WO_NoOfAutoGenerationTimes',
        'WO_NoOfGeneratedTimes',
        'WO_fullyGenerated',
        'WO_amendYN',
        'WO_amendRequestedDate',
        'WO_amendRequestedByEmpID',
        'WO_amendRequestedByEmpSystemID',
        'WO_confirmedYN',
        'WO_confirmedDate',
        'WO_confirmedByEmpID',
        'WO_terminateYN',
        'WO_terminatedDate',
        'WO_terminatedByEmpID',
        'WO_terminateComments',
        'partiallyGRVAllowed',
        'logisticsAvailable',
        'vatRegisteredYN',
        'manuallyClosed',
        'manuallyClosedByEmpSystemID',
        'manuallyClosedByEmpID',
        'manuallyClosedByEmpName',
        'manuallyClosedDate',
        'manuallyClosedComment',
        'createdUserGroup',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'isSelected',
        'timeStamp',
        'supCategoryICVMasterID',
        'supCategorySubICVID',
        'rcmActivated',
        'approval_remarks',
        'vat_number',
         'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'poMasterRefferedID' => 'integer',
        'purchaseOrderID' => 'integer',
        'poProcessId' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'departmentID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLine' => 'string',
        'companyAddress' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'purchaseOrderCode' => 'string',
        'serialNumber' => 'integer',
        'supplierID' => 'integer',
        'supplierPrimaryCode' => 'string',
        'supplierName' => 'string',
        'supplierAddress' => 'string',
        'supplierTelephone' => 'string',
        'supplierFax' => 'string',
        'supplierEmail' => 'string',
        'creditPeriod' => 'integer',
        'narration' => 'string',
        'poLocation' => 'integer',
        'financeCategory' => 'integer',
        'referenceNumber' => 'string',
        'shippingAddressID' => 'integer',
        'shippingAddressDescriprion' => 'string',
        'invoiceToAddressID' => 'integer',
        'invoiceToAddressDescription' => 'string',
        'soldToAddressID' => 'integer',
        'soldToAddressDescriprion' => 'string',
        'paymentTerms' => 'string',
        'deliveryTerms' => 'string',
        'panaltyTerms' => 'string',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'supplierDefaultCurrencyID' => 'integer',
        'supplierDefaultER' => 'float',
        'supplierTransactionCurrencyID' => 'integer',
        'supplierTransactionER' => 'float',
        'poConfirmedYN' => 'integer',
        'poConfirmedByEmpSystemID' => 'integer',
        'poConfirmedByEmpID' => 'string',
        'poConfirmedByName' => 'string',
        'poCancelledYN' => 'integer',
        'poCancelledBySystemID' => 'integer',
        'poCancelledBy' => 'string',
        'poCancelledByName' => 'string',
        'cancelledComments' => 'string',
        'poTotalComRptCurrency' => 'float',
        'poTotalLocalCurrency' => 'float',
        'poTotalSupplierDefaultCurrency' => 'float',
        'poTotalSupplierTransactionCurrency' => 'float',
        'poDiscountPercentage' => 'float',
        'poDiscountAmount' => 'float',
        'supplierVATEligible' => 'integer',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'shipTocontactPersonID' => 'string',
        'shipTocontactPersonTelephone' => 'string',
        'shipTocontactPersonFaxNo' => 'string',
        'shipTocontactPersonEmail' => 'string',
        'invoiceTocontactPersonID' => 'string',
        'invoiceTocontactPersonTelephone' => 'string',
        'invoiceTocontactPersonFaxNo' => 'string',
        'invoiceTocontactPersonEmail' => 'string',
        'soldTocontactPersonID' => 'string',
        'soldTocontactPersonTelephone' => 'string',
        'soldTocontactPersonFaxNo' => 'string',
        'soldTocontactPersonEmail' => 'string',
        'priority' => 'integer',
        'approved' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'addOnPercent' => 'float',
        'addOnDefaultPercent' => 'float',
        'GRVTrackingID' => 'integer',
        'logisticDoneYN' => 'integer',
        'poClosedYN' => 'integer',
        'grvRecieved' => 'integer',
        'invoicedBooked' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'poType' => 'string',
        'poType_N' => 'integer',
        'docRefNo' => 'string',
        'RollLevForApp_curr' => 'integer',
        'sentToSupplier' => 'integer',
        'sentToSupplierByEmpSystemID' => 'integer',
        'sentToSupplierByEmpID' => 'string',
        'sentToSupplierByEmpName' => 'string',
        'budgetBlockYN' => 'integer',
        'budgetYear' => 'integer',
        'hidePOYN' => 'integer',
        'hideByEmpSystemID' => 'integer',
        'hideByEmpID' => 'string',
        'hideByEmpName' => 'string',
        'hideComments' => 'string',
        'WO_purchaseOrderID' => 'integer',
        'WO_NoOfAutoGenerationTimes' => 'integer',
        'WO_NoOfGeneratedTimes' => 'integer',
        'WO_fullyGenerated' => 'integer',
        'WO_amendYN' => 'integer',
        'WO_amendRequestedByEmpID' => 'string',
        'WO_amendRequestedByEmpSystemID' => 'integer',
        'WO_confirmedYN' => 'integer',
        'WO_confirmedByEmpID' => 'string',
        'WO_terminateYN' => 'integer',
        'WO_terminatedByEmpID' => 'string',
        'WO_terminateComments' => 'string',
        'partiallyGRVAllowed' => 'integer',
        'logisticsAvailable' => 'integer',
        'vatRegisteredYN' => 'integer',
        'manuallyClosed' => 'integer',
        'manuallyClosedByEmpSystemID' => 'integer',
        'manuallyClosedByEmpID' => 'string',
        'manuallyClosedByEmpName' => 'string',
        'manuallyClosedComment' => 'string',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'isSelected' => 'boolean',
        'supCategoryICVMasterID' => 'integer',
        'supCategorySubICVID' => 'integer',
        'rcmActivated' => 'integer',
        'approval_remarks' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'poConfirmedByEmpSystemID', 'employeeSystemID');
    }

    public function cancelled_by()
    {
        return $this->belongsTo('App\Models\Employee', 'poCancelledBySystemID', 'employeeSystemID');
    }

    public function manually_closed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'manuallyClosedByEmpSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function sent_supplier_by()
    {
        return $this->belongsTo('App\Models\Employee', 'sentToSupplierByEmpSystemID', 'employeeSystemID');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'poLocation', 'locationID');
    }

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierID', 'supplierCodeSystem');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'supplierTransactionCurrencyID', 'currencyID');
    }

    public function fcategory()
    {
        return $this->belongsTo('App\Models\FinanceItemCategoryMaster', 'financeCategory', 'itemCategoryID');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\PurchaseOrderDetails', 'purchaseOrderMasterID', 'purchaseOrderID');
    }

    public function approved()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'purchaseOrderID');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'purchaseOrderID');
    }

    public function suppliercontact()
    {
        return $this->belongsTo('App\Models\SupplierContactDetails', 'supplierID', 'supplierID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function transactioncurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'supplierTransactionCurrencyID', 'currencyID');
    }

    public function companydocumentattachment()
    {
        return $this->hasMany('App\Models\CompanyDocumentAttachment', 'documentSystemID', 'documentSystemID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function erpPurchaseorderdetails()
    {
        return $this->hasMany(\App\Models\ErpPurchaseorderdetail::class);
    }


    
}
