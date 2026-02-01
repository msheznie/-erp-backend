<?php

namespace App\helper\Workflow\API;

use Illuminate\Support\Facades\DB;
use App\Models;
use App\helper\BlockInvoice;
use App\helper\CurrencyValidation;
use App\helper\IvmsDeliveryOrderService;
use App\Jobs\PushNotification;
use App\helper\Helper;

class DocumentConfirmApi
{
    public static function confirmDocumentForApi($params)
    {
        //Skip Employee Info when Confirming;
        $empInfoSkip = array(106, 107); // 107 mean documentMaster id of "Supplier Registration" document in ERP

        /** check document is already confirmed*/
        if (!array_key_exists('autoID', $params)) {
            return ['success' => false, 'message' => trans('custom.parameter_document_system_id_missing')];
        }

        if (!array_key_exists('company', $params)) {
            return ['success' => false, 'message' => trans('custom.parameter_company_missing')];
        }

        if (!array_key_exists('document', $params)) {
            return ['success' => false, 'message' => trans('custom.parameter_document_missing')];
        }
        DB::beginTransaction();
        try {


            $docInforArr = array('documentCodeColumnName' => '', 'confirmColumnName' => '', 'confirmedBy' => '', 'confirmedBySystemID' => '', 'confirmedDate' => '', 'tableName' => '', 'modelName' => '', 'primarykey' => '');

            switch ($params["document"]) { // check the document id and set relavant parameters
                case 1:
                case 50:
                case 51:
                    $docInforArr["documentCodeColumnName"] = 'purchaseRequestCode';
                    $docInforArr["confirmColumnName"] = 'PRConfirmedYN';
                    $docInforArr["confirmedBy"] = 'PRConfirmedBy';
                    $docInforArr["confirmedByEmpID"] = 'PRConfirmedByEmpName';
                    $docInforArr["confirmedBySystemID"] = 'PRConfirmedBySystemID';
                    $docInforArr["confirmedDate"] = 'PRConfirmedDate';
                    $docInforArr["tableName"] = 'erp_purchaserequest';
                    $docInforArr["modelName"] = 'PurchaseRequest';
                    $docInforArr["primarykey"] = 'purchaseRequestID';
                    break;
                case 2:
                case 5:
                case 52:
                    $docInforArr["documentCodeColumnName"] = 'purchaseOrderCode';
                    $docInforArr["confirmColumnName"] = 'poConfirmedYN';
                    $docInforArr["confirmedBy"] = 'poConfirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'poConfirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'poConfirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'poConfirmedDate';
                    $docInforArr["tableName"] = 'erp_purchaseordermaster';
                    $docInforArr["modelName"] = 'ProcumentOrder';
                    $docInforArr["primarykey"] = 'purchaseOrderID';
                    break;
                case 56:
                    $docInforArr["documentCodeColumnName"] = 'primarySupplierCode';
                    $docInforArr["confirmColumnName"] = 'supplierConfirmedYN';
                    $docInforArr["confirmedBy"] = 'supplierConfirmedEmpName';
                    $docInforArr["confirmedByEmpID"] = 'supplierConfirmedEmpID';
                    $docInforArr["confirmedBySystemID"] = 'supplierConfirmedEmpSystemID';
                    $docInforArr["confirmedDate"] = 'supplierConfirmedDate';
                    $docInforArr["tableName"] = 'suppliermaster';
                    $docInforArr["modelName"] = 'SupplierMaster';
                    $docInforArr["primarykey"] = 'supplierCodeSystem';
                    break;
                case 86:
                    $docInforArr["documentCodeColumnName"] = 'supplierName';
                    $docInforArr["confirmColumnName"] = 'supplierConfirmedYN';
                    $docInforArr["confirmedBy"] = 'supplierConfirmedEmpName';
                    $docInforArr["confirmedByEmpID"] = 'supplierConfirmedEmpID';
                    $docInforArr["confirmedBySystemID"] = 'supplierConfirmedEmpSystemID';
                    $docInforArr["confirmedDate"] = 'supplierConfirmedDate';
                    $docInforArr["tableName"] = 'registeredsupplier';
                    $docInforArr["modelName"] = 'RegisteredSupplier';
                    $docInforArr["primarykey"] = 'id';
                    break;
                case 57:
                    $docInforArr["documentCodeColumnName"] = 'primaryCode';
                    $docInforArr["confirmColumnName"] = 'itemConfirmedYN';
                    $docInforArr["confirmedBy"] = 'itemConfirmedByEMPName';
                    $docInforArr["confirmedByEmpID"] = 'itemConfirmedByEMPID';
                    $docInforArr["confirmedBySystemID"] = 'itemConfirmedByEMPSystemID';
                    $docInforArr["confirmedDate"] = 'itemConfirmedDate';
                    $docInforArr["tableName"] = 'itemmaster';
                    $docInforArr["modelName"] = 'ItemMaster';
                    $docInforArr["primarykey"] = 'itemCodeSystem';
                    break;
                case 58:
                    $docInforArr["documentCodeColumnName"] = 'CutomerCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedEmpName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'customermaster';
                    $docInforArr["modelName"] = 'CustomerMaster';
                    $docInforArr["primarykey"] = 'customerCodeSystem';
                    break;
                case 59:
                    $docInforArr["documentCodeColumnName"] = 'AccountCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedEmpName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedEmpDate';
                    $docInforArr["tableName"] = 'chartofaccounts';
                    $docInforArr["modelName"] = 'ChartOfAccount';
                    $docInforArr["primarykey"] = 'chartOfAccountSystemID';
                    break;
                case 9:
                    $docInforArr["documentCodeColumnName"] = 'RequestCode';
                    $docInforArr["confirmColumnName"] = 'ConfirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedEmpName';
                    $docInforArr["confirmedByEmpID"] = 'ConfirmedBy';
                    $docInforArr["confirmedBySystemID"] = 'ConfirmedBySystemID';
                    $docInforArr["confirmedDate"] = 'ConfirmedDate';
                    $docInforArr["tableName"] = 'erp_request';
                    $docInforArr["modelName"] = 'MaterielRequest';
                    $docInforArr["primarykey"] = 'RequestID';
                    break;
                case 3:
                    $docInforArr["documentCodeColumnName"] = 'grvPrimaryCode';
                    $docInforArr["confirmColumnName"] = 'grvConfirmedYN';
                    $docInforArr["confirmedBy"] = 'grvConfirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'grvConfirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'grvConfirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'grvConfirmedDate';
                    $docInforArr["tableName"] = 'erp_grvmaster';
                    $docInforArr["modelName"] = 'GRVMaster';
                    $docInforArr["primarykey"] = 'grvAutoID';
                    break;
                case 8:
                    $docInforArr["documentCodeColumnName"] = 'itemIssueCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_itemissuemaster';
                    $docInforArr["modelName"] = 'ItemIssueMaster';
                    $docInforArr["primarykey"] = 'itemIssueAutoID';
                    break;
                case 12:
                    $docInforArr["documentCodeColumnName"] = 'itemReturnCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_itemreturnmaster';
                    $docInforArr["modelName"] = 'ItemReturnMaster';
                    $docInforArr["primarykey"] = 'itemReturnAutoID';
                    break;
                case 13:
                    $docInforArr["documentCodeColumnName"] = 'stockTransferCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_stocktransfer';
                    $docInforArr["modelName"] = 'StockTransfer';
                    $docInforArr["primarykey"] = 'stockTransferAutoID';
                    break;
                case 10:
                    $docInforArr["documentCodeColumnName"] = 'stockReceiveCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_stockreceive';
                    $docInforArr["modelName"] = 'StockReceive';
                    $docInforArr["primarykey"] = 'stockReceiveAutoID';
                    break;
                case 61:
                    $docInforArr["documentCodeColumnName"] = 'documentCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_inventoryreclassification';
                    $docInforArr["modelName"] = 'InventoryReclassification';
                    $docInforArr["primarykey"] = 'inventoryreclassificationID';
                    break;
                case 24:
                    $docInforArr["documentCodeColumnName"] = 'purchaseReturnCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_purchasereturnmaster';
                    $docInforArr["modelName"] = 'PurchaseReturn';
                    $docInforArr["primarykey"] = 'purhaseReturnAutoID';
                    break;
                case 20:
                    $docInforArr["documentCodeColumnName"] = 'bookingInvCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_custinvoicedirect';
                    $docInforArr["modelName"] = 'CustomerInvoiceDirect';
                    $docInforArr["primarykey"] = 'custInvoiceDirectAutoID';
                    break;
                case 7:
                    $docInforArr["documentCodeColumnName"] = 'stockAdjustmentCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_stockadjustment';
                    $docInforArr["modelName"] = 'StockAdjustment';
                    $docInforArr["primarykey"] = 'stockAdjustmentAutoID';
                    break;
                case 15:
                    $docInforArr["documentCodeColumnName"] = 'debitNoteCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_debitnote';
                    $docInforArr["modelName"] = 'DebitNote';
                    $docInforArr["primarykey"] = 'debitNoteAutoID';
                    break;
                case 19:
                    $docInforArr["documentCodeColumnName"] = 'creditNoteCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_creditnote';
                    $docInforArr["modelName"] = 'CreditNote';
                    $docInforArr["primarykey"] = 'creditNoteAutoID';
                    break;
                case 11:
                    $docInforArr["documentCodeColumnName"] = 'bookingInvCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_bookinvsuppmaster';
                    $docInforArr["modelName"] = 'BookInvSuppMaster';
                    $docInforArr["primarykey"] = 'bookingSuppMasInvAutoID';
                    break;
                case 4:
                    $docInforArr["documentCodeColumnName"] = 'BPVcode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_paysupplierinvoicemaster';
                    $docInforArr["modelName"] = 'PaySupplierInvoiceMaster';
                    $docInforArr["primarykey"] = 'PayMasterAutoId';
                    break;
                case 62:
                    $docInforArr["documentCodeColumnName"] = 'bankRecPrimaryCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_bankrecmaster';
                    $docInforArr["modelName"] = 'BankReconciliation';
                    $docInforArr["primarykey"] = 'bankRecAutoID';
                    break;
                case 63:
                    $docInforArr["documentCodeColumnName"] = 'capitalizationCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_fa_assetcapitalization';
                    $docInforArr["modelName"] = 'AssetCapitalization';
                    $docInforArr["primarykey"] = 'capitalizationID';
                    break;
                case 64:
                    $docInforArr["documentCodeColumnName"] = 'bankTransferDocumentCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_paymentbanktransfer';
                    $docInforArr["modelName"] = 'PaymentBankTransfer';
                    $docInforArr["primarykey"] = 'paymentBankTransferID';
                    break;
                case 17:
                    $docInforArr["documentCodeColumnName"] = 'JVcode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_jvmaster';
                    $docInforArr["modelName"] = 'JvMaster';
                    $docInforArr["primarykey"] = 'jvMasterAutoId';
                    break;
                case 22:
                    $docInforArr["documentCodeColumnName"] = 'faCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_fa_asset_master';
                    $docInforArr["modelName"] = 'FixedAssetMaster';
                    $docInforArr["primarykey"] = 'faID';
                    break;
                case 23:
                    $docInforArr["documentCodeColumnName"] = 'depCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByEmpName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_fa_depmaster';
                    $docInforArr["modelName"] = 'FixedAssetDepreciationMaster';
                    $docInforArr["primarykey"] = 'depMasterAutoID';
                    break;
                case 46:
                    $docInforArr["documentCodeColumnName"] = 'transferVoucherNo';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByEmpName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_budgettransferform';
                    $docInforArr["modelName"] = 'BudgetTransferForm';
                    $docInforArr["primarykey"] = 'budgetTransferFormAutoID';
                    break;
                case 65:
                    $docInforArr["documentCodeColumnName"] = 'budgetmasterID';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByEmpName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_budgetmaster';
                    $docInforArr["modelName"] = 'BudgetMaster';
                    $docInforArr["primarykey"] = 'budgetmasterID';
                    break;
                case 41:
                    $docInforArr["documentCodeColumnName"] = 'disposalDocumentCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByEmpName';
                    $docInforArr["confirmedByEmpID"] = 'confimedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confimedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_fa_asset_disposalmaster';
                    $docInforArr["modelName"] = 'AssetDisposalMaster';
                    $docInforArr["primarykey"] = 'assetdisposalMasterAutoID';
                    break;
                case 21:
                    $docInforArr["documentCodeColumnName"] = 'custPaymentReceiveCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_customerreceivepayment';
                    $docInforArr["modelName"] = 'CustomerReceivePayment';
                    $docInforArr["primarykey"] = 'custReceivePaymentAutoID';
                    break;
                case 28:
                    $docInforArr["documentCodeColumnName"] = 'monthlyAdditionsCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedby';
                    $docInforArr["confirmedByEmpID"] = 'confirmedby';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'hrms_monthlyadditionsmaster';
                    $docInforArr["modelName"] = 'MonthlyAdditionsMaster';
                    $docInforArr["primarykey"] = 'monthlyAdditionsMasterID';
                    break;
                case 66: // Bank Account
                    $docInforArr["documentCodeColumnName"] = 'bankAccountAutoID';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_bankaccount';
                    $docInforArr["modelName"] = 'BankAccount';
                    $docInforArr["primarykey"] = 'bankAccountAutoID';
                    break;
                case 67: // Sales Quotation
                case 68: // Sales Order
                    $docInforArr["documentCodeColumnName"] = 'quotationCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_quotationmaster';
                    $docInforArr["modelName"] = 'QuotationMaster';
                    $docInforArr["primarykey"] = 'quotationMasterID';
                    break;
                case 71: // delivery order
                    $docInforArr["documentCodeColumnName"] = 'deliveryOrderCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_delivery_order';
                    $docInforArr["modelName"] = 'DeliveryOrder';
                    $docInforArr["primarykey"] = 'deliveryOrderID';
                    break;
                case 87: // Sales Return
                    $docInforArr["documentCodeColumnName"] = 'salesReturnCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'salesreturn';
                    $docInforArr["modelName"] = 'SalesReturn';
                    $docInforArr["primarykey"] = 'id';
                    break;
                case 96: // Currency Conversion
                    $docInforArr["documentCodeColumnName"] = 'conversionCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedEmpName';
                    $docInforArr["confirmedByEmpID"] = 'ConfirmedBy';
                    $docInforArr["confirmedBySystemID"] = 'ConfirmedBySystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'currency_conversion_master';
                    $docInforArr["modelName"] = 'CurrencyConversionMaster';
                    $docInforArr["primarykey"] = 'id';
                    break;
                case 97:
                    $docInforArr["documentCodeColumnName"] = 'stockCountCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_stockcount';
                    $docInforArr["modelName"] = 'StockCount';
                    $docInforArr["primarykey"] = 'stockCountAutoID';
                    break;
                case 102:
                    $docInforArr["documentCodeColumnName"] = 'additionVoucherNo';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByEmpName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_budgetaddition';
                    $docInforArr["modelName"] = 'ErpBudgetAddition';
                    $docInforArr["primarykey"] = 'id';
                    break;
                case 104:
                    $docInforArr["documentCodeColumnName"] = 'returnFillingCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByEmpName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'vat_return_filling_master';
                    $docInforArr["modelName"] = 'VatReturnFillingMaster';
                    $docInforArr["primarykey"] = 'id';
                    break;
                case 100:
                    $docInforArr["tableName"] = 'erp_budget_contingency';
                    $docInforArr["modelName"] = 'ContingencyBudgetPlan';
                    $docInforArr["primarykey"] = 'ID';
                    $docInforArr["documentCodeColumnName"] = 'ID';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    break;
                case 99: // asset verification
                    $docInforArr["documentCodeColumnName"] = 'verficationCode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_fa_asset_verification';
                    $docInforArr["modelName"] = 'AssetVerification';
                    $docInforArr["primarykey"] = 'id';
                    break;
                case 103: // asset Transfer
                    $docInforArr["documentCodeColumnName"] = 'document_code';
                    $docInforArr["confirmColumnName"] = 'confirmed_yn';
                    $docInforArr["confirmed_by_name"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmed_by_emp_id';
                    $docInforArr["confirmedDate"] = 'confirmed_date';
                    $docInforArr["tableName"] = 'erp_fa_fa_asset_transfer';
                    $docInforArr["modelName"] = 'ERPAssetTransfer';
                    $docInforArr["primarykey"] = 'id';
                    break;
                case 106: //Appointment
                    $docInforArr["documentCodeColumnName"] = 'primary_code';
                    $docInforArr["confirmColumnName"] = 'confirmed_yn';
                    $docInforArr["confirmed_by_name"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmed_by_emp_id';
                    $docInforArr["confirmedDate"] = 'confirmed_date';
                    $docInforArr["tableName"] = 'appointment';
                    $docInforArr["modelName"] = 'Appointment';
                    $docInforArr["primarykey"] = 'id';
                    break;
                case 107: //Supper registration
                    $docInforArr["documentCodeColumnName"] = 'id';
                    $docInforArr["confirmColumnName"] = 'confirmed_yn';
                    $docInforArr["confirmed_by_name"] = 'confirmed_by_name';
                    $docInforArr["confirmedByEmpID"] = 'confirmed_by_emp_id';
                    $docInforArr["confirmedBySystemID"] = 'confirmed_by_emp_id';
                    $docInforArr["confirmedDate"] = 'confirmed_date';
                    $docInforArr["tableName"] = 'srm_supplier_registration_link';
                    $docInforArr["modelName"] = 'SupplierRegistrationLink';
                    $docInforArr["primarykey"] = 'id';
                    break;
                case 108: //SRM Tender
                    $docInforArr["documentCodeColumnName"] = 'tender_code';
                    $docInforArr["confirmColumnName"] = 'confirmed_yn';
                    $docInforArr["confirmed_by_name"] = 'confirmed_by_name';
                    $docInforArr["confirmedByEmpID"] = 'confirmed_by_emp_system_id';
                    $docInforArr["confirmedBySystemID"] = 'confirmed_by_emp_system_id';
                    $docInforArr["confirmedDate"] = 'confirmed_date';
                    $docInforArr["tableName"] = 'srm_tender_master';
                    $docInforArr["modelName"] = 'TenderMaster';
                    $docInforArr["primarykey"] = 'id';
                    break;
                case 69:
                    $docInforArr["documentCodeColumnName"] = 'consoleJVcode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_consolejvmaster';
                    $docInforArr["modelName"] = 'ConsoleJVMaster';
                    $docInforArr["primarykey"] = 'consoleJvMasterAutoId';
                    break;
                default:
                    return ['success' => false, 'message' => trans('custom.document_id_not_found')];
            }

            $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
            $masterRec = $namespacedModel::find($params["autoID"]);
            if ($masterRec) {
                if (in_array($params["document"], [20, 71])) {
                    $invoiceBlockPolicy = Models\CompanyPolicyMaster::where('companyPolicyCategoryID', 45)
                        ->where('companySystemID', $params['company'])
                        ->where('isYesNO', 1)
                        ->first();


                    if ($invoiceBlockPolicy) {
                        $blockResult = BlockInvoice::blockCustomerInvoiceByCreditLimit($params["document"], $masterRec);
                        if (!$blockResult['status']) {
                            return ['success' => false, 'message' => $blockResult['message']];
                        }
                    }
                }

                //validate currency
                if (in_array($params["document"], Helper::documentListForValidateCurrency())) {
                    $currencyValidate = CurrencyValidation::validateCurrency($params["document"], $masterRec);
                    if (!$currencyValidate['status']) {
                        return ['success' => false, 'message' => $currencyValidate['message']];
                    }
                }

                //validate supplier blocked status
                if (in_array($params["document"], Helper::documentListForValidateSupplierBlockedStatus())) {
                    $supplierValidate = Helper::validateSupplierBlockedStatus($params["document"], $masterRec);

                    if ($supplierValidate) {
                        return ['success' => false, 'message' => trans('custom.supplier_blocked_cannot_confirm')];
                    }
                }

                //checking whether document approved table has a data for the same document
                $docExist = Models\DocumentApproved::where('documentSystemID', $params["document"])->where('documentSystemCode', $params["autoID"])->first();
                if (!$docExist) {
                    // check document is available in document master table
                    $document = Models\DocumentMaster::where('documentSystemID', $params["document"])->first();
                    if ($document) {
                        //check document is already confirmed
                        $isConfirm = $namespacedModel::where($docInforArr["primarykey"], $params["autoID"])->where($docInforArr["confirmColumnName"], 1)->first();
                        if (!$isConfirm) {
                            if(isset($masterRec->confirmedByEmpSystemID) && $masterRec->documentSystemID == 21) {
                                $empInfo = Models\Employee::with(['profilepic', 'user_data' => function($query) {
                                    $query->select('uuid', 'employee_id');
                                }])->find($masterRec->confirmedByEmpSystemID);
                            }else {
                                // get current employee detail.
                                if (!in_array($params['document'], $empInfoSkip)) {
                                    $empInfo = Models\Employee::with(['profilepic', 'user_data' => function($query) {
                                        $query->select('uuid', 'employee_id');
                                    }])->find(11);
                                } else {
                                    $empInfo  =  (object) ['empName' => null, 'empID' => null, 'employeeSystemID' => null];
                                }
                            }

                            if(isset($masterRec->confirmedDate) && $masterRec->documentSystemID == 21) {
                                $masterRec->update([$docInforArr["confirmColumnName"] => 1, $docInforArr["confirmedBy"] => $empInfo->empName, $docInforArr["confirmedByEmpID"] => $empInfo->empID, $docInforArr["confirmedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["confirmedDate"] => $masterRec->confirmedDate, 'RollLevForApp_curr' => 1]);
                            }else {
                                $masterRec->update([$docInforArr["confirmColumnName"] => 1, $docInforArr["confirmedBy"] => $empInfo->empName, $docInforArr["confirmedByEmpID"] => $empInfo->empID, $docInforArr["confirmedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["confirmedDate"] => now(), 'RollLevForApp_curr' => 1]);
                            }

                            //get the policy
                            $policy = Models\CompanyDocumentAttachment::where('companySystemID', $params["company"])->where('documentSystemID', $params["document"])->first();
                            if ($policy) {
                                $isSegmentWise = $policy->isServiceLineApproval;
                                $isCategoryWise = $policy->isCategoryApproval;
                                $isValueWise = $policy->isAmountApproval;
                                $isAttachment = $policy->isAttachmentYN;
                                //check for attachment is uploaded if attachment policy is set to must
                                if ($isAttachment == -1) {
                                    $docAttachment = Models\DocumentAttachments::where('companySystemID', $params["company"])->where('documentSystemID', $params["document"])->where('documentSystemCode', $params["autoID"])->first();
                                    if (!$docAttachment) {
                                        return ['success' => false, 'message' => trans('custom.no_attachments_attached')];
                                    }
                                }
                            } else {
                                return ['success' => false, 'message' => trans('custom.policy_not_available')];
                            }

                            // get approval rolls
                            $approvalLevel = Models\ApprovalLevel::with('approvalrole')->where('companySystemID', $params["company"])->where('documentSystemID', $params["document"])->where('departmentSystemID', $document["departmentSystemID"])->where('isActive', -1);

                            if ($isSegmentWise) {
                                if (array_key_exists('segment', $params)) {

                                    if ($params["segment"]) {
                                        $approvalLevel->where('serviceLineSystemID', $params["segment"]);
                                        $approvalLevel->where('serviceLineWise', 1);
                                    } else {
                                        return ['success' => false, 'message' => trans('custom.no_approval_setup_created')];
                                    }
                                } else {
                                    return ['success' => false, 'message' => trans('custom.serviceline_parameters_missing')];
                                }
                            }

                            if ($isCategoryWise) {
                                if (array_key_exists('category', $params)) {
                                    if ($params["category"]) {
                                        $approvalLevel->where('categoryID', $params["category"]);
                                        $approvalLevel->where('isCategoryWiseApproval', -1);
                                    } else {
                                        return ['success' => false, 'message' => trans('custom.no_approval_setup_created')];
                                    }
                                } else {
                                    return ['success' => false, 'message' => trans('custom.category_parameter_missing')];
                                }
                            }

                            if ($isValueWise) {
                                if (array_key_exists('amount', $params)) {
                                    if ($params["amount"] >= 0) {
                                        $amount = $params["amount"];
                                        $approvalLevel->where(function ($query) use ($amount) {
                                            $query->where('valueFrom', '<=', $amount);
                                            $query->where('valueTo', '>=', $amount);
                                        });
                                        $approvalLevel->where('valueWise', 1);
                                    } else {
                                        return ['success' => false, 'message' => trans('custom.no_approval_setup_created')];
                                    }
                                } else {
                                    return ['success' => false, 'message' => trans('custom.amount_parameter_missing')];
                                }
                            }

                            $output = $approvalLevel->first();

                            //when iscategorywiseapproval true and output is empty again check for isCategoryWiseApproval = 0
                            if (empty($output)) {

                                if ($isCategoryWise) {
                                    $approvalLevel = Models\ApprovalLevel::with('approvalrole')->where('companySystemID', $params["company"])->where('documentSystemID', $params["document"])->where('departmentSystemID', $document["departmentSystemID"])->where('isActive', -1);
                                    if ($isSegmentWise) {
                                        if (array_key_exists('segment', $params)) {
                                            if ($params["segment"]) {
                                                $approvalLevel->where('serviceLineSystemID', $params["segment"]);
                                                $approvalLevel->where('serviceLineWise', 1);
                                            } else {
                                                return ['success' => false, 'message' => trans('custom.no_approval_setup_created')];
                                            }
                                        } else {
                                            return ['success' => false, 'message' => trans('custom.serviceline_parameters_missing')];
                                        }
                                    }

                                    if ($isValueWise) {
                                        if (array_key_exists('amount', $params)) {
                                            if ($params["amount"] >= 0) {
                                                $amount = $params["amount"];
                                                $approvalLevel->where(function ($query) use ($amount) {
                                                    $query->where('valueFrom', '<=', $amount);
                                                    $query->where('valueTo', '>=', $amount);
                                                });
                                                $approvalLevel->where('valueWise', 1);
                                            } else {
                                                return ['success' => false, 'message' => trans('custom.no_approval_setup_created')];
                                            }
                                        } else {
                                            return ['success' => false, 'message' => trans('custom.amount_parameter_missing')];
                                        }
                                    }

                                    $approvalLevel->where('isCategoryWiseApproval', 0);
                                    $output = $approvalLevel->first();
                                }
                            }


                            if ($output) {
                                /** get source document master record*/
                                $sorceDocument = $namespacedModel::find($params["autoID"]);

                                //confirm the document
                                if (isset($params['email'])) {
                                    $email_in = $params['email'];
                                } else {
                                    $email_in = null;
                                }

                                $documentApproved = [];
                                if ($output) {
                                    if ($output->approvalrole) {
                                        foreach ($output->approvalrole as $val) {
                                            if ($val->approvalGroupID) {
                                                $documentApproved[] = array('companySystemID' => $val->companySystemID, 'companyID' => $val->companyID, 'departmentSystemID' => $val->departmentSystemID, 'departmentID' => $val->departmentID, 'serviceLineSystemID' => $val->serviceLineSystemID, 'serviceLineCode' => $val->serviceLineID, 'documentSystemID' => $val->documentSystemID, 'documentID' => $val->documentID, 'documentSystemCode' => $params["autoID"], 'documentCode' => $sorceDocument[$docInforArr["documentCodeColumnName"]], 'approvalLevelID' => $val->approvalLevelID, 'rollID' => $val->rollMasterID, 'approvalGroupID' => $val->approvalGroupID, 'rollLevelOrder' => $val->rollLevel, 'docConfirmedDate' => now(), 'docConfirmedByEmpSystemID' => $empInfo->employeeSystemID, 'docConfirmedByEmpID' => $empInfo->empID, 'timeStamp' => NOW(), 'reference_email' => $email_in);
                                            } else {
                                                return ['success' => false, 'message' => trans('custom.please_set_approval_group')];
                                            }
                                        }
                                    } else {
                                        return ['success' => false, 'message' => trans('custom.no_approval_setup_created')];
                                    }
                                }
                                // insert rolls to document approved table
                                Models\DocumentApproved::insert($documentApproved);

                                $documentApproved = Models\DocumentApproved::where("documentSystemID", $params["document"])
                                    ->where("documentSystemCode", $sorceDocument[$docInforArr["primarykey"]])
                                    ->where("rollLevelOrder", 1)
                                    ->first();
                                if ($documentApproved) {

                                    if ($documentApproved->approvedYN == 0) {
                                        $companyDocument = Models\CompanyDocumentAttachment::where('companySystemID', $documentApproved->companySystemID)
                                            ->where('documentSystemID', $documentApproved->documentSystemID)
                                            ->first();

                                        if (empty($companyDocument)) {
                                            return ['success' => false, 'message' => trans('custom.policy_not_found')];
                                        }

                                        $approvalList = Models\EmployeesDepartment::where('employeeGroupID', $documentApproved->approvalGroupID)
                                            ->whereHas('employee', function ($q) {
                                                $q->where('discharegedYN', 0);
                                            })
                                            ->where('companySystemID', $documentApproved->companySystemID)
                                            ->where('documentSystemID', $documentApproved->documentSystemID)
                                            ->where('isActive', 1)
                                            ->where('removedYN', 0);

                                        if ($companyDocument['isServiceLineApproval'] == -1) {
                                            $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproved->serviceLineSystemID);
                                        }

                                        $approvalList = $approvalList
                                            ->with(['employee'])
                                            ->groupBy('employeeSystemID')
                                            ->get();

                                        $emails = array();
                                        $pushNotificationUserIds = [];
                                        $pushNotificationArray = [];
                                        $document = Models\DocumentMaster::where('documentSystemID', $documentApproved->documentSystemID)->first();

                                        $approvedDocNameBody = $document->documentDescription . ' <b>' . $documentApproved->documentCode . '</b>';

                                        $pushNotificationMessage = trans('email.is_pending_approval', ['attribute' => $document->documentDescription . " " . $documentApproved->documentCode]);
                                        foreach ($approvalList as $da) {
                                            if ($da->employee) {

                                                $pushNotificationUserIds[] = $da->employee->employeeSystemID;
                                            }
                                        }

                                        $pushNotificationArray['companySystemID'] = $documentApproved->companySystemID;
                                        $pushNotificationArray['documentSystemID'] = $documentApproved->documentSystemID;
                                        $pushNotificationArray['id'] = $documentApproved->documentSystemCode;
                                        $pushNotificationArray['type'] = 1;
                                        $pushNotificationArray['documentCode'] = $documentApproved->documentCode;
                                        $pushNotificationArray['pushNotificationMessage'] = $pushNotificationMessage;


                                        if (in_array($params["document"], [71])) {
                                            $ivmsPolicy = Models\CompanyPolicyMaster::where('companyPolicyCategoryID', 47)
                                                ->where('companySystemID', $params['company'])
                                                ->where('isYesNO', 1)
                                                ->first();


                                            if ($ivmsPolicy) {
                                                $ivmsResult = IvmsDeliveryOrderService::postIvmsDeliveryOrder($masterRec);
                                                if (!$ivmsResult['status']) {
                                                    DB::rollback();
                                                    return ['success' => false, 'message' => $ivmsResult['message']];
                                                }
                                            }
                                        }

                                        if(!isset($params['sendNotication']) || (isset($params['sendNotication']) && $params['sendNotication']))
                                            $jobPushNotification = PushNotification::dispatch($pushNotificationArray, $pushNotificationUserIds, 1);

                                    //    $webPushData = [
                                    //        'title' => $pushNotificationMessage,
                                    //        'body' => '',
                                    //        'url' => $redirectUrl,
                                    //    ];

                                        // WebPushNotificationService::sendNotification($webPushData, 1, $pushNotificationUserIds);

                                    }
                                }

                                DB::commit();
                                return ['success' => true, 'message' => trans('custom.successfully_document_confirmed')];
                            } else {
                                DB::rollback();
                                return ['success' => false, 'message' => trans('custom.no_approval_setup_created')];
                            }
                        } else {
                            DB::rollback();
                            return ['success' => false, 'message' => trans('custom.document_already_confirmed')];
                        }
                    } else {
                        DB::rollback();
                        return ['success' => false, 'message' => trans('custom.document_not_found')];
                    }
                } else {
                    DB::rollback();
                    return ['success' => false, 'message' => trans('custom.document_approval_data_generated')];
                }
            } else {
                DB::rollback();
                return ['success' => false, 'message' => trans('custom.no_records_found')];
            }
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            //dd($e);
            return ['success' => false, 'message' => $e . trans('custom.error_occurred')];
        }
    }
}