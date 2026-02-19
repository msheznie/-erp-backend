<?php

namespace App\helper\Workflow;

use Illuminate\Support\Facades\DB;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\ApprovalLevel;
use App\Models\CompanyDocumentAttachment;
use App\Models\EmployeesDepartment;
use App\Models\ApprovalGroups;
use App\Models\Employee;
use App\Models\PaymentTermTemplateAssigned;
use App\Models\PaymentTermTemplate;
use App\Traits\ApproveRejectTransaction;
use App\helper\email as Email;
use App\helper\Helper;

class DocumentReject
{
    public static function rejectDocument($input)
    {
        $bodyName = '';
        DB::beginTransaction();
        try {
            switch ($input["documentSystemID"]) {
                case 2:
                case 5:
                case 52:
                    $docInforArr["tableName"] = 'erp_purchaseordermaster';
                    $docInforArr["modelName"] = 'ProcumentOrder';
                    $docInforArr["primarykey"] = 'purchaseOrderID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "poConfirmedByEmpSystemID";
                    break;
                case 1:
                case 50:
                case 51:
                    $docInforArr["tableName"] = 'erp_purchaserequest';
                    $docInforArr["modelName"] = 'PurchaseRequest';
                    $docInforArr["primarykey"] = 'purchaseRequestID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "PRConfirmedBySystemID";
                    break;
                case 20: //Customer Invoice
                    $docInforArr["tableName"] = 'erp_custinvoicedirect';
                    $docInforArr["modelName"] = 'CustomerInvoiceDirect';
                    $docInforArr["primarykey"] = 'custInvoiceDirectAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 11: //Booking Supplier Invoice
                    $docInforArr["tableName"] = 'erp_bookinvsuppmaster';
                    $docInforArr["modelName"] = 'BookInvSuppMaster';
                    $docInforArr["primarykey"] = 'bookingSuppMasInvAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 46:
                    $docInforArr["tableName"] = 'erp_budgettransferform';
                    $docInforArr["modelName"] = 'BudgetTransferForm';
                    $docInforArr["primarykey"] = 'budgetTransferFormAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 65: // budget
                    $docInforArr["tableName"] = 'erp_budgetmaster';
                    $docInforArr["modelName"] = 'BudgetMaster';
                    $docInforArr["primarykey"] = 'budgetmasterID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 22: // Asset Costing
                    $docInforArr["tableName"] = 'erp_fa_asset_master';
                    $docInforArr["modelName"] = 'FixedAssetMaster';
                    $docInforArr["primarykey"] = 'faID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 23: // Asset Depreciation
                    $docInforArr["tableName"] = 'erp_fa_depmaster';
                    $docInforArr["modelName"] = 'FixedAssetDepreciationMaster';
                    $docInforArr["primarykey"] = 'depMasterAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 21: //  Customer Receipt Voucher
                    $docInforArr["tableName"] = 'erp_customerreceivepayment';
                    $docInforArr["modelName"] = 'CustomerReceivePayment';
                    $docInforArr["primarykey"] = 'custReceivePaymentAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 4: // Payment Voucher
                    $docInforArr["tableName"] = 'erp_paysupplierinvoicemaster';
                    $docInforArr["modelName"] = 'PaySupplierInvoiceMaster';
                    $docInforArr["primarykey"] = 'PayMasterAutoId';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 19: // Credit Note
                    $docInforArr["tableName"] = 'erp_creditnote';
                    $docInforArr["modelName"] = 'CreditNote';
                    $docInforArr["primarykey"] = 'creditNoteAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 13: // stock transfer
                    $docInforArr["tableName"] = 'erp_stocktransfer';
                    $docInforArr["modelName"] = 'StockTransfer';
                    $docInforArr["primarykey"] = 'stockTransferAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 10: // stock receive
                    $docInforArr["tableName"] = 'erp_stockreceive';
                    $docInforArr["modelName"] = 'StockReceive';
                    $docInforArr["primarykey"] = 'stockReceiveAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 15: // Debit Note
                    $docInforArr["tableName"] = 'erp_debitnote';
                    $docInforArr["modelName"] = 'DebitNote';
                    $docInforArr["primarykey"] = 'debitNoteAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 8: // Materiel Issue
                    $docInforArr["tableName"] = 'erp_itemissuemaster';
                    $docInforArr["modelName"] = 'ItemIssueMaster';
                    $docInforArr["primarykey"] = 'itemIssueAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 12: // Materiel Return
                    $docInforArr["tableName"] = 'erp_itemreturnmaster';
                    $docInforArr["modelName"] = 'ItemReturnMaster';
                    $docInforArr["primarykey"] = 'itemReturnAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 17: //  Journal Voucher
                    $docInforArr["tableName"] = 'erp_jvmaster';
                    $docInforArr["modelName"] = 'JvMaster';
                    $docInforArr["primarykey"] = 'jvMasterAutoId';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 9: // Materiel Request
                    $docInforArr["tableName"] = 'erp_request';
                    $docInforArr["modelName"] = 'MaterielRequest';
                    $docInforArr["primarykey"] = 'RequestID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "ConfirmedBySystemID";
                    break;
                case 63: //  Asset Capitalization
                    $docInforArr["tableName"] = 'erp_fa_assetcapitalization';
                    $docInforArr["modelName"] = 'AssetCapitalization';
                    $docInforArr["primarykey"] = 'capitalizationID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 41: //  Asset Disposal
                    $docInforArr["tableName"] = 'erp_fa_asset_disposalmaster';
                    $docInforArr["modelName"] = 'AssetDisposalMaster';
                    $docInforArr["primarykey"] = 'assetdisposalMasterAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confimedByEmpSystemID";
                    break;
                case 64: // Payment bank transfer
                    $docInforArr["tableName"] = 'erp_paymentbanktransfer';
                    $docInforArr["modelName"] = 'PaymentBankTransfer';
                    $docInforArr["primarykey"] = 'paymentBankTransferID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 62: // Bank Reconciliation
                    $docInforArr["tableName"] = 'erp_bankrecmaster';
                    $docInforArr["modelName"] = 'BankReconciliation';
                    $docInforArr["primarykey"] = 'bankRecAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 57: // Item Master
                    $docInforArr["tableName"] = 'itemmaster';
                    $docInforArr["modelName"] = 'ItemMaster';
                    $docInforArr["primarykey"] = 'itemCodeSystem';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "itemConfirmedByEMPSystemID";
                    break;
                case 3: // Good Receipt Voucher
                    $docInforArr["tableName"] = 'erp_grvmaster';
                    $docInforArr["modelName"] = 'GRVMaster';
                    $docInforArr["primarykey"] = 'grvAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "grvConfirmedByEmpSystemID";
                    break;
                case 56: // Supplier master
                    $docInforArr["tableName"] = 'suppliermaster';
                    $docInforArr["modelName"] = 'SupplierMaster';
                    $docInforArr["primarykey"] = 'supplierCodeSystem';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 86: // Supplier master
                    $docInforArr["tableName"] = 'registeredsupplier';
                    $docInforArr["modelName"] = 'RegisteredSupplier';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "supplierConfirmedEmpSystemID";
                    break;
                case 58: // Customer master
                    $docInforArr["tableName"] = 'customermaster';
                    $docInforArr["modelName"] = 'CustomerMaster';
                    $docInforArr["primarykey"] = 'customerCodeSystem';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedEmpSystemID";
                    break;
                case 59: // Chart of account
                    $docInforArr["tableName"] = 'chartofaccounts';
                    $docInforArr["modelName"] = 'ChartOfAccount';
                    $docInforArr["primarykey"] = 'chartOfAccountSystemID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedEmpSystemID";
                    break;
                case 66: // Bank Account
                    $docInforArr["tableName"] = 'erp_bankaccount';
                    $docInforArr["modelName"] = 'BankAccount';
                    $docInforArr["primarykey"] = 'bankAccountAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 7: // Stock Adjustment
                    $docInforArr["tableName"] = 'erp_stockadjustment';
                    $docInforArr["modelName"] = 'StockAdjustment';
                    $docInforArr["primarykey"] = 'stockAdjustmentAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 67:
                case 68: // Sales Quotation
                    $docInforArr["tableName"] = 'erp_quotationmaster';
                    $docInforArr["modelName"] = 'QuotationMaster';
                    $docInforArr["primarykey"] = 'quotationMasterID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 71: // Delivery Order
                    $docInforArr["tableName"] = 'erp_delivery_order';
                    $docInforArr["modelName"] = 'DeliveryOrder';
                    $docInforArr["primarykey"] = 'deliveryOrderID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 87: // Delivery Order
                    $docInforArr["tableName"] = 'salesreturn';
                    $docInforArr["modelName"] = 'SalesReturn';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 24:
                    $docInforArr["tableName"] = 'erp_purchasereturnmaster';
                    $docInforArr["modelName"] = 'PurchaseReturn';
                    $docInforArr["primarykey"] = 'purhaseReturnAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 96:
                    $docInforArr["tableName"] = 'currency_conversion_master';
                    $docInforArr["modelName"] = 'CurrencyConversionMaster';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "ConfirmedBySystemID";
                    break;
                case 97: // Stock Count
                    $docInforArr["tableName"] = 'erp_stockcount';
                    $docInforArr["modelName"] = 'StockCount';
                    $docInforArr["primarykey"] = 'stockCountAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 100:
                    $docInforArr["tableName"] = 'erp_budget_contingency';
                    $docInforArr["modelName"] = 'ContingencyBudgetPlan';
                    $docInforArr["primarykey"] = 'ID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 99: // Asset verification
                    $docInforArr["tableName"] = 'erp_fa_asset_verification';
                    $docInforArr["modelName"] = 'AssetVerification';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 103: // Asset Transfer
                    $docInforArr["tableName"] = 'erp_fa_fa_asset_transfer';
                    $docInforArr["modelName"] = 'ERPAssetTransfer';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmed_by_emp_id";
                    break;
                case 102: // Budget Addition
                    $docInforArr["tableName"] = 'erp_budgetaddition';
                    $docInforArr["modelName"] = 'ErpBudgetAddition';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 104:
                    $docInforArr["tableName"] = 'vat_return_filling_master';
                    $docInforArr["modelName"] = 'VatReturnFillingMaster';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 106:
                    $docInforArr["tableName"] = 'appointment';
                    $docInforArr["modelName"] = 'Appointment';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmed_by_emp_id";
                    break;
                case 107:
                    $docInforArr["tableName"] = 'srm_supplier_registration_link';
                    $docInforArr["modelName"] = 'SupplierRegistrationLink';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmed_by_emp_id";
                    break;
                case 108:
                    $docInforArr["tableName"] = 'srm_tender_master';
                    $docInforArr["modelName"] = 'TenderMaster';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmed_by_emp_system_id";
                case 113:
                    $docInforArr["tableName"] = 'srm_tender_master';
                    $docInforArr["modelName"] = 'TenderMaster';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmed_by_emp_system_id";
                    break;
                case 69: // Console Journal Voucher
                    $docInforArr["tableName"] = 'erp_consolejvmaster';
                    $docInforArr["modelName"] = 'ConsoleJVMaster';
                    $docInforArr["primarykey"] = 'consoleJvMasterAutoId';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 117: // Edit Request
                    $docInforArr["tableName"] = 'document_modify_request';
                    $docInforArr["modelName"] = 'DocumentModifyRequest';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "requested_employeeSystemID";
                    break;
                case 118: // Edit Request
                    $docInforArr["tableName"] = 'document_modify_request';
                    $docInforArr["modelName"] = 'DocumentModifyRequest';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "requested_employeeSystemID";
                    break;
                case 119: //  Recurring Voucher
                    $docInforArr["tableName"] = 'recurring_voucher_setup';
                    $docInforArr["modelName"] = 'RecurringVoucherSetup';
                    $docInforArr["primarykey"] = 'recurringVoucherAutoId';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmedByEmpSystemID";
                    break;
                case 127:
                    $docInforArr["tableName"] = 'srm_tender_payment_proof';
                    $docInforArr["modelName"] = 'SRMTenderPaymentProof';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "approved_emp_system_id";
                    break;
                case 132:
                    $docInforArr["tableName"] = 'serviceline';
                    $docInforArr["modelName"] = 'SegmentMaster';
                    $docInforArr["primarykey"] = 'serviceLineSystemID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = 'confirmed_by_emp_system_id';
                    break;
                case 133:
                    $docInforArr["tableName"] = 'company_budget_plannings';
                    $docInforArr["modelName"] = 'CompanyBudgetPlanning';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    $docInforArr["confirmedEmpSystemID"] = "confirmed_by_emp_system_id";
                    break;
                default:
                    return ['success' => false, 'message' => trans('custom.document_id_not_set')];
            }
            //check document exist
            $docApprove = DocumentApproved::find($input["documentApprovedID"]);

            if ($docApprove) {

                if ($docApprove->approvedYN == -1) {
                    return ['success' => false, 'message' => trans('custom.level_already_approved')];
                }

                $reference_document_id = $input['documentSystemID'];
                if(isset($input['reference_document_id']) && $input['reference_document_id'])
                {
                    $reference_document_id = $input['reference_document_id'];
                }

                $empInfo = Helper::getEmployeeInfo();
                $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name

                if ($input["documentSystemID"] == 132) {
                    $docModal = $namespacedModel::withoutGlobalScope('final_level')->find($input["documentSystemCode"]);
                } else {
                    $docModal = $namespacedModel::find($input["documentSystemCode"]);
                }

                $policyConfirmedUserToApprove = '';

                $policyConfirmedUserToApprove = CompanyPolicyMaster::where('companyPolicyCategoryID', 31)
                    ->when(in_array($input["documentSystemID"], [56, 57, 58, 59]), function($query) use ($docModal){
                        $query->where('companySystemID', $docModal['primaryCompanySystemID']);
                    })
                    ->when(!in_array($input["documentSystemID"], [56, 57, 58, 59]), function($query) use ($docModal){
                        $query->where('companySystemID', $docModal['companySystemID']);
                    })
                    ->first();




                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $docApprove->companySystemID)
                    ->where('documentSystemID', $reference_document_id)
                    ->first();

                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => trans('custom.policy_not_found_general')];
                }


                $checkUserHasApprovalAccess = EmployeesDepartment::where('employeeGroupID', $docApprove->approvalGroupID)
                    ->where('companySystemID', $docApprove->companySystemID)
                    ->where('employeeSystemID', $empInfo->employeeSystemID)
                    ->where('documentSystemID', $reference_document_id)
                    ->where('isActive', 1)
                    ->where('removedYN', 0);

                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $checkUserHasApprovalAccess = $checkUserHasApprovalAccess->where('ServiceLineSystemID', $docApprove->serviceLineSystemID);
                }


                $checkUserHasApprovalAccess = $checkUserHasApprovalAccess->whereHas('employee', function ($q) {
                    $q->where('discharegedYN', 0);
                })
                    ->groupBy('employeeSystemID')
                    ->exists();

                if (!$checkUserHasApprovalAccess) {
                    if (($input["documentSystemID"] == 9 && ($docModal && $docModal->isFromPortal == 0)) || $input["documentSystemID"] != 9) {
                        return ['success' => false, 'message' => trans('custom.no_access_reject_document')];
                    }
                }


                if ($policyConfirmedUserToApprove && $policyConfirmedUserToApprove['isYesNO'] == 0) {
                    if ($docModal[$docInforArr["confirmedEmpSystemID"]] == $empInfo->employeeSystemID) {
                        return ['success' => false, 'message' => trans('custom.not_authorized_confirmed_person_reject')];
                    }
                }

                //check document is already rejected
                $isRejected = DocumentApproved::where('documentApprovedID', $input["documentApprovedID"])->where('rejectedYN', -1)->first();
                if (!$isRejected) {
                    $approvalLevel = ApprovalLevel::find($input["approvalLevelID"]);

                    if ($approvalLevel) {
                        // get current employee detail

                        // update record in document approved table
                        $approvedeDoc = $docApprove->update(['rejectedYN' => -1, 'rejectedDate' => now(), 'rejectedComments' => $input["rejectedComments"], 'employeeID' => $empInfo->empID, 'employeeSystemID' => $empInfo->employeeSystemID]);

                        if(isset($input['rejectedStatus']) && $input['rejectedStatus'] == 1) {
                            $docApprove->update([
                                'status' => 1
                            ]);
                        }

                        if (in_array($input["documentSystemID"], [2, 5, 52, 1, 50, 51, 20, 11, 46, 22, 23, 21, 4, 19, 13, 10, 15, 8, 12, 17, 9, 63, 41, 64, 62, 3, 57, 56, 58, 59, 66, 7, 67, 68, 71, 86, 87, 24, 96, 97, 99, 100, 103, 102, 65, 104, 106,107,108, 113, 69,117, 119, 127, 132])) {
                            if ($input["documentSystemID"] == 132) {
                                $timesReferredUpdate = $namespacedModel::withoutGlobalScope('final_level')->find($docApprove["documentSystemCode"])->increment($docInforArr["referredColumnName"]);
                                $refferedBackYNUpdate = $namespacedModel::withoutGlobalScope('final_level')->find($docApprove["documentSystemCode"])->update(['refferedBackYN' => -1]);
                            } else {
                                $timesReferredUpdate = $namespacedModel::find($docApprove["documentSystemCode"])->increment($docInforArr["referredColumnName"]);
                                $refferedBackYNUpdate = $namespacedModel::find($docApprove["documentSystemCode"])->update(['refferedBackYN' => -1]);
                            }
                        }
                        else if($input["documentSystemID"] == 118)
                        {
                            $refferedBackYNUpdate = $namespacedModel::find($docApprove["documentSystemCode"])->update(['confirmation_rejected' => -1,'confirmation_rejected_date' => now(),'confirmation_rejected_by_user_system_id' => $empInfo->employeeSystemID]);
                        }

                        /*send Email*/
                        $confirmedUser = 0;
                        $emails = array();

                        if ($input["documentSystemID"] == 132) {
                            $sourceModel = $namespacedModel::withoutGlobalScope('final_level')->find($input["documentSystemCode"]);
                        } else {
                            $sourceModel = $namespacedModel::find($input["documentSystemCode"]);
                        }

                        if (!empty($sourceModel)) {

                            $currentApproved = DocumentApproved::find($input["documentApprovedID"]);
                            $document = DocumentMaster::where('documentSystemID', $currentApproved->documentSystemID)->first();
                            $confirmedUser = $currentApproved->docConfirmedByEmpSystemID;
                            // $companyDocument = CompanyDocumentAttachment::where('companySystemID', $currentApproved->companySystemID)
                            //     ->where('documentSystemID', $currentApproved->documentSystemID)
                            //     ->first();

                            // if (empty($companyDocument)) {
                            //     return ['success' => false, 'message' => 'Policy not found for this document'];
                            // }

                            if($input["documentSystemID"] == 117 )
                            {
                                $document->documentDescription = $sourceModel->type == 1?'Edit Request':'Amend Request';
                            }

                            if($input["documentSystemID"] == 118)
                            {
                                $document->documentDescription = $sourceModel->type == 1?'Edit Approve Request':'Amend Approve Request';
                            }



                            if($input["documentSystemID"] == 56 )
                            {
                                $subjectName = $document->documentDescription . ' ' . $sourceModel->supplierName;
                                $bodyName = '<p>'.$document->documentDescription . ' ' . '<b>' . $sourceModel->supplierName . '</b>';
                            }
                            else if($input["documentSystemID"] == 58 )
                            {
                                $subjectName = $document->documentDescription . ' ' . $sourceModel->CustomerName;
                                $bodyName = '<p>'.$document->documentDescription . ' ' . '<b>' . $sourceModel->CustomerName . '</b>';
                            }
                            else if(!empty($input["document_system_id"]) && $input["document_system_id"] == 108)
                            {
                                $subjectName = 'Tender ' . $currentApproved->documentCode;
                            }
                            else
                            {
                                $subjectName = $document->documentDescription . ' ' . $currentApproved->documentCode;
                                $bodyName = '<p>'.$document->documentDescription . ' ' . '<b>' . $currentApproved->documentCode . '</b>';
                            }

                            $subject = trans('email.is_rejected_subject', ['attribute' => $subjectName]);
                            if(isset($input['rejectedStatus']) && $input['rejectedStatus'] == 1) {
                                if(($input["document_system_id"] == 108 || $input["document_system_id"] == 113)){
                                    $documentType = ($input["document_system_id"]) == 108 ? 'Tender' : 'RFX';
                                    $body ="<p>We regret to inform you that the $documentType document has been rejected by the approver.</p>" .
                                        "<p>$documentType Code: $sourceModel->tender_code</p>" .
                                        "<p>$documentType Title: $sourceModel->title</p>" .
                                        "<p>Reject Comment: " . $input["rejectedComments"] . "</p>" .
                                        "<p>Thank You.</p>";
                                }else {
                                    $body = trans('email.is_rejected', ['attribute' => $bodyName, 'empName' => $empInfo->empName, 'rejectedComments' => $input["rejectedComments"]]);
                                }
                            }else {
                                $body = trans('email.is_rejected', ['attribute' => $bodyName, 'empName' => $empInfo->empName, 'rejectedComments' => $input["rejectedComments"]]);
                            }

                            // get previously approved person for send Emil
                            if ($input["rollLevelOrder"] > 1) {

                                $previousApprovals = ApproveRejectTransaction::previousDocumentApprovers($currentApproved->documentSystemID, $currentApproved->documentSystemCode, $currentApproved->rollLevelOrder, $currentApproved->companySystemID);

                                if (count((array)$previousApprovals) > 0) {
                                    foreach ($previousApprovals as $row) {
                                        if ($row->employeeSystemID > 0) {
                                            $emails[] = array(
                                                'empSystemID' => $row->employeeSystemID,
                                                'companySystemID' => $row->companySystemID,
                                                'docSystemID' => $row->documentSystemID,
                                                'alertMessage' => $subject,
                                                'emailAlertMessage' => $body,
                                                'docSystemCode' => $row->documentSystemCode
                                            );
                                        }
                                    }
                                }
                            }

                            // get confirmed user for send Emil
                            if ($confirmedUser > 0) {
                                $emails[] = array(
                                    'empSystemID' => $confirmedUser,
                                    'companySystemID' => $currentApproved->companySystemID,
                                    'docSystemID' => $currentApproved->documentSystemID,
                                    'alertMessage' => $subject,
                                    'emailAlertMessage' => $body,
                                    'docSystemCode' => $input["documentSystemCode"]
                                );
                            }

                            if($input["documentSystemID"] == 133)
                            {
                                $refferedBackYNUpdate = $namespacedModel::find($docApprove["documentSystemCode"])->update(['rejected_yn' => -1,'rejected_date' => now()]);

                            }
                            if($input["documentSystemID"] == 117)
                            {
                                $refferedBackYNUpdate = $namespacedModel::find($docApprove["documentSystemCode"])->update(['status' => 0,'rejected_date' => now(),'rejected_by_user_system_id' => $empInfo->employeeSystemID]);

                            }
                            if($input["documentSystemID"] == 118)
                            {
                                $refferedBackYNUpdate = $namespacedModel::find($docApprove["documentSystemCode"])->update(['status' => 0,'confirmation_rejected_date' => now(),'confirmation_rejected_by_user_system_id' => $empInfo->employeeSystemID]);

                            }
                            if($input["documentSystemID"] == 2)
                            {
                                $poAssignedTemplateId = PaymentTermTemplateAssigned::where('supplierID', $sourceModel->supplierID)->value('templateID');
                                $isActiveTemplate = PaymentTermTemplate::where('id', $poAssignedTemplateId)->value('isActive');

                                if ($poAssignedTemplateId != null && $isActiveTemplate) {
                                    DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $input['purchaseOrderID'])->where('templateID', $poAssignedTemplateId)->update(['isRejected' => true]);
                                } else {
                                    $poDefaultConfigUpdate = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $input['purchaseOrderID'])->where('isDefaultAssign', true)->where('isConfigUpdate', true)->first();
                                    if ($poDefaultConfigUpdate) {
                                        DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $input['purchaseOrderID'])->where('templateID', $poDefaultConfigUpdate->templateID)
                                            ->where('isDefaultAssign', true)->update(['isRejected' => true]);
                                    } else {
                                        $defaultTemplateID = PaymentTermTemplate::where('isDefault', true)->value('id');
                                        DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $input['purchaseOrderID'])->where('templateID', $defaultTemplateID)->update(['isRejected' => true]);
                                    }
                                }
                            }

                            if($input["documentSystemID"] == 107 || $input["documentSystemID"] == 106 || $input["documentSystemID"] == 127)
                            {
                                if (isset($currentApproved->reference_email) && !empty($currentApproved->reference_email)) {

                                    if($input["documentSystemID"] == 107 )
                                    {
                                        $link = env('SRM_LINK');
                                        $loginLink = str_replace("/register/", "/", $link);

                                        $sub = trans('email.kyc_referred_back_body', [
                                            'empName' => $empInfo->empName,
                                            'rejectedComments' => $input["rejectedComments"],
                                            'loginLink' => $loginLink
                                        ]);

                                        $msg = trans('email.registration_referred_back');
                                    }
                                    else if($input["documentSystemID"] == 106)
                                    {
                                        $sub = trans('email.delivery_appointment_rejected_body', [
                                            'empName' => $empInfo->empName,
                                            'rejectedComments' => $input["rejectedComments"]
                                        ]);
                                        $msg = trans('email.delivery_appointment_rejected');
                                    }

                                    else if ($input["documentSystemID"] == 127)
                                    {
                                        $supplierName = $input["supplierName"];
                                        $tenderCode = $input["tenderCode"];
                                        $tenderTitle = $input["tenderTitle"];
                                        $comment = $input["rejectedComments"];

                                        $sub = "<p>Dear $supplierName,</p>
                                        <p>Please find the comments provided for the document attached to the tender 
                                        <b>$tenderCode</b>, <b>$tenderTitle</b>.</p>
                                        <p><b>Comment:</b></p>
                                        <p>$comment</p>
                                        <p>Kindly review the comments and attach the updated or corresponding document.</p>
                                        <p>Regards,</p>";

                                        $msg = " Tender Payment Attachment Proof Rejected";
                                    }

                                    $dataEmail['empEmail'] = $currentApproved->reference_email;
                                    $dataEmail['companySystemID'] = $currentApproved->companySystemID;
                                    $temp = $sub;
                                    $dataEmail['alertMessage'] = $msg;
                                    $dataEmail['emailAlertMessage'] = $temp;


                                    $sendEmail = Email::sendEmailErp($dataEmail);
                                }
                                else
                                {
                                    return ['success' => false, 'message' => trans('email.unable_to_send')];
                                }
                            }else if((!isset($input['rejectedStatus']) || $input['rejectedStatus'] == 0)  && ($input["document_system_id"] == 108 || $input["document_system_id"] == 113)){
                                $confirmedUserEmail = Employee::select('empName','empEmail')
                                    ->where('employeeSystemID',$sourceModel->confirmed_by_emp_system_id)
                                    ->first();
                                if (isset($confirmedUserEmail->empEmail) && !empty($confirmedUserEmail->empEmail)) {
                                    $sub = $sourceModel->tender_code." Referred Back";
                                    $body = "<p>Dear " .$confirmedUserEmail->empName. ',</p>' .
                                        "<p>The document " . $sourceModel->tender_code . ' ' . $sourceModel->title . ' has been referred back for your review with the below comment:' .
                                        "<br><br>" . $input["rejectedComments"] . "." . " <br>" .
                                        "<br>Kindly review the document. <br>" .
                                        "Thank You.</p>";

                                    $dataEmail['empEmail'] = $confirmedUserEmail->empEmail;
                                    $dataEmail['companySystemID'] = $sourceModel->company_id;
                                    $temp = $body;
                                    $dataEmail['alertMessage'] = $sub;
                                    $dataEmail['emailAlertMessage'] = $temp;
                                    $sendEmail = Email::sendEmailErp($dataEmail);
                                }else {
                                    return ['success' => false, 'message' => trans('email.unable_to_send')];
                                }

                            }else
                            {
                                $sendEmail = email::sendEmail($emails);
                            }


                            if (!$sendEmail["success"]) {
                                return ['success' => false, 'message' => $sendEmail["message"]];
                            }
                        }
                    } else {
                        return ['success' => false, 'message' => trans('custom.approval_level_not_found')];
                    }
                    DB::commit();

                    $rejectedMsg = ($input["documentSystemID"] == 108 || $input["documentSystemID"] == 113) ? trans('custom.referred_back') : trans('custom.rejected');
                    return ['success' => true, 'message' => trans('custom.document_successfully') . ' ' . $rejectedMsg];

                } else {
                    return ['success' => false, 'message' => trans('custom.level_already_rejected')];
                }
            } else {
                return ['success' => false, 'message' => trans('custom.no_record_found')];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e . trans('custom.error_occurred')];
        }
    }
}