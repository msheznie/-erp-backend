<?php

namespace App\helper\Workflow\API;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models;
use App\Models\ProcumentOrder;
use App\Models\GRVMaster;
use App\Models\BookInvSuppMaster;
use App\Models\SupplierMaster;
use App\Models\DebitNote;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\AssetVerificationDetail;
use App\Models\FixedAssetMaster;
use App\Models\SupplierRegistrationLink;
use App\Models\InterCompanyAssetDisposal;
use App\Jobs\CreateSupplierTransactions;
use App\Jobs\CreateGRVSupplierInvoice;
use App\Jobs\ItemLedgerInsert;
use App\Jobs\CreateStockReceive;
use App\Jobs\CreateSupplierInvoice;
use App\Jobs\BankLedgerInsert;
use App\Jobs\BudgetAdjustment;
use App\Jobs\BudgetAdditionAdjustment;
use App\Jobs\GeneralLedgerInsert;
use App\Jobs\UnbilledGRVInsert;
use App\Jobs\WarehouseItemUpdate;
use App\Jobs\CreateConsoleJV;
use App\Jobs\EliminationLedgerInsert;
use App\Jobs\CreateCustomerInvoice;
use App\Jobs\CreateAccumulatedDepreciation;
use App\Jobs\PushNotification;
use App\Services\BudgetConsumptionService;
use App\Services\GeneralLedger\GlPostedDateService;
use App\Services\BudgetReviewService;
use App\Services\BudgetHistoryService;
use App\Services\AssetTransferService;
use App\Services\HrMonthlyDeductionService;
use App\Services\AssignedServices\SupplierAssignService;
use App\Services\AssignedServices\CustomerAssignService;
use App\Services\CurrencyConversionService;
use App\Services\ChartOfAccountDependency;
use App\Services\CreateCustomerThirdPartyInvoice;
use App\helper\SupplierRegister;
use App\helper\Helper;
use App\helper\Email;
use App\helper\SendEmailForDocument;

class DocumentApproveApi
{
    public static function approveDocumentForApi($input)
    {

        $docInforArr = array('tableName' => '', 'modelName' => '', 'primarykey' => '', 'approvedColumnName' => '', 'approvedBy' => '', 'approvedBySystemID' => '', 'approvedDate' => '', 'approveValue' => '', 'confirmedYN' => '', 'confirmedEmpSystemID' => '');

        $dataBase = (isset($input['db'])) ? $input['db'] : "";
        switch ($input["documentSystemID"]) { // check the document id and set relavant parameters
            case 57:
                $docInforArr["tableName"] = 'itemmaster';
                $docInforArr["modelName"] = 'ItemMaster';
                $docInforArr["primarykey"] = 'itemCodeSystem';
                $docInforArr["approvedColumnName"] = 'itemApprovedYN';
                $docInforArr["approvedBy"] = 'itemApprovedBy';
                $docInforArr["approvedBySystemID"] = 'itemApprovedBySystemID';
                $docInforArr["approvedDate"] = 'itemApprovedDate';
                $docInforArr["approveValue"] = 1;
                $docInforArr["confirmedYN"] = "itemConfirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "itemConfirmedByEMPSystemID";
                break;
            case 56:
                $docInforArr["tableName"] = 'suppliermaster';
                $docInforArr["modelName"] = 'SupplierMaster';
                $docInforArr["primarykey"] = 'supplierCodeSystem';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedby';
                $docInforArr["approvedBySystemID"] = 'approvedEmpSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = 1;
                $docInforArr["confirmedYN"] = "supplierConfirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "supplierConfirmedEmpSystemID";
                break;
            case 86:
                $docInforArr["tableName"] = 'registeredsupplier';
                $docInforArr["modelName"] = 'RegisteredSupplier';
                $docInforArr["primarykey"] = 'supplierName';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedby';
                $docInforArr["approvedBySystemID"] = 'approvedEmpSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = 1;
                $docInforArr["confirmedYN"] = "supplierConfirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "supplierConfirmedEmpSystemID";
                break;
            case 58:
                $docInforArr["tableName"] = 'customermaster';
                $docInforArr["modelName"] = 'CustomerMaster';
                $docInforArr["primarykey"] = 'customerCodeSystem';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedEmpID';
                $docInforArr["approvedBySystemID"] = 'approvedEmpSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = 1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedEmpSystemID";
                break;
            case 59:
                $docInforArr["tableName"] = 'chartofaccounts';
                $docInforArr["modelName"] = 'ChartOfAccount';
                $docInforArr["primarykey"] = 'chartOfAccountSystemID';
                $docInforArr["approvedColumnName"] = 'isApproved';
                $docInforArr["approvedBy"] = 'approvedBy';
                $docInforArr["approvedBySystemID"] = 'approvedBySystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = 1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedEmpSystemID";
                break;
            case 2:

            case 5:
            case 52:
                $docInforArr["tableName"] = 'erp_purchaseordermaster';
                $docInforArr["modelName"] = 'ProcumentOrder';
                $docInforArr["primarykey"] = 'purchaseOrderID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "poConfirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "poConfirmedByEmpSystemID";
                break;
            case 1:
            case 50:
            case 51:
                $docInforArr["tableName"] = 'erp_purchaserequest';
                $docInforArr["modelName"] = 'PurchaseRequest';
                $docInforArr["primarykey"] = 'purchaseRequestID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "PRConfirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "PRConfirmedBySystemID";
                break;
            case 3: // GRV
                $docInforArr["tableName"] = 'erp_grvmaster';
                $docInforArr["modelName"] = 'GRVMaster';
                $docInforArr["primarykey"] = 'grvAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "grvConfirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "grvConfirmedByEmpSystemID";
                break;
            case 8: // material issue
                $docInforArr["tableName"] = 'erp_itemissuemaster';
                $docInforArr["modelName"] = 'ItemIssueMaster';
                $docInforArr["primarykey"] = 'itemIssueAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 9: // material request
                $docInforArr["tableName"] = 'erp_request';
                $docInforArr["modelName"] = 'MaterielRequest';
                $docInforArr["primarykey"] = 'RequestID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "ConfirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "ConfirmedBySystemID";
                break;
            case 12: // stock return
                $docInforArr["tableName"] = 'erp_itemreturnmaster';
                $docInforArr["modelName"] = 'ItemReturnMaster';
                $docInforArr["primarykey"] = 'itemReturnAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 13: // stock transfer
                $docInforArr["tableName"] = 'erp_stocktransfer';
                $docInforArr["modelName"] = 'StockTransfer';
                $docInforArr["primarykey"] = 'stockTransferAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 10: // stock receive
                $docInforArr["tableName"] = 'erp_stockreceive';
                $docInforArr["modelName"] = 'StockReceive';
                $docInforArr["primarykey"] = 'stockReceiveAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 61: // Inventory reclassification
                $docInforArr["tableName"] = 'erp_inventoryreclassification';
                $docInforArr["modelName"] = 'InventoryReclassification';
                $docInforArr["primarykey"] = 'inventoryreclassificationID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 20: // customer invoice
                $docInforArr["tableName"] = 'erp_custinvoicedirect';
                $docInforArr["modelName"] = 'CustomerInvoiceDirect';
                $docInforArr["primarykey"] = 'custInvoiceDirectAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 24: // purchase return
                $docInforArr["tableName"] = 'erp_purchasereturnmaster';
                $docInforArr["modelName"] = 'PurchaseReturn';
                $docInforArr["primarykey"] = 'purhaseReturnAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 7: // stock adjustment
                $docInforArr["tableName"] = 'erp_stockadjustment';
                $docInforArr["modelName"] = 'StockAdjustment';
                $docInforArr["primarykey"] = 'stockAdjustmentAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 19: // credit note
                $docInforArr["tableName"] = 'erp_creditnote';
                $docInforArr["modelName"] = 'CreditNote';
                $docInforArr["primarykey"] = 'creditNoteAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 15: // debit note
                $docInforArr["tableName"] = 'erp_debitnote';
                $docInforArr["modelName"] = 'DebitNote';
                $docInforArr["primarykey"] = 'debitNoteAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 11: // supplier invoice
                $docInforArr["tableName"] = 'erp_bookinvsuppmaster';
                $docInforArr["modelName"] = 'BookInvSuppMaster';
                $docInforArr["primarykey"] = 'bookingSuppMasInvAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 4: // Payment voucher
                $docInforArr["tableName"] = 'erp_paysupplierinvoicemaster';
                $docInforArr["modelName"] = 'PaySupplierInvoiceMaster';
                $docInforArr["primarykey"] = 'PayMasterAutoId';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 21: // Bank Receipt Voucher
                $docInforArr["tableName"] = 'erp_customerreceivepayment';
                $docInforArr["modelName"] = 'CustomerReceivePayment';
                $docInforArr["primarykey"] = 'custReceivePaymentAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 62: // Bank Reconciliation
                $docInforArr["tableName"] = 'erp_bankrecmaster';
                $docInforArr["modelName"] = 'BankReconciliation';
                $docInforArr["primarykey"] = 'bankRecAutoID';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 63: // Asset Capitlaization
                $docInforArr["tableName"] = 'erp_fa_assetcapitalization';
                $docInforArr["modelName"] = 'AssetCapitalization';
                $docInforArr["primarykey"] = 'capitalizationID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 64: // Bank Transfer
                $docInforArr["tableName"] = 'erp_paymentbanktransfer';
                $docInforArr["modelName"] = 'PaymentBankTransfer';
                $docInforArr["primarykey"] = 'paymentBankTransferID';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 22: // Fixed Asset
                $docInforArr["tableName"] = 'erp_fa_asset_master';
                $docInforArr["modelName"] = 'FixedAssetMaster';
                $docInforArr["primarykey"] = 'faID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 17: // Journal Voucher
                $docInforArr["tableName"] = 'erp_jvmaster';
                $docInforArr["modelName"] = 'JvMaster';
                $docInforArr["primarykey"] = 'jvMasterAutoId';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 23: // Asset depreciation
                $docInforArr["tableName"] = 'erp_fa_depmaster';
                $docInforArr["modelName"] = 'FixedAssetDepreciationMaster';
                $docInforArr["primarykey"] = 'depMasterAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 46: // budget transfer
                $docInforArr["tableName"] = 'erp_budgettransferform';
                $docInforArr["modelName"] = 'BudgetTransferForm';
                $docInforArr["primarykey"] = 'budgetTransferFormAutoID';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 65: // budget
                $docInforArr["tableName"] = 'erp_budgetmaster';
                $docInforArr["modelName"] = 'BudgetMaster';
                $docInforArr["primarykey"] = 'budgetmasterID';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 41: // Asset Disposal
                $docInforArr["tableName"] = 'erp_fa_asset_disposalmaster';
                $docInforArr["modelName"] = 'AssetDisposalMaster';
                $docInforArr["primarykey"] = 'assetdisposalMasterAutoID';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confimedByEmpSystemID";
                break;
            case 66: // Bank Account
                $docInforArr["tableName"] = 'erp_bankaccount';
                $docInforArr["modelName"] = 'BankAccount';
                $docInforArr["primarykey"] = 'bankAccountAutoID';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedByEmpID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = 1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 67: // Sales Quotation
            case 68: // Sales Order
                $docInforArr["tableName"] = 'erp_quotationmaster';
                $docInforArr["modelName"] = 'QuotationMaster';
                $docInforArr["primarykey"] = 'quotationMasterID';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedbyEmpID';
                $docInforArr["approvedBySystemID"] = 'approvedEmpSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 71: // Delivery Order
                $docInforArr["tableName"] = 'erp_delivery_order';
                $docInforArr["modelName"] = 'DeliveryOrder';
                $docInforArr["primarykey"] = 'deliveryOrderID';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedbyEmpID';
                $docInforArr["approvedBySystemID"] = 'approvedEmpSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 87: // SalesReturn
                $docInforArr["tableName"] = 'salesreturn';
                $docInforArr["modelName"] = 'SalesReturn';
                $docInforArr["primarykey"] = 'id';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedbyEmpID';
                $docInforArr["approvedBySystemID"] = 'approvedEmpSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 96: // Currency Conversion
                $docInforArr["tableName"] = 'currency_conversion_master';
                $docInforArr["modelName"] = 'CurrencyConversionMaster';
                $docInforArr["primarykey"] = 'id';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedby';
                $docInforArr["approvedBySystemID"] = 'approvedEmpSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = 1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "ConfirmedBySystemID";
                break;
            case 97: // stock count
                $docInforArr["tableName"] = 'erp_stockcount';
                $docInforArr["modelName"] = 'StockCount';
                $docInforArr["primarykey"] = 'stockCountAutoID';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 104: // vat return filling
                $docInforArr["tableName"] = 'vat_return_filling_master';
                $docInforArr["modelName"] = 'VatReturnFillingMaster';
                $docInforArr["primarykey"] = 'id';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedEmpID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 100:
                $docInforArr["tableName"] = 'erp_budget_contingency';
                $docInforArr["modelName"] = 'ContingencyBudgetPlan';
                $docInforArr["primarykey"] = 'ID';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = 1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 102:
                $docInforArr["tableName"] = 'erp_budgetaddition';
                $docInforArr["modelName"] = 'ErpBudgetAddition';
                $docInforArr["primarykey"] = 'id';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedByUserSystemID';
                $docInforArr["approvedBySystemID"] = 'approvedEmpID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = 1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 99: // asset verification
                $docInforArr["tableName"] = 'erp_fa_asset_verification';
                $docInforArr["modelName"] = 'AssetVerification';
                $docInforArr["primarykey"] = 'id';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 103: // asset Transfer
                $docInforArr["tableName"] = 'erp_fa_fa_asset_transfer';
                $docInforArr["modelName"] = 'ERPAssetTransfer';
                $docInforArr["primarykey"] = 'id';
                $docInforArr["approvedColumnName"] = 'approved_yn';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approved_by_emp_id';
                $docInforArr["approvedDate"] = 'approved_date';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmed_yn";
                $docInforArr["confirmedEmpSystemID"] = "confirmed_by_emp_id";
                break;
            case 106:
                $docInforArr["tableName"] = 'appointment';
                $docInforArr["modelName"] = 'Appointment';
                $docInforArr["primarykey"] = 'id';
                $docInforArr["approvedColumnName"] = 'approved_yn';
                $docInforArr["approvedBy"] = 'approved_by_emp_name';
                $docInforArr["approvedBySystemID"] = 'approved_by_emp_id';
                $docInforArr["approvedDate"] = 'approved_date';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmed_yn";
                $docInforArr["confirmedEmpSystemID"] = "confirmed_by_emp_id";
                break;
            case 107:  //Supper registration
                $docInforArr["tableName"] = 'srm_supplier_registration_link';
                $docInforArr["modelName"] = 'SupplierRegistrationLink';
                $docInforArr["primarykey"] = 'id';
                $docInforArr["approvedColumnName"] = 'approved_yn';
                $docInforArr["approvedBy"] = 'approved_by_emp_name';
                $docInforArr["approvedBySystemID"] = 'approved_by_emp_id';
                $docInforArr["approvedDate"] = 'approved_date';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmed_yn";
                $docInforArr["confirmedEmpSystemID"] = "confirmed_by_emp_id";
                break;
            case 108: //SRM Tender
                $docInforArr["tableName"] = 'srm_tender_master';
                $docInforArr["modelName"] = 'TenderMaster';
                $docInforArr["primarykey"] = 'id';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approved_by_emp_name';
                $docInforArr["approvedBySystemID"] = 'approved_by_user_system_id';
                $docInforArr["approvedDate"] = 'approved_date';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmed_yn";
                $docInforArr["confirmedEmpSystemID"] = "confirmed_by_emp_system_id";
                break;
            case 69: // Console Journal Voucher
                $docInforArr["tableName"] = 'erp_consolejvmaster';
                $docInforArr["modelName"] = 'ConsoleJVMaster';
                $docInforArr["primarykey"] = 'consoleJvMasterAutoId';
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["approvedBy"] = 'approvedByUserID';
                $docInforArr["approvedBySystemID"] = 'approvedByUserSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                $docInforArr["approveValue"] = -1;
                $docInforArr["confirmedYN"] = "confirmedYN";
                $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                break;
            case 127:
                $docInforArr["modelName"] = 'SRMTenderPaymentProof';
                $docInforArr["approvedColumnName"] = 'approved_yn';
                $docInforArr["approvedBy"] = 'approved_by_emp_id';
                $docInforArr["approvedBySystemID"] = 'approved_emp_system_id';
                $docInforArr["approvedDate"] = 'approved_date';
                $docInforArr["approveValue"] = 1;
                $docInforArr["confirmedYN"] = "confirmed_yn";
                $docInforArr["confirmedEmpSystemID"] = "confirmed_by_emp_system_id";
                break;
            default:
                return ['success' => false, 'message' => trans('custom.document_id_not_found')];
        }


        //return ['success' => true , 'message' => $docInforArr];
        DB::beginTransaction();
        try {
            $userMessage = trans('custom.successfully_approved_the_document');
            $more_data = [];
            $userMessageE = '';
            $docApproved = Models\DocumentApproved::find($input["documentApprovedID"]);
            if ($docApproved) {

                if(isset($input['empID'])) {
                    // get current employee detail
                    $empInfo = Models\Employee::with(['profilepic', 'user_data' => function($query) {
                        $query->select('uuid', 'employee_id');
                    }])->find($input['empID']);
                }else {
                    // get current employee detail
                    $empInfo = Models\Employee::with(['profilepic', 'user_data' => function($query) {
                        $query->select('uuid', 'employee_id');
                    }])->find(11);
                }

                $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
                $isConfirmed = $namespacedModel::find($input["documentSystemCode"]);
                if (!$isConfirmed[$docInforArr["confirmedYN"]]) { // check document is confirmed or not
                    return ['success' => false, 'message' => trans('custom.document_not_confirmed')];
                }

                $policyConfirmedUserToApprove = '';

                if (in_array($input["documentSystemID"], [56, 57, 58, 59])) {
                    $policyConfirmedUserToApprove = Models\CompanyPolicyMaster::where('companyPolicyCategoryID', 31)
                        ->where('companySystemID', $isConfirmed['primaryCompanySystemID'])
                        ->first();
                } else {
                    $policyConfirmedUserToApprove = Models\CompanyPolicyMaster::where('companyPolicyCategoryID', 31)
                        ->where('companySystemID', $isConfirmed['companySystemID'])
                        ->first();
                }


                $companyDocument = Models\CompanyDocumentAttachment::where('companySystemID', $docApproved->companySystemID)
                    ->where('documentSystemID', $input["documentSystemID"])
                    ->first();
                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => trans('custom.policy_not_found_general')];
                }

                $checkUserHasApprovalAccess = Models\EmployeesDepartment::where('employeeGroupID', $docApproved->approvalGroupID)
                    ->where('companySystemID', $docApproved->companySystemID)
                    ->where('employeeSystemID', $empInfo->employeeSystemID)
                    ->where('documentSystemID', $input["documentSystemID"])
                    ->where('isActive', 1)
                    ->where('removedYN', 0);

                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $checkUserHasApprovalAccess = $checkUserHasApprovalAccess->where('ServiceLineSystemID', $docApproved->serviceLineSystemID);
                }


                if(!isset($input['isCheckPrivilages']) || (isset($input['isCheckPrivilages']) && $input['isCheckPrivilages']))
                {

                    $checkUserHasApprovalAccess = $checkUserHasApprovalAccess->whereHas('employee', function ($q) {
                        $q->where('discharegedYN', 0);
                    })
                        ->groupBy('employeeSystemID')
                        ->exists();

                    if (!$checkUserHasApprovalAccess) {
                        return ['success' => false, 'message' => trans('custom.no_access_approve_document')];
                    }

                    if ($policyConfirmedUserToApprove && $policyConfirmedUserToApprove['isYesNO'] == 0) {
                        if ($isConfirmed[$docInforArr["confirmedEmpSystemID"]] == $empInfo->employeeSystemID) {
                            return ['success' => false, 'message' => trans('custom.not_authorized_confirmed_person')];
                        }
                    }
                }


                if (["documentSystemID"] == 46) {
                    if ($isConfirmed['year'] != date("Y")) {
                        return ['success' => false, 'message' => trans('custom.budget_transfer_not_current_year')];
                    }
                }

                if ($docApproved->rejectedYN == -1) {
                    return ['success' => false, 'message' => trans('custom.level_already_rejected')];
                }

                //check document is already approved
                $isApproved = Models\DocumentApproved::where('documentApprovedID', $input["documentApprovedID"])->where('approvedYN', -1)->first();
                if (!$isApproved) {
                    $approvalLevel = Models\ApprovalLevel::find($input["approvalLevelID"]);

                    if ($approvalLevel) {
                        //Budget check on the 1st level approval for PR/DR/WR
                        if ($input["rollLevelOrder"] == 1) {
                            if (BudgetConsumptionService::budgetCheckDocumentList($input["documentSystemID"])) {
                                $budgetCheck = BudgetConsumptionService::checkBudget($input["documentSystemID"], $input["documentSystemCode"]);
                                if ($budgetCheck['status'] && $budgetCheck['message'] != "") {
                                    if (BudgetConsumptionService::budgetBlockUpdateDocumentList($input["documentSystemID"])) {
                                        $prMasterUpdate = $namespacedModel::find($input["documentSystemCode"])->update(['budgetBlockYN' => -1]);
                                    }
                                    DB::commit();
                                    return ['success' => false, 'message' => $budgetCheck['message']];
                                } else {
                                    if (BudgetConsumptionService::budgetBlockUpdateDocumentList($input["documentSystemID"])) {
                                        // update PR master table
                                        $prMasterUpdate = $namespacedModel::find($input["documentSystemCode"])->update(['budgetBlockYN' => 0]);
                                    }
                                }
                            }
                        }

                        if ($input['documentSystemID'] == 107 || $input['documentSystemID'] == 127) {
                            // pass below data for taking action in controller
                            $more_data = [
                                'numberOfLevels' => $approvalLevel->noOfLevels,
                                'currentLevel' => $input["rollLevelOrder"]
                            ];
                        }

                        if ($approvalLevel->noOfLevels == $input["rollLevelOrder"]) { // update the document after the final approval

                            $validatePostedDate = GlPostedDateService::validatePostedDate($input["documentSystemCode"], $input["documentSystemID"]);

                            if (!$validatePostedDate['status']) {
                                DB::rollback();
                                return ['success' => false, 'message' => $validatePostedDate['message']];
                            }

                            if($input["documentSystemID"] == 2){
                                $purchaseOrderMaster  = ProcumentOrder::find($input["documentSystemCode"]);
                                if ($purchaseOrderMaster && $purchaseOrderMaster->supplierID > 0) {

                                    $masterModel = ['supplierPrimaryCode' => $input["supplierPrimaryCode"], 'documentSystemID' => $input["documentSystemID"], 'documentID' => $purchaseOrderMaster->documentID, 'documentSystemCode' => $input["documentSystemCode"], 'documentCode' => $purchaseOrderMaster->purchaseOrderCode, 'documentDate' => $purchaseOrderMaster->createdDateTime, 'documentNarration' => $purchaseOrderMaster->narration, 'supplierID' => $purchaseOrderMaster->supplierID, 'supplierCode' => $purchaseOrderMaster->supplierPrimaryCode, 'supplierName' => $purchaseOrderMaster->supplierName, 'confirmedDate' => $purchaseOrderMaster->poConfirmedDate, 'confirmedBy' => $purchaseOrderMaster->poConfirmedByEmpSystemID, 'approvedDate' => $purchaseOrderMaster->approvedDate, 'lastApprovedBy' => $empInfo->employeeSystemID, 'transactionCurrency' => $purchaseOrderMaster->supplierTransactionCurrencyID, 'amount' => $purchaseOrderMaster->poTotalSupplierTransactionCurrency];
                                    CreateSupplierTransactions::dispatch($masterModel);
                                }
                            }

                            if($input["documentSystemID"] == 3){

                                $grvMaster  = GRVMaster::find($input["documentSystemCode"]);
                                if ($grvMaster && $grvMaster->supplierID > 0) {

                                    $masterModel = ['supplierPrimaryCode' => $input["supplierPrimaryCode"], 'documentSystemID' => $input["documentSystemID"], 'documentID' => $grvMaster->documentID, 'documentSystemCode' => $input["documentSystemCode"], 'documentCode' => $grvMaster->grvPrimaryCode, 'documentDate' => $grvMaster->createdDateTime, 'documentNarration' => $grvMaster->grvNarration, 'supplierID' => $grvMaster->supplierID, 'supplierCode' => $grvMaster->supplierPrimaryCode, 'supplierName' => $grvMaster->supplierName, 'confirmedDate' => $grvMaster->grvConfirmedDate, 'confirmedBy' => $grvMaster->grvConfirmedByEmpSystemID, 'approvedDate' => $grvMaster->approvedDate, 'lastApprovedBy' => $empInfo->employeeSystemID, 'transactionCurrency' => $grvMaster->supplierTransactionCurrencyID, 'amount' => $grvMaster->grvTotalSupplierTransactionCurrency];
                                    CreateSupplierTransactions::dispatch($masterModel);
                                }
                            }


                            if($input["documentSystemID"] == 11){

                                $supplierInvMaster  = BookInvSuppMaster::find($input["documentSystemCode"]);

                                if ($supplierInvMaster && $supplierInvMaster->supplierID > 0) {

                                    $supplierMaster = SupplierMaster::find($supplierInvMaster->supplierID);
                                    $masterModel = ['supplierPrimaryCode' => $input["supplierPrimaryCode"], 'documentSystemID' => $input["documentSystemID"], 'documentID' => $supplierInvMaster->documentID, 'documentSystemCode' => $input["documentSystemCode"], 'documentCode' => $supplierInvMaster->bookingInvCode, 'documentDate' => $supplierInvMaster->createdDateAndTime, 'documentNarration' => $supplierInvMaster->comments, 'supplierID' => $supplierInvMaster->supplierID, 'supplierCode' => $supplierMaster->primarySupplierCode, 'supplierName' => $supplierMaster->supplierName, 'confirmedDate' => $supplierInvMaster->confirmedDate, 'confirmedBy' => $supplierInvMaster->confirmedByEmpSystemID, 'approvedDate' => $supplierInvMaster->approvedDate, 'lastApprovedBy' => $empInfo->employeeSystemID, 'transactionCurrency' => $supplierInvMaster->supplierTransactionCurrencyID, 'amount' => $supplierInvMaster->bookingAmountTrans];
                                    CreateSupplierTransactions::dispatch($masterModel);

                                }
                            }


                            if($input["documentSystemID"] == 15){

                                $debitNoteMaster  = DebitNote::find($input["documentSystemCode"]);
                                if ($debitNoteMaster && $debitNoteMaster->supplierID > 0) {

                                    $supplierMaster = SupplierMaster::find($debitNoteMaster->supplierID);
                                    $masterModel = ['supplierPrimaryCode' => $input["supplierPrimaryCode"], 'documentSystemID' => $input["documentSystemID"], 'documentID' => $debitNoteMaster->documentID, 'documentSystemCode' => $input["documentSystemCode"], 'documentCode' => $debitNoteMaster->debitNoteCode, 'documentDate' => $debitNoteMaster->createdDateAndTime, 'documentNarration' => $debitNoteMaster->comments, 'supplierID' => $debitNoteMaster->supplierID, 'supplierCode' => $supplierMaster->primarySupplierCode, 'supplierName' => $supplierMaster->supplierName, 'confirmedDate' => $debitNoteMaster->confirmedDate, 'confirmedBy' => $debitNoteMaster->confirmedByEmpSystemID, 'approvedDate' => $debitNoteMaster->approvedDate, 'lastApprovedBy' => $empInfo->employeeSystemID, 'transactionCurrency' => $debitNoteMaster->supplierTransactionCurrencyID, 'amount' => $debitNoteMaster->debitAmountTrans];
                                    CreateSupplierTransactions::dispatch($masterModel);
                                }
                            }

                            if($input["documentSystemID"] == 4){

                                $paySupplierMaster  = PaySupplierInvoiceMaster::find($input["documentSystemCode"]);
                                if ($paySupplierMaster && $paySupplierMaster->BPVsupplierID > 0) {

                                    $supplierMaster = SupplierMaster::find($paySupplierMaster->BPVsupplierID);
                                    $masterModel = ['supplierPrimaryCode' => $input["supplierPrimaryCode"], 'documentSystemID' => $input["documentSystemID"], 'documentID' => $paySupplierMaster->documentID, 'documentSystemCode' => $input["documentSystemCode"], 'documentCode' => $paySupplierMaster->BPVcode, 'documentDate' => $paySupplierMaster->createdDateTime, 'documentNarration' => $paySupplierMaster->BPVNarration, 'supplierID' => $paySupplierMaster->BPVsupplierID, 'supplierCode' => $supplierMaster->primarySupplierCode, 'supplierName' => $supplierMaster->supplierName, 'confirmedDate' => $paySupplierMaster->confirmedDate, 'confirmedBy' => $paySupplierMaster->confirmedByEmpSystemID, 'approvedDate' => $paySupplierMaster->approvedDate, 'lastApprovedBy' => $empInfo->employeeSystemID, 'transactionCurrency' => $paySupplierMaster->supplierTransCurrencyID, 'amount' => $paySupplierMaster->suppAmountDocTotal];
                                    CreateSupplierTransactions::dispatch($masterModel);
                                }
                            }

                            // create monthly deduction
                            if (
                                $input["documentSystemID"] == 4 &&
                                $input['createMonthlyDeduction'] == 1 &&
                                Helper::checkHrmsIntergrated($input['companySystemID'])
                            ) {

                                $monthly_ded = new HrMonthlyDeductionService($input['documentSystemCode']);
                                $message = $monthly_ded->create_monthly_deduction();

                                $more_data = ($message != '') ? ['custom_message' => $message] : [];
                            }

                            if ($input["documentSystemID"] == 99) { // asset verification
                                $verified_date = $isConfirmed['documentDate'];
                                AssetVerificationDetail::where('verification_id', $isConfirmed['id'])->get()->each(function ($asset) use ($verified_date) {
                                    FixedAssetMaster::where('faID', $asset['faID'])->update(['lastVerifiedDate' => $verified_date]);
                                });
                            }

                            if ($input["documentSystemID"] == 97) { //stock count negative validation
                                // $stockCountRes = StockCountService::updateStockCountAdjustmentDetail($input);
                                // if (!$stockCountRes['status']) {
                                //     DB::rollback();
                                //     return ['success' => false, 'message' => $stockCountRes['message']];
                                // }
                            }

                            $sourceModel = $namespacedModel::find($input["documentSystemCode"]);

                            if ($input["documentSystemID"] == 46) { //Budget transfer for review notfifications
                                $budgetBlockNotifyRes = BudgetReviewService::notfifyBudgetBlockRemoval($input['documentSystemID'], $input['documentSystemCode']);
                                if (!$budgetBlockNotifyRes['status']) {
                                    DB::rollback();
                                    return ['success' => false, 'message' => $budgetBlockNotifyRes['message']];
                                }
                            }

                            if ($input["documentSystemID"] == 65) { //write budget to history table
                                $budgetHistoryRes = BudgetHistoryService::updateHistory($input['documentSystemCode']);
                                if (!$budgetHistoryRes['status']) {
                                    DB::rollback();
                                    return ['success' => false, 'message' => $budgetHistoryRes['message']];
                                }
                            }

                            if (in_array($input["documentSystemID"], [3, 8, 12, 13, 10, 20, 61, 24, 7, 19, 15, 11, 4, 21, 22, 17, 23, 41, 71, 87, 97])) { // already GL entry passed Check
                                $outputGL = Models\GeneralLedger::where('documentSystemCode', $input["documentSystemCode"])->where('documentSystemID', $input["documentSystemID"])->first();
                                if ($outputGL) {
                                    return ['success' => false, 'message' => trans('custom.gl_entries_already_passed')];
                                }
                            }

                            if ($input["documentSystemID"] == 103) { // Asset Transfer
                                $generatePR = AssetTransferService::generatePRForAssetTransfer($input);
                                if (!$generatePR['status']) {
                                    DB::rollback();
                                    return ['success' => false, 'message' => $generatePR['message']];
                                }
                            }

                            if(isset($input['approvedDate'])) {
                                $finalupdate = $namespacedModel::find($input["documentSystemCode"])->update([$docInforArr["approvedColumnName"] => $docInforArr["approveValue"], $docInforArr["approvedBy"] => $empInfo->empID, $docInforArr["approvedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["approvedDate"] => $input['approvedDate']]);

                            }else {
                                $finalupdate = $namespacedModel::find($input["documentSystemCode"])->update([$docInforArr["approvedColumnName"] => $docInforArr["approveValue"], $docInforArr["approvedBy"] => $empInfo->empID, $docInforArr["approvedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["approvedDate"] => now()]);

                            }

                            $masterData = ['documentSystemID' => $docApproved->documentSystemID, 'autoID' => $docApproved->documentSystemCode, 'companySystemID' => $docApproved->companySystemID, 'employeeSystemID' => $empInfo->employeeSystemID];

                            $masterDataDEO = ['documentSystemID' => $docApproved->documentSystemID, 'id' => $docApproved->id, 'companySystemID' => $docApproved->companySystemID, 'employeeSystemID' => $empInfo->employeeSystemID];

                            if ($input["documentSystemID"] == 57) { //Auto assign item to itemassign table
                                $itemMaster = DB::table('itemmaster')->selectRaw('itemCodeSystem,primaryCode as itemPrimaryCode,secondaryItemCode,barcode,itemDescription,unit as itemUnitOfMeasure,itemUrl,primaryCompanySystemID as companySystemID,primaryCompanyID as companyID,financeCategoryMaster,financeCategorySub, -1 as isAssigned,companymaster.localCurrencyID as wacValueLocalCurrencyID,companymaster.reportingCurrency as wacValueReportingCurrencyID,NOW() as timeStamp, faFinanceCatID')->join('companymaster', 'companySystemID', '=', 'primaryCompanySystemID')->where('itemCodeSystem', $input["documentSystemCode"])->first();
                                $itemAssign = Models\ItemAssigned::insert(collect($itemMaster)->toArray());
                            }

                            if ($input["documentSystemID"] == 56) { //Auto assign item to supplier table
                                $supplierAssignRes = SupplierAssignService::assignSupplier($input["documentSystemCode"], $docApproved->companySystemID);
                                if (!$supplierAssignRes['status']) {
                                    DB::rollback();
                                    return ['success' => false, 'message' => trans('custom.error_assign_supplier')];
                                }
                            }

                            if ($input["documentSystemID"] == 58) { //Auto assign customer
                                $supplierAssignRes = CustomerAssignService::assignCustomer($input["documentSystemCode"], $docApproved->companySystemID);
                                if (!$supplierAssignRes['status']) {
                                    DB::rollback();
                                    return ['success' => false, 'message' => trans('custom.error_assign_customer')];
                                }
                            }

                            if ($input["documentSystemID"] == 86) { //insert data to supplier table
                                $resSupplierRegister = SupplierRegister::registerSupplier($input);
                                if (!$resSupplierRegister['status']) {
                                    DB::rollback();
                                    return ['success' => false, 'message' => $resSupplierRegister['message']];
                                }
                            }

                            if ($input["documentSystemID"] == 96) { //insert data to conversion table
                                $conversionRes = CurrencyConversionService::setConversion($input);
                                if (!$conversionRes['status']) {
                                    DB::rollback();
                                    return ['success' => false, 'message' => $conversionRes['message']];
                                }
                            }

                            if ($input["documentSystemID"] == 59) { //Auto assign item to Chart Of Account
                                $chartOfAccount = $namespacedModel::selectRaw('primaryCompanySystemID as companySystemID,primaryCompanyID as companyID,chartOfAccountSystemID,AccountCode,AccountDescription,masterAccount,catogaryBLorPLID,catogaryBLorPL,controllAccountYN,controlAccountsSystemID,controlAccounts,isActive,isBank,AllocationID,relatedPartyYN,-1 as isAssigned,NOW() as timeStamp')->find($input["documentSystemCode"]);
                                $chartOfAccountAssign = Models\ChartOfAccountsAssigned::insert($chartOfAccount->toArray());
                                $assignResp = ChartOfAccountDependency::assignToReports($input["documentSystemCode"]);
                                if (!$assignResp['status']) {
                                    DB::rollback();
                                    return ['success' => false, 'message' => $assignResp['message']];
                                }

                                $templateAssignRes = ChartOfAccountDependency::assignToTemplateCategory($input["documentSystemCode"], $docApproved->companySystemID);
                                if (!$templateAssignRes['status']) {
                                    DB::rollback();
                                    return ['success' => false, 'message' => $templateAssignRes['message']];
                                }

                                $checkAndAssignRelatedParty = ChartOfAccountDependency::checkAndAssignToRelatedParty($input["documentSystemCode"], $docApproved->companySystemID);
                                if (!$checkAndAssignRelatedParty['status']) {
                                    DB::rollback();
                                    return ['success' => false, 'message' => $checkAndAssignRelatedParty['message']];
                                }
                            }

                            if ($input["documentSystemID"] == 63) { //Create Asset Disposal
                                $assetDisposal = Helper::generateAssetDisposal($masterData);
                            }

                            if ($input["documentSystemID"] == 17) { //Create Accrual JV Reversal

                                $jvMasterData = $namespacedModel::find($input["documentSystemCode"]);

                                if ($jvMasterData->jvType == 1 && $jvMasterData->isReverseAccYN == 0) {
                                    $accrualJournalVoucher = Helper::generateAccrualJournalVoucher($input["documentSystemCode"]);
                                } else if ($jvMasterData->jvType == 5 && $jvMasterData->isReverseAccYN == 0) {
                                    $POAccrualJournalVoucher = Helper::generatePOAccrualJournalVoucher($input["documentSystemCode"]);
                                }
                            }

                            // insert the record to item ledger

                            if (in_array($input["documentSystemID"], [3, 8, 12, 13, 10, 61, 24, 7, 20, 71, 87, 97, 11])) {

                                if ($input['documentSystemID'] == 71) {
                                    if ($sourceModel->isFrom != 5) {
                                        $jobIL = ItemLedgerInsert::dispatch($masterData, $dataBase);
                                    }
                                } else if ($input['documentSystemID'] == 11) {
                                    if ($sourceModel->documentType == 3) {
                                        $jobIL = ItemLedgerInsert::dispatch($masterData, $dataBase);
                                    }
                                } else {
                                    $jobIL = ItemLedgerInsert::dispatch($masterData, $dataBase);
                                }
                            }

                            if ($input["documentSystemID"] == 11) {
                                if ($sourceModel->documentType == 1 && $sourceModel->createMonthlyDeduction) {
                                    $monthlyDedRes = HrMonthlyDeductionService::createMonthlyDeductionForSupplierInvoice($masterData);

                                    if (!$monthlyDedRes['status']) {
                                        return ['success' => false, 'message' => $monthlyDedRes['message']];
                                    }
                                }
                            }


                            // insert the record to general ledger

                            if (in_array($input["documentSystemID"], [3, 8, 12, 13, 10, 20, 61, 24, 7, 19, 15, 11, 4, 21, 22, 17, 23, 41, 71, 87, 97])) {
                                if ($input['documentSystemID'] == 71) {
                                    if ($sourceModel->isFrom != 5) {
                                        $jobGL = GeneralLedgerInsert::dispatch($masterData, $dataBase);
                                    }
                                } else if ($input['documentSystemID'] == 17) {
                                    if ($sourceModel->jvType != 9) {
                                        $jobGL = GeneralLedgerInsert::dispatch($masterData, $dataBase);
                                    }
                                } else {
                                    $jobGL = GeneralLedgerInsert::dispatch($masterData, $dataBase);
                                }
                                if ($input["documentSystemID"] == 3) {
                                    $sourceData = $namespacedModel::find($input["documentSystemCode"]);
                                    $masterData['supplierID'] = $sourceData->supplierID;
                                    $jobUGRV = UnbilledGRVInsert::dispatch($masterData, $dataBase);
                                    $jobSI = CreateGRVSupplierInvoice::dispatch($input["documentSystemCode"], $dataBase);
                                    WarehouseItemUpdate::dispatch($input["documentSystemCode"]);

                                    if ($sourceData->interCompanyTransferYN == -1) {
                                        $consoleJVData = [
                                            'data' => InterCompanyAssetDisposal::where('grvID', $sourceData->grvAutoID)->first(),
                                            'type' => "INTER_ASSET_DISPOSAL"
                                        ];

                                        CreateConsoleJV::dispatch($consoleJVData);
                                    }
                                }

                                if ($input["documentSystemID"] == 21) {
                                    $sourceData = $namespacedModel::find($input["documentSystemCode"]);
                                    if ($sourceData->intercompanyPaymentID > 0) {
                                        $receiptData = [
                                            'data' => $sourceData,
                                            'type' => "FUND_TRANSFER"
                                        ];

                                        CreateConsoleJV::dispatch($receiptData);
                                    }
                                }

                            }

                            if ($input["documentSystemID"] == 69) {
                                $outputEL = Models\EliminationLedger::where('documentSystemCode', $input["documentSystemCode"])->where('documentSystemID', $input["documentSystemID"])->first();
                                if ($outputEL) {
                                    return ['success' => false, 'message' => trans('custom.elimination_ledger_entries_already_passed')];
                                }

                                $jobGL = EliminationLedgerInsert::dispatch($masterData);
                            }

                            if ($input["documentSystemID"] == 24) {
                                $updateReturnQty = Helper::updateReturnQtyInGrvDetails($masterData);
                                if (!$updateReturnQty["success"]) {
                                    return ['success' => false, 'message' => $updateReturnQty["message"]];
                                }

                                $updateReturnQtyInPo = Helper::updateReturnQtyInPoDetails($masterData);
                                if (!$updateReturnQtyInPo["success"]) {
                                    return ['success' => false, 'message' => $updateReturnQty["message"]];
                                }
                            }

                            if ($input["documentSystemID"] == 87) {

                                $updateReturnQtyInPo = Helper::updateReturnQtyInDeliveryOrderDetails($input["documentSystemCode"]);
                                if (!$updateReturnQtyInPo["success"]) {
                                    return ['success' => false, 'message' => "Success"];
                                }
                            }



                            if ($input["documentSystemID"] == 21) {
                                //$bankLedgerInsert = \App\Jobs\BankLedgerInsert::dispatch($masterData);
                                if ($sourceModel->pdcChequeYN == 0) {
                                    $bankLedgerInsert = Helper::appendToBankLedger($input["documentSystemCode"]);
                                }
                            }
                            if ($input["documentSystemID"] == 13 && !empty($sourceModel)) {
                                $jobCI = CreateStockReceive::dispatch($sourceModel, $dataBase);
                            }
                            if ($input["documentSystemID"] == 10 && !empty($sourceModel)) {
                                $jobSI = CreateSupplierInvoice::dispatch($sourceModel);
                            }
                            if ($input["documentSystemID"] == 4 && !empty($sourceModel)) {
                                //$jobPV = CreateReceiptVoucher::dispatch($sourceModel);
                                if ($sourceModel->invoiceType == 3) {
                                    $jobPV = Helper::generateCustomerReceiptVoucher($sourceModel);
                                    if (!$jobPV["success"]) {
                                        return ['success' => false, 'message' => $jobPV["message"]];
                                    }
                                } else if($sourceModel->invoiceType == 2){
                                    $jobPV = Helper::generatePaymentVoucher($sourceModel);
                                    if (!$jobPV["success"]) {
                                        return ['success' => false, 'message' => $jobPV["message"]];
                                    }
                                }
                                else {
                                    if ($sourceModel->pdcChequeYN == 0) {
                                        $bankLedger = BankLedgerInsert::dispatch($masterData);
                                    }
                                }
                            }

                            if ($input["documentSystemID"] == 46 && !empty($sourceModel)) {
                                $jobBTN = BudgetAdjustment::dispatch($sourceModel);
                            }

                            if ($input["documentSystemID"] == 102 && !empty($sourceModel)) { //Budget Addition Note Job
                                $jobBDA = BudgetAdditionAdjustment::dispatch($sourceModel);
                            }

                            if ($input["documentSystemID"] == 61) { //create fixed asset
                                $fixeAssetDetail = Models\InventoryReclassificationDetail::with(['master'])->where('inventoryreclassificationID', $input["documentSystemCode"])->get();
                                $qtyRangeArr = [];
                                if ($fixeAssetDetail) {
                                    $lastSerialNumber = 1;
                                    $lastSerial = Models\FixedAssetMaster::selectRaw('MAX(serialNo) as serialNo')->first();
                                    if ($lastSerial) {
                                        $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                                    }
                                    foreach ($fixeAssetDetail as $val) {
                                        if ($val["currentStockQty"]) {
                                            $qtyRange = range(1, $val["currentStockQty"]);
                                            if ($qtyRange) {
                                                foreach ($qtyRange as $qty) {
                                                    $documentCode = ($val["master"]["companyID"] . '\\FA' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));
                                                    $data["departmentID"] = 'AM';
                                                    $data["departmentSystemID"] = null;
                                                    $data["serviceLineSystemID"] = $val["master"]["serviceLineSystemID"];
                                                    $data["serviceLineCode"] = $val["master"]["serviceLineCode"];
                                                    $data["docOriginSystemCode"] = $val["inventoryreclassificationID"];
                                                    $data["docOrigin"] = $val["master"]["documentCode"];
                                                    $data["docOriginDetailID"] = $val["inventoryReclassificationDetailID"];
                                                    $data["companySystemID"] = $val["master"]["companySystemID"];
                                                    $data["companyID"] = $val["master"]["companyID"];
                                                    $data["documentSystemID"] = 22;
                                                    $data["documentID"] = 'FA';
                                                    $data["serialNo"] = $lastSerialNumber;
                                                    $data["itemCode"] = $val["itemSystemCode"];
                                                    $data["faCode"] = $documentCode;
                                                    $data["assetDescription"] = $val["itemDescription"];
                                                    $data["COSTUNIT"] = $val["unitCostLocal"];
                                                    $data["costUnitRpt"] = $val["unitCostRpt"];
                                                    $data["assetType"] = 1;
                                                    $data['createdPcID'] = gethostname();
                                                    $data['createdUserID'] = Helper::getEmployeeID();
                                                    $data['createdUserSystemID'] = Helper::getEmployeeSystemID();
                                                    $data["timestamp"] = date('Y-m-d H:i:s');
                                                    $qtyRangeArr[] = $data;
                                                    $lastSerialNumber++;
                                                }
                                            }
                                        }
                                    }
                                    $fixedAsset = Models\FixedAssetMaster::insert($qtyRangeArr);
                                }
                            }

                            //generate customer invoice or Direct GRV
                            if ($input["documentSystemID"] == 41 && !empty($sourceModel)) {
                                if ($sourceModel->disposalType == 1) {
                                    $jobCI = CreateCustomerInvoice::dispatch($sourceModel, $dataBase);
                                }
                                else if ($sourceModel->disposalType == 6) {
                                    $message = CreateCustomerThirdPartyInvoice::customerInvoiceCreate($sourceModel, $dataBase,$empInfo);

                                    if (!$message['status']) {
                                        DB::rollback();
                                        return ['success' => false, 'message' => $message['message']];
                                    }
                                }
                                $updateDisposed = Models\AssetDisposalDetail::ofMaster($input["documentSystemCode"])->get();
                                if (count($updateDisposed) > 0) {
                                    foreach ($updateDisposed as $val) {
                                        $faMaster = Models\FixedAssetMaster::find($val->faID)->update(['DIPOSED' => -1, 'disposedDate' => $sourceModel->disposalDocumentDate, 'assetdisposalMasterAutoID' => $input["documentSystemCode"]]);
                                    }
                                }
                            }


                            // generate asset costing
                            if ($input["documentSystemID"] == 22) {
                                $assetCosting = Helper::generateAssetCosting($sourceModel);
                            }

                            // insert the record to budget consumed data
                            if (BudgetConsumptionService::budgetConsumedDocumentList($input["documentSystemID"])) {

                                $budgetConsumedRes = BudgetConsumptionService::insertBudgetConsumedData($input["documentSystemID"], $input["documentSystemCode"]);
                                if (!$budgetConsumedRes['status']) {
                                    return ['success' => false, 'message' => $budgetConsumedRes['message']];
                                }
                            }

                            // adding records to budget consumption data
                            if ($input["documentSystemID"] == 11 || $input["documentSystemID"] == 4 || $input["documentSystemID"] == 15 || $input["documentSystemID"] == 19) {
                                $storingBudget = Helper::storeBudgetConsumption($masterData);
                            }

                            //sending email based on policy
                            if ($input["documentSystemID"] == 1 || $input["documentSystemID"] == 50 || $input["documentSystemID"] == 51 || $input["documentSystemID"] == 2 || $input["documentSystemID"] == 5 || $input["documentSystemID"] == 52 || $input["documentSystemID"] == 4) {
                                $sendingEmail = Helper::sendingEmailNotificationPolicy($masterData);
                            }

                            if ($input["documentSystemID"] == 107) {

                                $suppiler_info = SupplierRegistrationLink::where('id', '=', $docApproved->documentSystemCode)->first();
                                if (isset($suppiler_info) && isset($docApproved->reference_email) && !empty($docApproved->reference_email)) {

                                    $dataEmail['empEmail'] = $docApproved->reference_email;
                                    $dataEmail['companySystemID'] = $docApproved->companySystemID;
                                    $temp = '<p>Dear Supplier, <br /></p><p>Please be informed that your KYC has been approved. <br><br> Thank You. </p>';
                                    $dataEmail['alertMessage'] = "Registration Approved";
                                    $dataEmail['emailAlertMessage'] = $temp;
                                    $sendEmail = Email::sendEmailErp($dataEmail);
                                }
                            }

                            if ($input["documentSystemID"] == 106) {

                                $suppiler_info = SupplierRegistrationLink::where('id', '=', $docApproved->documentSystemCode)->first();
                                if (isset($docApproved->reference_email) && !empty($docApproved->reference_email)) {
                                    $dataEmail['empEmail'] = $docApproved->reference_email;
                                    $dataEmail['companySystemID'] = $docApproved->companySystemID;
                                    $temp = '<p>Dear Supplier, <br /></p><p>Please be informed that your appointment has been approved. <br><br> Thank You. </p>';
                                    $dataEmail['alertMessage'] = "Appoinment Approved";
                                    $dataEmail['emailAlertMessage'] = $temp;
                                    $sendEmail = Email::sendEmailErp($dataEmail);
                                }
                            }

                            if ($input["documentSystemID"] == 22) {
                                $acc_d = CreateAccumulatedDepreciation::dispatch($input["faID"], $database);
                            }
                            //

                        } else {
                            // update roll level in master table
                            $rollLevelUpdate = $namespacedModel::find($input["documentSystemCode"])->update(['RollLevForApp_curr' => $input["rollLevelOrder"] + 1]);
                        }

                        // update record in document approved table
                        if(isset($input['approvedDate'])) {
                            $approvedeDoc = $docApproved::find($input["documentApprovedID"])->update(['approvedYN' => -1, 'approvedDate' => $input['approvedDate'], 'approvedComments' => $input["approvedComments"], 'employeeID' => $empInfo->empID, 'employeeSystemID' => $empInfo->employeeSystemID]);

                        }else {
                            $approvedeDoc = $docApproved::find($input["documentApprovedID"])->update(['approvedYN' => -1, 'approvedDate' => now(), 'approvedComments' => $input["approvedComments"], 'employeeID' => $empInfo->empID, 'employeeSystemID' => $empInfo->employeeSystemID]);

                        }

                        $sourceModel = $namespacedModel::find($input["documentSystemCode"]);
                        $currentApproved = Models\DocumentApproved::find($input["documentApprovedID"]);
                        $emails = array();
                        $pushNotificationUserIds = [];
                        $pushNotificationArray = [];
                        if (!empty($sourceModel)) {
                            $document = Models\DocumentMaster::where('documentSystemID', $currentApproved->documentSystemID)->first();
                            $subjectName = $document->documentDescription . ' ' . $currentApproved->documentCode;
                            $bodyName = $document->documentDescription . ' ' . '<b>' . $currentApproved->documentCode . '</b>';

                            if ($sourceModel[$docInforArr["confirmedYN"]] == 1 || $sourceModel[$docInforArr["confirmedYN"]] == -1) {

                                if ($approvalLevel->noOfLevels == $input["rollLevelOrder"]) { // if fully approved
                                    $subject = trans('email.is_fully_approved', ['attribute' => $subjectName]);
                                    $body = trans('email.is_fully_approved', ['attribute' => $bodyName]) . " . ";
                                    $pushNotificationMessage = $subject;
                                    $pushNotificationUserIds[] = $sourceModel[$docInforArr["confirmedEmpSystemID"]];
                                } else {

                                    $companyDocument = Models\CompanyDocumentAttachment::where('companySystemID', $currentApproved->companySystemID)
                                        ->where('documentSystemID', $currentApproved->documentSystemID)
                                        ->first();

                                    if (empty($companyDocument)) {
                                        return ['success' => false, 'message' => trans('custom.policy_not_found')];
                                    }

                                    $nextLevel = $currentApproved->rollLevelOrder + 1;

                                    $nextApproval = Models\DocumentApproved::where('companySystemID', $currentApproved->companySystemID)
                                        ->where('documentSystemID', $currentApproved->documentSystemID)
                                        ->where('documentSystemCode', $currentApproved->documentSystemCode)
                                        ->where('rollLevelOrder', $nextLevel)
                                        ->first();

                                    $approvalList = Models\EmployeesDepartment::where('employeeGroupID', $nextApproval->approvalGroupID)
                                        ->whereHas('employee', function ($q) {
                                            $q->where('discharegedYN', 0);
                                        })
                                        ->where('companySystemID', $currentApproved->companySystemID)
                                        ->where('documentSystemID', $currentApproved->documentSystemID)
                                        ->where('isActive', 1)
                                        ->where('removedYN', 0);


                                    if ($companyDocument['isServiceLineApproval'] == -1) {
                                        $approvalList = $approvalList->where('ServiceLineSystemID', $currentApproved->serviceLineSystemID);
                                    }

                                    $approvalList = $approvalList
                                        ->with(['employee'])
                                        ->groupBy('employeeSystemID')
                                        ->get();

                                    $pushNotificationMessage = $subjectName . " is pending for your approval.";

                                  


                                    $redirectUrl = Helper::checkDomai();
                                    //$body = '<p>' . $approvedDocNameBody . ' is pending for your approval. <br><br><a href="' . $redirectUrl . '">Click here to approve</a></p>';
                                    $nextApprovalBody = trans('email.level_approved_pending_body', ['attribute' => $bodyName, 'level' => $currentApproved->rollLevelOrder, 'redirectUrl' => $redirectUrl]);

                                    $nextApprovalSubject = trans('email.level_approved_pending', ['attribute' => $subjectName, 'level' => $currentApproved->rollLevelOrder]);
                                    $nextApproveNameList = "";
                                    foreach ($approvalList as $da) {
                                        if ($da->employee) {

                                            $nextApproveNameList = $nextApproveNameList . '<br>' . $da->employee->empName;

                                            $emails[] = array(
                                                'empSystemID' => $da->employee->employeeSystemID,
                                                'companySystemID' => $nextApproval->companySystemID,
                                                'docSystemID' => $nextApproval->documentSystemID,
                                                'alertMessage' => $nextApprovalSubject,
                                                'emailAlertMessage' => $nextApprovalBody,
                                                'docSystemCode' => $nextApproval->documentSystemCode
                                            );

                                            $pushNotificationUserIds[] = $da->employee->employeeSystemID;
                                        }
                                    }

                                    $subject = trans('email.level_approved_sent_next', ['attribute' => $subjectName, 'level' => $currentApproved->rollLevelOrder]);
                                    $body = trans('email.level_approved_sent_next_body', ['attribute' => $bodyName, 'level' => $currentApproved->rollLevelOrder, 'nextApproveNameList' => $nextApproveNameList]);
                                }


                                $emails[] = array(
                                    'empSystemID' => $sourceModel[$docInforArr["confirmedEmpSystemID"]],
                                    'companySystemID' => $currentApproved->companySystemID,
                                    'docSystemID' => $currentApproved->documentSystemID,
                                    'alertMessage' => $subject,
                                    'emailAlertMessage' => $body,
                                    'docSystemCode' => $input["documentSystemCode"]
                                );

                                $pushNotificationArray['companySystemID'] = $currentApproved->companySystemID;
                                $pushNotificationArray['documentSystemID'] = $currentApproved->documentSystemID;
                                $pushNotificationArray['id'] = $currentApproved->documentSystemCode;
                                $pushNotificationArray['type'] = 1;
                                $pushNotificationArray['documentCode'] = $currentApproved->documentCode;
                                $pushNotificationArray['pushNotificationMessage'] = $pushNotificationMessage;
                            }
                        }

                        if ($input['documentSystemID'] == 2) {
                            SendEmailForDocument::approvedDocument($input);
                        }

                        $notifyConfirm = (isset($input['fromUpload']) && $input['fromUpload']) ? false : true;

                        if ($notifyConfirm) {
                            if(!isset($input['sendMail']) || (isset($input['sendMail']) && $input['sendMail'])) {
                                $sendEmail = Email::sendEmail($emails);

                                if (!$sendEmail["success"]) {
                                    return ['success' => false, 'message' => $sendEmail["message"]];
                                }
                            }


                            if(!isset($input['sendNotication']) || (isset($input['sendNotication']) && $input['sendNotication']))
                                $jobPushNotification = PushNotification::dispatch($pushNotificationArray, $pushNotificationUserIds, 1, $dataBase);
                        }


                        $webPushData = [
                            'title' => $pushNotificationMessage,
                            'body' => '',
                            'url' => isset($redirectUrl) ? $redirectUrl : "",
                        ];

                        // WebPushNotificationService::sendNotification($webPushData, 2, $pushNotificationUserIds, $dataBase);

                    } else {
                        return ['success' => false, 'message' => trans('custom.approval_level_not_found')];
                    }
                    DB::commit();
                    return ['success' => true, 'message' => $userMessage, 'data' => $more_data];
                } else {
                    return ['success' => false, 'message' => trans('custom.level_already_approved')];
                }
            } else {
                return ['success' => false, 'message' => trans('custom.no_records_found')];
            }
        } catch (\Exception $e) {
            DB::rollback();
            //$data = ['documentSystemCode' => $input['documentSystemCode'],'documentSystemID' => $input['documentSystemID']];
            //RollBackApproval::dispatch($data);
            Log::channel('document_approval')->error($e->getMessage());


            $msg = 'Error Occurred';
            if (in_array($e->getCode(), [404, 500])) {
                $msg = $e->getMessage();
            }

            return ['success' => false, 'message' => $msg];
            // return ['success' => false, 'message' => $e->getMessage()." Line:".$e->getLine()];

        }
    }
}