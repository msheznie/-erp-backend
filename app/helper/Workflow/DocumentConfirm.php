<?php

namespace App\helper\Workflow;

use Illuminate\Support\Facades\DB;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\ApprovalLevel;
use App\Models\CompanyDocumentAttachment;
use App\Models\Employee;
use App\Models\ApprovalGroups;
use App\Models\EmployeesDepartment;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentAttachments;
use App\helper\BlockInvoice;
use App\helper\CurrencyValidation;
use App\helper\email as Email;
use App\helper\Helper;
use App\helper\IvmsDeliveryOrderService;
use App\Services\UserTypeService;
use App\Services\DocumentAutoApproveService;
use App\Services\DocumentReportingManagerService;
use App\Jobs\PushNotification;
use App\Models\PvApprovalTypeSetup;
use App\Services\PvApprovalTypeSetupService;

class DocumentConfirm
{
    public static function confirmDocument($params)
    {
        //Skip Employee Info when Confirming;
        $empInfoSkip = array(106, 107, 127); // 107 mean documentMaster id of "Supplier Registration" document in ERP

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
                case 113: //SRM RFX
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
                case 117:
                    $docInforArr["documentCodeColumnName"] = 'code';
                    $docInforArr["confirmColumnName"] = 'requested';
                    $docInforArr["confirmedBy"] = 'requested_by_name';
                    $docInforArr["confirmedByEmpID"] = 'requested_employeeSystemID';
                    $docInforArr["confirmedBySystemID"] = 'requested_employeeSystemID';
                    $docInforArr["confirmedDate"] = 'requested_date';
                    $docInforArr["tableName"] = 'document_modify_request';
                    $docInforArr["modelName"] = 'DocumentModifyRequest';
                    $docInforArr["primarykey"] = 'id';
                    break;
                case 118:
                    $docInforArr["documentCodeColumnName"] = 'code';
                    $docInforArr["confirmColumnName"] = 'confirm';
                    $docInforArr["confirmedBy"] = 'requested_by_name';
                    $docInforArr["confirmedByEmpID"] = 'requested_employeeSystemID';
                    $docInforArr["confirmedBySystemID"] = 'requested_employeeSystemID';
                    $docInforArr["confirmedDate"] = 'confirmation_date';
                    $docInforArr["tableName"] = 'document_modify_request';
                    $docInforArr["modelName"] = 'DocumentModifyRequest';
                    $docInforArr["primarykey"] = 'id';
                    break;
                case 119:
                    $docInforArr["documentCodeColumnName"] = 'RRVcode';
                    $docInforArr["confirmColumnName"] = 'confirmedYN';
                    $docInforArr["confirmedBy"] = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'recurring_voucher_setup';
                    $docInforArr["modelName"] = 'RecurringVoucherSetup';
                    $docInforArr["primarykey"] = 'recurringVoucherAutoId';
                    break;
                case 127:
                    $docInforArr["documentCodeColumnName"] = 'document_code';
                    $docInforArr["confirmColumnName"] = 'confirmed_yn';
                    $docInforArr["confirmedBy"] = 'confirmed_by_name';
                    $docInforArr["confirmedByEmpID"] = 'confirmed_by_emp_id';
                    $docInforArr["confirmedBySystemID"] = 'confirmed_by_emp_system_id';
                    $docInforArr["confirmedDate"] = 'confirmed_date';
                    $docInforArr["tableName"] = 'srm_tender_payment_proof';
                    $docInforArr["modelName"] = 'SRMTenderPaymentProof';
                    $docInforArr["primarykey"] = 'id';
                    break;
                case 132:
                    $docInforArr["documentCodeColumnName"] = 'ServiceLineCode';
                    $docInforArr["confirmColumnName"] = 'confirmed_yn';
                    $docInforArr["confirmedBy"] = 'confirmed_by_name';
                    $docInforArr["confirmedByEmpID"] = 'confirmed_by_emp_id';
                    $docInforArr["confirmedBySystemID"] = 'confirmed_by_emp_system_id';
                    $docInforArr["confirmedDate"] = 'confirmed_date';
                    $docInforArr["tableName"] = 'serviceline';
                    $docInforArr["modelName"] = 'SegmentMaster';
                    $docInforArr["primarykey"] = 'serviceLineSystemID';
                    break;
                case 133:
                    $docInforArr["documentCodeColumnName"] = 'planningCode';
                    $docInforArr["confirmColumnName"] = 'confirmed_yn';
                    $docInforArr["confirmedBy"] = 'confirmed_by';
                    $docInforArr["confirmedByEmpID"] = 'confirmed_by_emp_id';
                    $docInforArr["confirmedBySystemID"] = 'confirmed_by_emp_system_id';
                    $docInforArr["confirmedDate"] = 'confirmed_at';
                    $docInforArr["tableName"] = 'company_budget_plannings';
                    $docInforArr["modelName"] = 'CompanyBudgetPlanning';
                    $docInforArr["primarykey"] = 'id';
                    break;
                default:
                    return ['success' => false, 'message' => trans('custom.document_id_not_found')];
            }

            $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name

            if ($params["document"] == 132) {
                $masterRec = $namespacedModel::withoutGlobalScope('final_level')->find($params["autoID"]);
            } else {
                $masterRec = $namespacedModel::find($params["autoID"]);
            }


            if ($masterRec) {
                if (in_array($params["document"], [20, 71])) {
                    $invoiceBlockPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 45)
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

                $reference_document_id = $params['document'];
                if(isset($params['reference_document_id']) && $params['reference_document_id'])
                {
                    $reference_document_id = $params['reference_document_id'];
                }

                //checking whether document approved table has a data for the same document
                $docExist = DocumentApproved::where('documentSystemID', $params["document"])->where('documentSystemCode', $params["autoID"])->first();

                if (!$docExist) {
                    // check document is available in document master table
                    $document = DocumentMaster::where('documentSystemID', $params["document"])->first();
                    if ($document) {
                        //check document is already confirmed
                        if ($params["document"] == 132) {
                            $isConfirm = $namespacedModel::withoutGlobalScope('final_level')->where($docInforArr["primarykey"], $params["autoID"])->where($docInforArr["confirmColumnName"], 1)->first();
                        } else {
                            $isConfirm = $namespacedModel::where($docInforArr["primarykey"], $params["autoID"])->where($docInforArr["confirmColumnName"], 1)->first();
                        }

                        if (!$isConfirm) {
                            // get current employee detail.
                            if (!in_array($params['document'], $empInfoSkip)) {
                                // check system user or not
                                if(isset($params['isAutoCreateDocument']) && $params['isAutoCreateDocument']) {
                                    $empInfo = UserTypeService::getSystemEmployee();
                                }
                                else{
                                    if(!empty(isset($params["employee_id"]))) {
                                        $empInfo = Employee::with(['profilepic', 'user_data' => function($query) {
                                            $query->select('uuid', 'employee_id');
                                        }])->find($params["employee_id"]);
                                    } else {
                                        $empInfo = Helper::getEmployeeInfo();
                                    }
                                }
                            } else {
                                $empInfo  =  (object) ['empName' => null, 'empID' => null, 'employeeSystemID' => null];
                            }

                            $masterRec->update([$docInforArr["confirmColumnName"] => 1, $docInforArr["confirmedBy"] => $empInfo->empName, $docInforArr["confirmedByEmpID"] => $empInfo->empID, $docInforArr["confirmedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["confirmedDate"] => now(), 'RollLevForApp_curr' => 1, 'refferedBackYN' => 0]);

                            //get the policy
                            $policy = CompanyDocumentAttachment::where('companySystemID', $params["company"])->where('documentSystemID', $reference_document_id)->first();
                            if ($policy) {
                                $isSegmentWise = $policy->isServiceLineApproval;
                                $isCategoryWise = $policy->isCategoryApproval;
                                $isSubcategoryWise = !empty($policy->isSubcategoryApproval);
                                $isValueWise = $policy->isAmountApproval;
                                $isPRTypeWise = $policy->isPRTypeApproval;
                                $isAttachment = $policy->isAttachmentYN;
                                $isPvTypeWise = false;

                                if($params["document"] == 4) {
                                    $isPvTypeWise = PvApprovalTypeSetup::where('company_system_id', $params["company"])->where('document_attachment_id', $policy->companyDocumentAttachmentID)->where('is_active', 1)->exists();
                                }

                                $fromCiUpload = false;
                                if(isset($params["fromUpload"]) && $params["fromUpload"] == true){
                                    $fromCiUpload = true;
                                }

                                if($fromCiUpload == false){
                                    //check for attachment is uploaded if attachment policy is set to must
                                    if ($isAttachment == -1) {
                                        $docAttachment = DocumentAttachments::where('companySystemID', $params["company"])->where('documentSystemID', $params['document'])->where('documentSystemCode', $params["autoID"])->first();
                                        if (!$docAttachment) {
                                            return ['success' => false, 'message' => trans('custom.no_attachments_attached')];
                                        }
                                    }
                                }

                            } else {
                                return ['success' => false, 'message' => trans('custom.policy_not_available')];
                            }

                            // get approval rolls
                            $approvalLevel = ApprovalLevel::with('approvalrole')->where('companySystemID', $params["company"])->where('documentSystemID', $reference_document_id)->where('departmentSystemID', $document["departmentSystemID"])->where('isActive', -1);

                            
                            if($params["document"] == 133){
                                $approvalLevel->where('workflow', $masterRec->workflowID);
                                if(!$approvalLevel->exists()){
                                    return ['success' => false, 'message' => trans('custom.no_approval_setup_created')];
                                }
                            }

                            if($params["document"] != 4) {
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
    
                                if ($isSubcategoryWise) {
                                    if (array_key_exists('subCategory', $params) && $params['subCategory']) {
                                        $approvalLevel->where('subcategoryID', $params['subCategory']);
                                    } else {
                                        return ['success' => false, 'message' => trans('custom.no_approval_setup_created')];
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
                            }

                            if ($params["document"] == 108) {
                                if (!array_key_exists('tenderTypeId', $params)) {
                                    return ['success' => false, 'message' => trans('custom.tender_type_parameter_missing')];
                                }

                                $tenderTypeId = $params["tenderTypeId"];
                                $tenderApprovalLevel = ApprovalLevel::isExistsTenderType($tenderTypeId, $params["company"], $reference_document_id);
                                $approvalLevel->where(function ($query) use ($tenderTypeId, $tenderApprovalLevel) {
                                    $tenderApprovalLevel
                                        ? $query->where('tenderTypeId', $tenderTypeId)
                                        : $query->where('tenderTypeId', -1)->orWhereNull('tenderTypeId');
                                });
                            }

                            if ($isPRTypeWise && ($reference_document_id == 1)) {
                                if (array_key_exists('prType', $params)) {
                                    if ($params["prType"]) {
                                        $approvalLevel->where('prType', $params["prType"]);
                                        $approvalLevel->where('prTypeWise', 1);
                                    } else {
                                        return ['success' => false, 'message' => trans('custom.no_approval_setup_created')];
                                    }
                                } else {
                                    return ['success' => false, 'message' => trans('custom.pr_type_parameter_missing')];
                                }
                            }

                            if($params["document"] == 4) {
                                if($isPvTypeWise) {
                                    $pvDocumentType = PvApprovalTypeSetupService::getPVDocumentTypeForApproval($masterRec);
                                    $invoiceTypeColumnName = $pvDocumentType['invoiceTypeColumnName'];
                                    $expenseClaimOrPettyCashColumnName = $pvDocumentType['expenseClaimOrPettyCashColumnName'];

                                    if (!is_null($invoiceTypeColumnName)) {
                                        $matchingSetup = PvApprovalTypeSetup::where('company_system_id', $params["company"])
                                            ->where('document_attachment_id', $policy->companyDocumentAttachmentID)
                                            ->when(!is_null($invoiceTypeColumnName), function ($query) use ($invoiceTypeColumnName) {
                                                $query->where($invoiceTypeColumnName, 1);
                                            })
                                            ->when(!is_null($expenseClaimOrPettyCashColumnName), function ($query) use ($expenseClaimOrPettyCashColumnName) {
                                                $query->where($expenseClaimOrPettyCashColumnName, 1);
                                            })
                                            ->where('is_active', 1)
                                            ->get();

                                        if (empty($matchingSetup->toArray())) {
                                            return ['success' => false, 'message' => trans('custom.no_active_type_based_approval_setup_found_for_this_payment_voucher_type')];
                                        }

                                        if (count($matchingSetup) > 1) {
                                            return ['success' => false, 'message' => trans('custom.multiple_active_type_based_approval_setup_found_for_this_payment_voucher_type')];
                                        }

                                        $approvalLevel->where('pvTypeWise', 1)->where('pvTypeSetupID', $matchingSetup->first()->id);

                                        if ($matchingSetup->first()->is_amount_approval) {
                                            if (array_key_exists('amount', $params)) {
                                                if ($params["amount"] >= 0) {
                                                    $amount = $params["amount"];
                                                    $approvalLevel->where(function ($query) use ($amount) {
                                                        $query->where('valueFrom', '<=', $amount);
                                                        $query->where('valueTo', '>=', $amount);
                                                    });
                                                    $approvalLevel->where('valueWise', 1);
                                                } 
                                                else {
                                                    return ['success' => false, 'message' => trans('custom.no_active_type_based_approval_setup_found_for_this_payment_voucher_type')];
                                                }
                                            } 
                                            else {
                                                return ['success' => false, 'message' => trans('custom.amount_parameter_missing')];
                                            }
                                        }
                                    }
                                }
                                else {
                                    if ($isValueWise) {
                                        if (array_key_exists('amount', $params)) {
                                            if ($params["amount"] >= 0) {
                                                $amount = $params["amount"];
                                                $approvalLevel->where(function ($query) use ($amount) {
                                                    $query->where('valueFrom', '<=', $amount);
                                                    $query->where('valueTo', '>=', $amount);
                                                });
                                                $approvalLevel->where('valueWise', 1);
                                            } 
                                            else {
                                                return ['success' => false, 'message' => trans('custom.no_approval_setup_created')];
                                            }
                                        } 
                                        else {
                                            return ['success' => false, 'message' => trans('custom.amount_parameter_missing')];
                                        }
                                    }
                                }
                            }

                            $output = $approvalLevel->first();

                            //when iscategorywiseapproval true and output is empty again check for isCategoryWiseApproval = 0
                            if (empty($output)) {
                                if ($isCategoryWise && ($params["document"] != 4)) {
                                    $approvalLevel = ApprovalLevel::with('approvalrole')->where('companySystemID', $params["company"])->where('documentSystemID', $params["document"])->where('departmentSystemID', $document["departmentSystemID"])->where('isActive', -1);
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

                                    if ($isPRTypeWise && ($reference_document_id == 1)) {
                                        if (array_key_exists('prType', $params)) {
                                            if ($params["prType"]) {
                                                $approvalLevel->where('prType', $params["prType"]);
                                                $approvalLevel->where('prTypeWise', 1);
                                            } else {
                                                return ['success' => false, 'message' => trans('custom.no_approval_setup_created')];
                                            }
                                        } else {
                                            return ['success' => false, 'message' => trans('custom.pr_type_parameter_missing')];
                                        }
                                    }

                                    $approvalLevel->where('isCategoryWiseApproval', 0);
                                    $output = $approvalLevel->first();
                                }
                            }

                            if(isset($params['isAutoCreateDocument']) && $params['isAutoCreateDocument']){
                                $sorceDocument = $namespacedModel::find($params["autoID"]);
                                $documentApprovedAuto = DocumentAutoApproveService::setDocumentApprovedData($params, $sorceDocument, $docInforArr, $empInfo);
                                DocumentApproved::insert($documentApprovedAuto);
                                DB::commit();
                                return ['success' => true, 'message' => trans('custom.successfully_document_confirmed')];
                            }

                            if ($output) {
                                /** get source document master record*/
                                if ($params["document"] == 132) {
                                     $sorceDocument = $namespacedModel::withoutGlobalScope('final_level')->find($params["autoID"]);
                                } else {
                                     $sorceDocument = $namespacedModel::find($params["autoID"]);
                                }
                                $unverifiedEmails = null;
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
                                                $approvalGroup = ApprovalGroups::find($val->approvalGroupID);
                                                if($approvalGroup && $approvalGroup->isReportingManager == 1){
                                                    $reportingManagerResult = DocumentReportingManagerService::getReportingManagerDocumentApprovedData($empInfo, $val, $params, $sorceDocument, $docInforArr, $email_in);
                                                    if($reportingManagerResult['success']){
                                                        $documentApproved[] = $reportingManagerResult['data'];
                                                    } else {
                                                        return $reportingManagerResult;
                                                    }
                                                } else {
                                                    $documentApproved[] = array('companySystemID' => $val->companySystemID, 'companyID' => $val->companyID, 'departmentSystemID' => $val->departmentSystemID, 'departmentID' => $val->departmentID, 'serviceLineSystemID' => $val->serviceLineSystemID, 'serviceLineCode' => $val->serviceLineID, 'documentSystemID' => $params['document'], 'documentID' => $val->documentID, 'documentSystemCode' => $params["autoID"], 'documentCode' => $sorceDocument[$docInforArr["documentCodeColumnName"]], 'approvalLevelID' => $val->approvalLevelID, 'rollID' => $val->rollMasterID, 'approvalGroupID' => $val->approvalGroupID, 'rollLevelOrder' => $val->rollLevel, 'docConfirmedDate' => now(), 'docConfirmedByEmpSystemID' => $empInfo->employeeSystemID, 'docConfirmedByEmpID' => $empInfo->empID, 'timeStamp' => NOW(), 'reference_email' => $email_in);
                                                }
                                            } else {
                                                return ['success' => false, 'message' => trans('custom.please_set_approval_group')];
                                            }
                                        }
                                    } else {
                                        return ['success' => false, 'message' => trans('custom.no_approval_setup_created')];
                                    }
                                }
                                // insert rolls to document approved table
                                DocumentApproved::insert($documentApproved);

                                $documentApproved = DocumentApproved::where("documentSystemID", $params["document"])
                                    ->where("documentSystemCode", $sorceDocument[$docInforArr["primarykey"]])
                                    ->where("rollLevelOrder", 1)
                                    ->first();
                                if ($documentApproved) {
                                    if(isset($params['isAutoCreateDocument']) && $params['isAutoCreateDocument']){
                                    }
                                    else{
                                        if ($documentApproved->approvedYN == 0) {
                                            $companyDocument = CompanyDocumentAttachment::where('companySystemID', $documentApproved->companySystemID)
                                                ->where('documentSystemID', $reference_document_id)
                                                ->first();

                                            if (empty($companyDocument)) {
                                                return ['success' => false, 'message' => trans('custom.policy_not_found')];
                                            }

                                            $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproved->approvalGroupID)
                                                ->whereHas('employee', function ($q) {
                                                    $q->where('discharegedYN', 0);
                                                })
                                                ->where('companySystemID', $documentApproved->companySystemID)
                                                ->where('documentSystemID', $reference_document_id)
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
                                            $document = DocumentMaster::where('documentSystemID', $documentApproved->documentSystemID)->first();
                                            $file = [];

                                            if($params["document"] == 117 )
                                            {
                                                $document->documentDescription = $sorceDocument->type == 1?'Edit request':'Amend request';
                                            }

                                            if($params["document"] == 118 )
                                            {
                                                $document->documentDescription = $sorceDocument->type == 1?'Edit confirm request':'Amend confirm request';
                                                $companySystemId = $documentApproved->companySystemID;

                                                $amendmentsList = DocumentModifyRequest::select('id','documentSystemCode','document_master_id')
                                                    ->with(['documentAttachments'=> function ($q) use ($companySystemId){
                                                        $q->select('documentSystemCode','attachmentID','originalFileName','path')
                                                            ->where('companySystemID',$companySystemId)
                                                            ->where('documentSystemID',108)
                                                            ->where('attachmentType',3);
                                                    }])
                                                    ->whereHas('documentAttachments', function($q2) use ($companySystemId) {
                                                        $q2->where('companySystemID',$companySystemId)
                                                            ->where('documentSystemID',108)
                                                            ->where('attachmentType',3);
                                                    })
                                                    ->where('id',$params['autoID'])
                                                    ->where('companySystemID',$companySystemId)
                                                    ->first();

                                                if(!empty($amendmentsList)){
                                                    $documentAttachments = $amendmentsList->documentAttachments;
                                                    foreach ($documentAttachments as $amendments){
                                                        $file[$amendments->originalFileName] = Helper::getFileUrlFromS3($amendments->path);
                                                    }
                                                }
                                            }

                                            $subject = trans('email.pending_approval', ['documentDescription' => $document->documentDescription, 'documentCode' => $documentApproved->documentCode]);

                                            if($params["document"] == 56 )
                                            {
                                                $approvedDocNameBody = $document->documentDescription . ' <b>' . $masterRec->supplierName . '</b>';
                                                $subject = trans('email.pending_approval', ['documentDescription' => $document->documentDescription, 'documentCode' => $masterRec->supplierName]);
                                            }
                                            else if($params["document"] == 58 )
                                            {
                                                $approvedDocNameBody = $document->documentDescription . ' <b>' . $masterRec->CustomerName . '</b>';
                                                $subject = trans('email.pending_approval', ['documentDescription' => $document->documentDescription, 'documentCode' => $masterRec->CustomerName]);
                                            }
                                            else
                                            {
                                                $approvedDocNameBody = $document->documentDescription . ' <b>' . $documentApproved->documentCode . '</b>';
                                                $subject = trans('email.pending_approval', ['documentDescription' => $document->documentDescription, 'documentCode' => $documentApproved->documentCode]);
                                            }



                                            if($document->documentSystemID == 107){
                                                $approvedDocNameBody = $document->documentDescription . ', <b> "' . $documentApproved->suppliername->name . '"</b>';
                                            }

                                            if($document->documentSystemID == 108 || $document->documentSystemID == 113){
                                                $type = ['Tender', 'RFQ', 'RFI', 'RFP'];
                                                $approvedDocNameBody = $type[$params["document_type"]]. ' ' . ' <b>' . $documentApproved->documentCode . '</b>';
                                            }

                                       

                                            $documentValues = [107,108,113,117,118]; // srm related documents.
                                            $redirectUrl = (in_array($params["document"], $documentValues)) ? Helper::checkDomainErp($params["document"], $documentApproved->documentSystemCode) : Helper::checkDomai();

                                            $body = '<p>' . trans('email.is_pending_approval', ['attribute' => $approvedDocNameBody]) . '. <br><br>';

                                            if ($params["document"] == 117) {
                                                $ammendComment = Helper::getDocumentModifyRequestDetails($params['autoID']);
                                                $ammendText = '<b>Comment :</b> ' . $ammendComment['description'] . '<br>';
                                                $body .= $ammendText;
                                            }

                                            if ($document->documentSystemID == 113 || $document->documentSystemID == 108) {
                                                $type = ['Tender', 'RFQ', 'RFI', 'RFP'];
                                                $body .= '<p>' . trans('email.tender_title', ['type' => $type[$params["document_type"]], 'title' => $params["tender_title"]]) . '</p>';
                                                $body .= '<p>' . trans('email.tender_description', ['type' => $type[$params["document_type"]], 'description' => $params["tender_description"]]) . '</p>';
                                            }

                                            $body .= '<a href="' . $redirectUrl . '">' . trans('email.click_here_to_approve') . '</a></p>';

                                            if ($document->documentSystemID == 107){
                                                $subject = trans('email.pending_approval', ['documentDescription' => $document->documentDescription, 'documentCode' => '"' . $documentApproved->suppliername->name . '"']);
                                            }

                                            if($document->documentSystemID == 108 || $document->documentSystemID == 113){
                                                $type = ['Tender', 'RFQ', 'RFI', 'RFP'];
                                                $subject = trans('email.pending_approval', ['documentDescription' => $type[$params["document_type"]], 'documentCode' => $documentApproved->documentCode]);
                                            }

                                            $pushNotificationMessage = $document->documentDescription . " " . $documentApproved->documentCode . " is pending for your approval.";
                                            foreach ($approvalList as $da) {
                                                if ($da->employee) {
                                                    $emails[] = array(
                                                        'empSystemID' => $da->employee->employeeSystemID,
                                                        'companySystemID' => $documentApproved->companySystemID,
                                                        'docSystemID' => $documentApproved->documentSystemID,
                                                        'alertMessage' => $subject,
                                                        'emailAlertMessage' => $body,
                                                        'docSystemCode' => $documentApproved->documentSystemCode,
                                                        'attachmentList'=> $file
                                                    );

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
                                                $ivmsPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 47)
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

                                            $notifyConfirm = (isset($params['fromUpload']) && $params['fromUpload']) ? false : true;

                                            if ($notifyConfirm) {
                                                $sendEmail = Email::sendEmail($emails);

                                                if (!$sendEmail["success"]) {
                                                    return ['success' => false, 'message' => $sendEmail["message"]];
                                                }


                                                if(isset($sendEmail['unverifiedEmailMsg']) && !empty($sendEmail['unverifiedEmailMsg']))
                                                {
                                                    $unverifiedEmails = $sendEmail['unverifiedEmailMsg'];
                                                //    event(new UnverifiedEmailEvent($unverifiedEmails));
                                                }
                                                $jobPushNotification = PushNotification::dispatch($pushNotificationArray, $pushNotificationUserIds, 1);

                                                $webPushData = [
                                                    'title' => $pushNotificationMessage,
                                                    'body' => '',
                                                    'url' => $redirectUrl,
                                                ];
                                            }

                                            // WebPushNotificationService::sendNotification($webPushData, 1, $pushNotificationUserIds);

                                        }
                                    }
                                }

                                DB::commit();
                                return ['success' => true, 'message' => trans('custom.successfully_document_confirmed'), 'data' => $unverifiedEmails];

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