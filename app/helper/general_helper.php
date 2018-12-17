<?php
/**
 * =============================================
 * -- File Name : general_helper.php
 * -- Project Name : ERP
 * -- Module Name :  Helper class
 * -- Author : Mohamed Mubashir
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all the common function
 * -- REVISION HISTORY
 * Date: 08 - May 2018 By: Mubashir Description: Added an already document has record in document approved table check to confirmDocument function
 * Date: 12 - June 2018 By: Nazir Description: Adden a new function companyFinanceYear() for company finance year drop down
 * Date: 12 - June 2018 By: Nazir Description: Adden a new function companyFinancePeriod() for company finance period drop down
 */

namespace App\helper;

use App\Jobs\BankLedgerInsert;
use App\Jobs\BudgetAdjustment;
use App\Jobs\CreateCustomerInvoice;
use App\Jobs\CreateGRVSupplierInvoice;
use App\Jobs\CreateReceiptVoucher;
use App\Jobs\CreateStockReceive;
use App\Jobs\CreateSupplierInvoice;
use App\Jobs\GeneralLedgerInsert;
use App\Jobs\ItemLedgerInsert;
use App\Jobs\UnbilledGRVInsert;
use App\Models;
use App\Models\CustomerReceivePayment;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Response;
use InfyOm\Generator\Utils\ResponseUtil;

class Helper
{
    /**
     * Get all the documents
     * @return mixed
     */
    public static function getAllDocuments()
    {
        $document = Models\DocumentMaster::all();
        return $document;
    }

    /**
     * Get all company service line
     * @param $company - current company id
     * @return $serviceline all service lines
     */
    public static function getCompanyServiceline($company)
    {
        $companiesByGroup = "";
        if (self::checkIsCompanyGroup($company)) {
            $companiesByGroup = self::getGroupCompany($company);
        } else {
            $companiesByGroup = (array)$company;
        }
        $serviceline = Models\SegmentMaster::whereIN('companySystemID', $companiesByGroup)->get();
        return $serviceline;
    }


    /**
     * Get all companies related to a group
     * @param $selectedCompanyId - current company id
     * @return array
     */
    public static function getGroupCompany($selectedCompanyId)
    {
        $companiesByGroup = Models\Company::with('child')->where("masterCompanySystemIDReorting", $selectedCompanyId)->get();
        $groupCompany = [];
        if ($companiesByGroup) {
            foreach ($companiesByGroup as $val) {
                if ($val['child']) {
                    foreach ($val['child'] as $val1) {
                        $groupCompany[] = array('companySystemID' => $val1["companySystemID"], 'CompanyID' => $val1["CompanyID"], 'CompanyName' => $val1["CompanyName"]);
                    }
                } else {
                    $groupCompany[] = array('companySystemID' => $val["companySystemID"], 'CompanyID' => $val["CompanyID"], 'CompanyName' => $val["CompanyName"]);
                }
            }
        }
        $groupCompany = array_column($groupCompany, 'companySystemID');
        return $groupCompany;
    }

    /**
     * Get all sub companies related to a group
     * @param $selectedCompanyId - current company id
     * @return array
     */
    public static function getSubCompaniesByGroupCompany($selectedCompanyId)
    {
        /*$companiesByGroup = Models\Company::with('child')->where("masterCompanySystemIDReorting", $selectedCompanyId)->get();
        $groupCompany = [];
        if ($companiesByGroup) {
            foreach ($companiesByGroup as $val) {
                if ($val['isGroup'] == -1) {
                    foreach ($val['child'] as $val1) {
                        if ($val['isGroup'] == 0) {
                            $groupCompany[] = array('companySystemID' => $val1["companySystemID"], 'CompanyID' => $val1["CompanyID"], 'CompanyName' => $val1["CompanyName"]);
                        }
                    }
                } else {
                    $groupCompany[] = array('companySystemID' => $val["companySystemID"], 'CompanyID' => $val["CompanyID"], 'CompanyName' => $val["CompanyName"]);
                }
            }
        }
        $groupCompany = array_column($groupCompany, 'companySystemID');
        return $groupCompany;*/
        $companiesByGroup = Models\Company::with('child')->where("masterCompanySystemIDReorting", $selectedCompanyId)->get();
        $groupCompany = [];
        if ($companiesByGroup) {
            foreach ($companiesByGroup as $val) {
                if ($val['child']) {
                    foreach ($val['child'] as $val1) {
                        $groupCompany[] = array('companySystemID' => $val1["companySystemID"], 'CompanyID' => $val1["CompanyID"], 'CompanyName' => $val1["CompanyName"]);
                    }
                } else {
                    $groupCompany[] = array('companySystemID' => $val["companySystemID"], 'CompanyID' => $val["CompanyID"], 'CompanyName' => $val["CompanyName"]);
                }
            }
        }
        $groupCompany = array_column($groupCompany, 'companySystemID');
        return $groupCompany;
    }

    /**
     * A common function to confirm document with approval creation
     * @param $params : accept parameters as an array
     * $param 1-documentSystemID : autoID
     * $param 2-company : company
     * $param 3-document : document
     * $param 4-segment : segment
     * $param 5-category : category
     * $param 6-amount : amount
     * no return values
     */
    public static function confirmDocument($params)
    {
        /** check document is already confirmed*/
        if (!array_key_exists('autoID', $params)) {
            return ['success' => false, 'message' => 'Parameter documentSystemID is missing'];
        }

        if (!array_key_exists('company', $params)) {
            return ['success' => false, 'message' => 'Parameter company is missing'];
        }

        if (!array_key_exists('document', $params)) {
            return ['success' => false, 'message' => 'Parameter document is missing'];
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
                default:
                    return ['success' => false, 'message' => 'Document ID not found'];
            }


            $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
            $masterRec = $namespacedModel::find($params["autoID"]);
            if ($masterRec) {
                //checking whether document approved table has a data for the same document
                $docExist = Models\DocumentApproved::where('documentSystemID', $params["document"])->where('documentSystemCode', $params["autoID"])->first();
                if (!$docExist) {
                    // check document is available in document master table
                    $document = Models\DocumentMaster::where('documentSystemID', $params["document"])->first();
                    if ($document) {
                        //check document is already confirmed
                        $isConfirm = $namespacedModel::where($docInforArr["primarykey"], $params["autoID"])->where($docInforArr["confirmColumnName"], 1)->first();

                        if (!$isConfirm) {
                            // get current employee detail
                            $empInfo = self::getEmployeeInfo();
                            //confirm the document
                            $masterRec->update([$docInforArr["confirmColumnName"] => 1, $docInforArr["confirmedBy"] => $empInfo->empName, $docInforArr["confirmedByEmpID"] => $empInfo->empID, $docInforArr["confirmedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["confirmedDate"] => now(), 'RollLevForApp_curr' => 1]);

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
                                        return ['success' => false, 'message' => 'There is no attachments attached. Please attach an attachment before you confirm the document'];

                                    }
                                }
                            } else {
                                return ['success' => false, 'message' => 'Policy not available for this document.'];
                            }

                            // get approval rolls
                            $approvalLevel = Models\ApprovalLevel::with('approvalrole')->where('companySystemID', $params["company"])->where('documentSystemID', $params["document"])->where('departmentSystemID', $document["departmentSystemID"])->where('isActive', -1);
                            if ($isSegmentWise) {
                                if (array_key_exists('segment', $params)) {
                                    if ($params["segment"]) {
                                        $approvalLevel->where('serviceLineSystemID', $params["segment"]);
                                        $approvalLevel->where('serviceLineWise', 1);
                                    } else {
                                        return ['success' => false, 'message' => 'No approval setup created for this document'];
                                    }
                                } else {
                                    return ['success' => false, 'message' => 'Serviceline parameters are missing'];
                                }
                            }

                            if ($isCategoryWise) {
                                if (array_key_exists('category', $params)) {
                                    if ($params["category"]) {
                                        $approvalLevel->where('categoryID', $params["category"]);
                                        $approvalLevel->where('isCategoryWiseApproval', -1);
                                    } else {
                                        return ['success' => false, 'message' => 'No approval setup created for this document'];
                                    }
                                } else {
                                    return ['success' => false, 'message' => 'Category parameter are missing'];
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
                                        return ['success' => false, 'message' => 'No approval setup created for this document'];
                                    }
                                } else {
                                    return ['success' => false, 'message' => 'Amount parameter are missing'];
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
                                                return ['success' => false, 'message' => 'No approval setup created for this document'];
                                            }
                                        } else {
                                            return ['success' => false, 'message' => 'Serviceline parameters are missing'];
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
                                                return ['success' => false, 'message' => 'No approval setup created for this document'];
                                            }
                                        } else {
                                            return ['success' => false, 'message' => 'Amount parameter are missing'];
                                        }
                                    }

                                    $approvalLevel->where('isCategoryWiseApproval', 0);
                                    $output = $approvalLevel->first();
                                }
                            }

                            if ($output) {
                                /** get source document master record*/
                                $sorceDocument = $namespacedModel::find($params["autoID"]);
                                $documentApproved = [];
                                if ($output) {
                                    if ($output->approvalrole) {
                                        foreach ($output->approvalrole as $val) {
                                            if ($val->approvalGroupID) {
                                                $documentApproved[] = array('companySystemID' => $val->companySystemID, 'companyID' => $val->companyID, 'departmentSystemID' => $val->departmentSystemID, 'departmentID' => $val->departmentID, 'serviceLineSystemID' => $val->serviceLineSystemID, 'serviceLineCode' => $val->serviceLineID, 'documentSystemID' => $val->documentSystemID, 'documentID' => $val->documentID, 'documentSystemCode' => $params["autoID"], 'documentCode' => $sorceDocument[$docInforArr["documentCodeColumnName"]], 'approvalLevelID' => $val->approvalLevelID, 'rollID' => $val->rollMasterID, 'approvalGroupID' => $val->approvalGroupID, 'rollLevelOrder' => $val->rollLevel, 'docConfirmedDate' => now(), 'docConfirmedByEmpSystemID' => $empInfo->employeeSystemID, 'docConfirmedByEmpID' => $empInfo->empID, 'timeStamp' => NOW());
                                            } else {
                                                return ['success' => false, 'message' => 'Please set the approval group'];
                                            }
                                        }
                                    } else {
                                        return ['success' => false, 'message' => 'No approval setup created for this document'];
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
                                            return ['success' => false, 'message' => 'Policy not found for this document'];
                                        }

                                        $approvalList = Models\EmployeesDepartment::where('employeeGroupID', $documentApproved->approvalGroupID)
                                            ->where('companySystemID', $documentApproved->companySystemID)
                                            ->where('documentSystemID', $documentApproved->documentSystemID);

                                        if ($companyDocument['isServiceLineApproval'] == -1) {
                                            $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproved->serviceLineSystemID);
                                        }

                                        $approvalList = $approvalList
                                            ->with(['employee'])
                                            ->groupBy('employeeSystemID')
                                            ->get();

                                        $emails = array();
                                        $document = Models\DocumentMaster::where('documentSystemID', $documentApproved->documentSystemID)->first();

                                        $approvedDocNameBody = $document->documentDescription . ' <b>' . $documentApproved->documentCode . '</b>';

                                        $body = '<p>' . $approvedDocNameBody . '  is pending for your approval.</p>';
                                        $subject = "Pending " . $document->documentDescription . " approval " . $documentApproved->documentCode;

                                        foreach ($approvalList as $da) {
                                            if ($da->employee) {
                                                $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                                                    'companySystemID' => $documentApproved->companySystemID,
                                                    'docSystemID' => $documentApproved->documentSystemID,
                                                    'alertMessage' => $subject,
                                                    'emailAlertMessage' => $body,
                                                    'docSystemCode' => $documentApproved->documentSystemCode);
                                            }
                                        }

                                        $sendEmail = \Email::sendEmail($emails);
                                        if (!$sendEmail["success"]) {
                                            return ['success' => false, 'message' => $sendEmail["message"]];
                                        }

                                    }

                                }
                                DB::commit();
                                return ['success' => true, 'message' => 'Successfully document confirmed'];
                            } else {
                                DB::rollback();
                                return ['success' => false, 'message' => 'No approval setup created for this document'];
                            }
                        } else {
                            DB::rollback();
                            return ['success' => false, 'message' => 'Document is already confirmed'];
                        }
                    } else {
                        DB::rollback();
                        return ['success' => false, 'message' => 'Document not found'];
                    }
                } else {
                    DB::rollback();
                    return ['success' => false, 'message' => 'Document approval data is already generated.'];
                }
            } else {
                DB::rollback();
                return ['success' => false, 'message' => 'No records found'];
            }
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            //dd($e);
            return ['success' => false, 'message' => $e . 'Error Occurred'];
        }
    }

    /**
     * Function to get currency conversion rate by company,supplier and bankaccount
     * @param $companySystemID - company auto id
     * @param $transactionCurrencyID - document/supplier/customer transaction currency
     * @param $documentCurrencyID - this is an optional currency from each line item EX: PR it takes the local currency
     * @param $transactionAmount - document/supplier/customer transaction amount
     * @param null $bankAccountAutoID - bank account ID
     * @return trasToLocER,trasToRptER,transToBankER,reportingAmount,localAmount,documentAmount,bankAmount
     */
    public static function currencyConversion($companySystemID, $transactionCurrencyID, $documentCurrencyID, $transactionAmount, $bankAccountAutoID = null)
    {
        $locaCurrencyID = null;
        $reportingCurrencyID = null;
        $bankAccountCurrencyID = null;

        $reportingAmount = 0;
        $localAmount = 0;
        $documentAmount = 0;
        $bankAmount = 0;

        $trasToSuppER = 1;
        $trasToLocER = 0;
        $trasToRptER = 0;
        $transToBankER = 0;
        $transToDocER = 0;

        // get company local and reporting currency conversion
        if ($companySystemID) {
            $companyCurrency = Models\Company::find($companySystemID);
            if ($companyCurrency) {
                $locaCurrencyID = $companyCurrency->localCurrencyID;
                $reportingCurrencyID = $companyCurrency->reportingCurrency;
                $conversion = Models\CurrencyConversion::where('masterCurrencyID', $transactionCurrencyID)->where('subCurrencyID', $locaCurrencyID)->first();
                $trasToLocER = $conversion->conversion;
                $conversion = Models\CurrencyConversion::where('masterCurrencyID', $transactionCurrencyID)->where('subCurrencyID', $reportingCurrencyID)->first();
                $trasToRptER = $conversion->conversion;

                if ($transactionCurrencyID == $reportingCurrencyID) {
                    $reportingAmount = $transactionAmount;
                } else {
                    if ($trasToRptER > $trasToSuppER) {
                        if ($trasToRptER > 1) {
                            $reportingAmount = $transactionAmount / $trasToRptER;
                        } else {
                            $reportingAmount = $transactionAmount * $trasToRptER;
                        }
                    } else {
                        If ($trasToRptER > 1) {
                            $reportingAmount = $transactionAmount * $trasToRptER;
                        } else {
                            $reportingAmount = $transactionAmount / $trasToRptER;
                        }
                    }
                }

                if ($transactionCurrencyID == $locaCurrencyID) {
                    $localAmount = $transactionAmount;
                } else {
                    if ($trasToLocER > $trasToSuppER) {
                        if ($trasToLocER > 1) {
                            $localAmount = $transactionAmount / $trasToLocER;
                        } else {
                            $localAmount = $transactionAmount * $trasToLocER;
                        }
                    } else {
                        If ($trasToLocER > 1) {
                            $localAmount = $transactionAmount * $trasToLocER;
                        } else {
                            $localAmount = $transactionAmount / $trasToLocER;
                        }
                    }
                }
            }
        }

        // get bank currency conversion
        if ($bankAccountAutoID) {
            $bankCurrency = Models\BankAccount::find($bankAccountAutoID);
            if ($bankCurrency) {
                $bankAccountCurrencyID = $bankCurrency->accountCurrencyID;
                $conversion = Models\CurrencyConversion::where('masterCurrencyID', $transactionCurrencyID)->where('subCurrencyID', $bankAccountCurrencyID)->first();
                $transToBankER = $conversion->conversion;

                if ($transactionCurrencyID == $bankAccountCurrencyID) {
                    $bankAmount = $transactionAmount;
                } else {
                    if ($transToBankER > $trasToSuppER) {
                        if ($transToBankER > 1) {
                            $bankAmount = $transactionAmount / $transToBankER;
                        } else {
                            $bankAmount = $transactionAmount * $transToBankER;
                        }
                    } else {
                        If ($transToBankER > 1) {
                            $bankAmount = $transactionAmount * $transToBankER;
                        } else {
                            $bankAmount = $transactionAmount / $transToBankER;
                        }
                    }
                }
            }
        }

        // get document currency. Ex : in purchase request the currency which is selected in the header is the document currency
        if ($documentCurrencyID) {
            $conversion = Models\CurrencyConversion::where('masterCurrencyID', $transactionCurrencyID)->where('subCurrencyID', $documentCurrencyID)->first();
            $transToDocER = $conversion->conversion;

            if ($transactionCurrencyID == $documentCurrencyID) {
                $documentAmount = $transactionAmount;
            } else {
                if ($transToDocER > $trasToSuppER) {
                    if ($transToDocER > 1) {
                        $documentAmount = $transactionAmount / $transToDocER;
                    } else {
                        $documentAmount = $transactionAmount * $transToDocER;
                    }
                } else {
                    If ($transToDocER > 1) {
                        $documentAmount = $transactionAmount * $transToDocER;
                    } else {
                        $documentAmount = $transactionAmount / $transToDocER;
                    }
                }
            }
        }
        $array = array('trasToLocER' => $trasToLocER,
            'trasToRptER' => $trasToRptER,
            'transToBankER' => $transToBankER,
            'transToDocER' => $transToDocER,
            'reportingAmount' => $reportingAmount,
            'localAmount' => $localAmount,
            'documentAmount' => $documentAmount,
            'bankAmount' => $bankAmount);

        return $array;

    }


    /**
     * function to approve documents
     * @param $input - get line records
     * @return mixed
     */
    public static function approveDocument($input)
    {
        $docInforArr = array('tableName' => '', 'modelName' => '', 'primarykey' => '', 'approvedColumnName' => '', 'approvedBy' => '', 'approvedBySystemID' => '', 'approvedDate' => '', 'approveValue' => '', 'confirmedYN' => '', 'confirmedEmpSystemID' => '');
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
            default:
                return ['success' => false, 'message' => 'Document ID not found'];
        }

        //return ['success' => true , 'message' => $docInforArr];
        DB::beginTransaction();
        try {
            $userMessage = 'Successfully approved the document';
            $userMessageE = '';
            $docApproved = Models\DocumentApproved::find($input["documentApprovedID"]);
            if ($docApproved) {

                // get current employee detail
                $empInfo = self::getEmployeeInfo();
                $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
                $isConfirmed = $namespacedModel::find($input["documentSystemCode"]);
                if (!$isConfirmed[$docInforArr["confirmedYN"]]) { // check document is confirmed or not
                    return ['success' => false, 'message' => 'Document is not confirmed'];
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

                if ($policyConfirmedUserToApprove['isYesNO'] == 0) {
                    if ($isConfirmed[$docInforArr["confirmedEmpSystemID"]] == $empInfo->employeeSystemID) {
                        return ['success' => false, 'message' => 'Not authorized!'];
                    }
                }

                if (["documentSystemID"] == 46) {
                    if ($isConfirmed['year'] != date("Y")) {
                        return ['success' => false, 'message' => 'Budget transfer you are trying to approve is not for the current year. You cannot approve a budget transfer which is not for current year.'];
                    }
                }

                //check document is already approved
                $isApproved = Models\DocumentApproved::where('documentApprovedID', $input["documentApprovedID"])->where('approvedYN', -1)->first();
                if (!$isApproved) {
                    $approvalLevel = Models\ApprovalLevel::find($input["approvalLevelID"]);

                    if ($approvalLevel) {
                        //Budget check on the 1st level approval for PR/DR/WR
                        if ($input["rollLevelOrder"] == 1) {
                            if ($input["documentSystemID"] == 1 || $input["documentSystemID"] == 50 || $input["documentSystemID"] == 51) {

                                $purchaseRequestMaster = Models\PurchaseRequest::find($input["documentSystemCode"]);

                                $checkBudget = Models\CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
                                    ->where('companySystemID', $purchaseRequestMaster->companySystemID)
                                    ->first();

                                if ($checkBudget->isYesNO == 1) {
                                    if ($purchaseRequestMaster->checkBudgetYN == -1) {

                                        $purchaseRequestID = $purchaseRequestMaster->purchaseRequestID;
                                        $serviceLineSystemID = $purchaseRequestMaster->serviceLineSystemID;
                                        $companySystemID = $purchaseRequestMaster->companySystemID;
                                        $budgetYear = $purchaseRequestMaster->budgetYear;

                                        if ($purchaseRequestMaster->financeCategory != 3) {

                                            $totalDocumentRptAmount = 0;

                                            $documentAmount = collect(\DB::select('SELECT erp_purchaserequestdetails.purchaseRequestID,templateGLCode.templatesDetailsAutoID, Sum(erp_purchaserequestdetails.quantityRequested*erp_purchaserequestdetails.estimatedCost) AS totalCost, erp_purchaserequestdetails.budgetYear FROM erp_purchaserequestdetails INNER JOIN (SELECT erp_templatesglcode.templatesDetailsAutoID, erp_templatesdetails.templateDetailDescription,erp_templatesglcode.templateMasterID, erp_templatesglcode.chartOfAccountSystemID, erp_templatesglcode.glCode FROM erp_templatesglcode INNER JOIN erp_templatesdetails ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID AND erp_templatesdetails.templatesMasterAutoID = erp_templatesglcode.templateMasterID WHERE erp_templatesglcode.templateMasterID = 15 OR erp_templatesglcode.templateMasterID = 3) AS templateGLCode ON templateGLCode.chartOfAccountSystemID = erp_purchaserequestdetails.financeGLcodePLSystemID AND erp_purchaserequestdetails.budgetYear = ' . $budgetYear . ' WHERE erp_purchaserequestdetails.purchaseRequestID = ' . $purchaseRequestID . ' GROUP BY erp_purchaserequestdetails.purchaseRequestID,templateGLCode.templatesDetailsAutoID'))->first();

                                            if ($documentAmount) {

                                                $budgetAmount = collect(\DB::select('SELECT erp_budjetdetails.companySystemID, erp_budjetdetails.serviceLineSystemID,erp_budjetdetails.templateDetailID,templateGLCode.templateDetailDescription, erp_budjetdetails. YEAR,sum(erp_budjetdetails.budjetAmtLocal) AS budgetLocalAmount,sum(erp_budjetdetails.budjetAmtRpt) AS budgetRptAmount FROM erp_budjetdetails INNER JOIN (SELECT erp_templatesglcode.templatesDetailsAutoID,erp_templatesdetails.templateDetailDescription,erp_templatesglcode.templateMasterID,erp_templatesglcode.chartOfAccountSystemID,erp_templatesglcode.glCode FROM erp_templatesglcode INNER JOIN erp_templatesdetails ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID AND erp_templatesdetails.templatesMasterAutoID = erp_templatesglcode.templateMasterID WHERE erp_templatesglcode.templateMasterID = 15 OR erp_templatesglcode.templateMasterID = 3 ) AS templateGLCode ON templateGLCode.templatesDetailsAutoID = erp_budjetdetails.templateDetailID AND templateGLCode.chartOfAccountSystemID = erp_budjetdetails.chartOfAccountID WHERE erp_budjetdetails.YEAR = ' . $budgetYear . ' AND erp_budjetdetails.companySystemID = ' . $companySystemID . ' AND erp_budjetdetails.serviceLineSystemID = ' . $serviceLineSystemID . ' AND erp_budjetdetails.templateDetailID = ' . $documentAmount->templatesDetailsAutoID . ' GROUP BY erp_budjetdetails.companySystemID, erp_budjetdetails.serviceLineSystemID, erp_budjetdetails. YEAR, erp_budjetdetails.templateDetailID'))->first();

                                                $consumedAmount = collect(\DB::select('SELECT erp_budgetconsumeddata.companySystemID,erp_budgetconsumeddata.serviceLineSystemID,templateGLCode.templatesDetailsAutoID,templateGLCode.templateDetailDescription, erp_budgetconsumeddata.Year, sum( erp_budgetconsumeddata.consumedLocalAmount ) AS ConsumedLocalAmount, sum( erp_budgetconsumeddata.consumedRptAmount ) AS ConsumedRptAmount FROM erp_budgetconsumeddata INNER JOIN ( SELECT erp_templatesglcode.templatesDetailsAutoID, erp_templatesdetails.templateDetailDescription, erp_templatesglcode.templateMasterID,erp_templatesglcode.chartOfAccountSystemID, erp_templatesglcode.glCode FROM erp_templatesglcode INNER JOIN erp_templatesdetails ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID AND erp_templatesdetails.templatesMasterAutoID = erp_templatesglcode.templateMasterID WHERE erp_templatesglcode.templateMasterID = 15 OR erp_templatesglcode.templateMasterID = 3 ) AS templateGLCode ON templateGLCode.chartOfAccountSystemID = erp_budgetconsumeddata.chartOfAccountID WHERE erp_budgetconsumeddata.Year = ' . $budgetYear . ' AND erp_budgetconsumeddata.companySystemID = ' . $companySystemID . ' AND erp_budgetconsumeddata.serviceLineSystemID = ' . $serviceLineSystemID . ' AND templateGLCode.templatesDetailsAutoID = ' . $documentAmount->templatesDetailsAutoID . ' GROUP BY erp_budgetconsumeddata.companySystemID, erp_budgetconsumeddata.serviceLineSystemID, erp_budgetconsumeddata.Year,templateGLCode.templatesDetailsAutoID'))->first();

                                                $pendingAmount = collect(\DB::select('SELECT erp_purchaseordermaster.companySystemID, erp_purchaseordermaster.serviceLineSystemID, templateGLCode.templatesDetailsAutoID,templateGLCode.templateDetailDescription,Sum(GRVcostPerUnitLocalCur * noQty) AS localAmt,Sum(GRVcostPerUnitComRptCur * noQty) AS rptAmt,erp_purchaseorderdetails.budgetYear FROM erp_purchaseordermaster INNER JOIN erp_purchaseorderdetails ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID INNER JOIN (SELECT erp_templatesglcode.templatesDetailsAutoID, erp_templatesdetails.templateDetailDescription, erp_templatesglcode.templateMasterID,erp_templatesglcode.chartOfAccountSystemID,erp_templatesglcode.glCode FROM erp_templatesglcode INNER JOIN erp_templatesdetails ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID AND erp_templatesdetails.templatesMasterAutoID = erp_templatesglcode.templateMasterID WHERE erp_templatesglcode.templateMasterID = 15 OR erp_templatesglcode.templateMasterID = 3 ) AS templateGLCode ON templateGLCode.chartOfAccountSystemID = erp_purchaseorderdetails.financeGLcodePLSystemID WHERE erp_purchaseordermaster.approved = 0 AND erp_purchaseordermaster.poCancelledYN= 0 AND erp_purchaseordermaster.budgetYear = ' . $budgetYear . ' AND erp_purchaseordermaster.companySystemID = ' . $companySystemID . ' AND erp_purchaseordermaster.serviceLineSystemID = ' . $serviceLineSystemID . ' AND templateGLCode.templatesDetailsAutoID = ' . $documentAmount->templatesDetailsAutoID . ' GROUP BY erp_purchaseordermaster.companySystemID, erp_purchaseordermaster.serviceLineSystemID, templateGLCode.templatesDetailsAutoID, erp_purchaseorderdetails.budgetYear'))->first();

                                                //get reporting amount converted
                                                $currencyConversionRptAmount = self::currencyConversion($companySystemID, $purchaseRequestMaster->currency, $purchaseRequestMaster->currency, $documentAmount->totalCost);

                                                $budgetDescription = '';
                                                $totalDocumentRptAmount = $currencyConversionRptAmount['reportingAmount'];
                                                $totalBudgetRptAmount = 0;
                                                $totalConsumedRptAmount = 0;
                                                $totalPendingRptAmount = 0;
                                                if ($consumedAmount) {
                                                    $totalConsumedRptAmount = $consumedAmount->ConsumedRptAmount;
                                                }

                                                if ($pendingAmount) {
                                                    $totalPendingRptAmount = $pendingAmount->rptAmt;
                                                }

                                                if ($budgetAmount) {
                                                    $totalBudgetRptAmount = ($budgetAmount->budgetRptAmount * -1);
                                                    $budgetDescription = $budgetAmount->templateDetailDescription;
                                                }

                                                $totalConsumedAmount = $currencyConversionRptAmount['reportingAmount'] + $totalConsumedRptAmount + $totalPendingRptAmount;

                                                if ($totalConsumedAmount > $totalBudgetRptAmount) {
                                                    $userMessageE .= "Budget Exceeded ' . $budgetDescription . '";
                                                    $userMessageE .= "<br>";
                                                    $userMessageE .= "Budget Amount : '" . round($totalBudgetRptAmount, 2) . "'";
                                                    $userMessageE .= "<br>";
                                                    $userMessageE .= "Document Amount : '" . round($totalDocumentRptAmount, 2) . "'";
                                                    $userMessageE .= "<br>";
                                                    $userMessageE .= "Consumed Amount : '" . round($totalConsumedRptAmount, 2) . "'";
                                                    $userMessageE .= "<br>";
                                                    $userMessageE .= "Pending PO Amount : '" . round($totalPendingRptAmount, 2) . "'";
                                                    $userMessageE .= "<br>";
                                                    $userMessageE .= "Total Consumed Amount : '" . round($totalConsumedAmount, 2) . "'";
                                                    // update PR master table
                                                    $prMasterUpdate = $namespacedModel::find($input["documentSystemCode"])->update(['budgetBlockYN' => -1]);
                                                    DB::commit();
                                                    return ['success' => false, 'message' => $userMessageE];
                                                } else {
                                                    $userMessage .= "<br>";
                                                    $userMessage .= "Budget Amount : '" . round($totalBudgetRptAmount, 2) . "'";
                                                    $userMessage .= "<br>";
                                                    $userMessage .= "Document Amount : '" . round($totalDocumentRptAmount, 2) . "'";
                                                    $userMessage .= "<br>";
                                                    $userMessage .= "Consumed Amount : '" . round($totalConsumedRptAmount, 2) . "'";
                                                    $userMessage .= "<br>";
                                                    $userMessage .= "Pending PO Amount : '" . round($totalPendingRptAmount, 2) . "'";
                                                    $userMessage .= "<br>";
                                                    $userMessage .= "Total Consumed Amount : '" . round($totalConsumedAmount, 2) . "'";

                                                    // update PR master table
                                                    $prMasterUpdate = $namespacedModel::find($input["documentSystemCode"])->update(['budgetBlockYN' => 0]);
                                                }

                                            }
                                        }// closing finance Category check if condition
                                    } else {
                                        // update PR master table
                                        $prMasterUpdate = $namespacedModel::find($input["documentSystemCode"])->update(['budgetBlockYN' => 0]);
                                    }
                                }
                            }
                        }

                        if ($approvalLevel->noOfLevels == $input["rollLevelOrder"]) { // update the document after the final approval
                            $finalupdate = $namespacedModel::find($input["documentSystemCode"])->update([$docInforArr["approvedColumnName"] => $docInforArr["approveValue"], $docInforArr["approvedBy"] => $empInfo->empID, $docInforArr["approvedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["approvedDate"] => now()]);

                            $masterData = ['documentSystemID' => $docApproved->documentSystemID, 'autoID' => $docApproved->documentSystemCode, 'companySystemID' => $docApproved->companySystemID, 'employeeSystemID' => $empInfo->employeeSystemID];

                            if ($input["documentSystemID"] == 57) { //Auto assign item to itemassign table
                                $itemMaster = DB::table('itemmaster')->selectRaw('itemCodeSystem,primaryCode as itemPrimaryCode,secondaryItemCode,barcode,itemDescription,unit as itemUnitOfMeasure,itemUrl,primaryCompanySystemID as companySystemID,primaryCompanyID as companyID,financeCategoryMaster,financeCategorySub, -1 as isAssigned,companymaster.localCurrencyID as wacValueLocalCurrencyID,companymaster.reportingCurrency as wacValueReportingCurrencyID,NOW() as timeStamp')->join('companymaster', 'companySystemID', '=', 'primaryCompanySystemID')->where('itemCodeSystem', $input["documentSystemCode"])->first();
                                $itemAssign = Models\ItemAssigned::insert(collect($itemMaster)->toArray());
                            }

                            if ($input["documentSystemID"] == 56) { //Auto assign item to supplier table
                                $supplierMaster = $namespacedModel::selectRaw('supplierCodeSystem as supplierCodeSytem,primaryCompanySystemID as companySystemID,primaryCompanyID as companyID,uniqueTextcode,primarySupplierCode,secondarySupplierCode,supplierName,liabilityAccountSysemID,liabilityAccount,UnbilledGRVAccountSystemID,UnbilledGRVAccount,address,countryID,supplierCountryID,telephone,fax,supEmail,webAddress,currency,nameOnPaymentCheque,creditLimit,creditPeriod,supCategoryMasterID,supCategorySubID,registrationNumber,registrationExprity,supplierImportanceID,supplierNatureID,supplierTypeID,WHTApplicable,vatEligible,vatNumber,vatPercentage,supCategoryICVMasterID,supCategorySubICVID,isLCCYN,-1 as isAssigned,NOW() as timeStamp')->find($input["documentSystemCode"]);
                                $supplierAssign = Models\SupplierAssigned::insert($supplierMaster->toArray());
                            }

                            if ($input["documentSystemID"] == 59) { //Auto assign item to Chart Of Account
                                $chartOfAccount = $namespacedModel::selectRaw('primaryCompanySystemID as companySystemID,primaryCompanyID as companyID,chartOfAccountSystemID,AccountCode,AccountDescription,masterAccount,catogaryBLorPLID,catogaryBLorPL,controllAccountYN,controlAccountsSystemID,controlAccounts,isActive,isBank,AllocationID,relatedPartyYN,-1 as isAssigned,NOW() as timeStamp')->find($input["documentSystemCode"]);
                                $chartOfAccountAssign = Models\ChartOfAccountsAssigned::insert($chartOfAccount->toArray());
                            }

                            if ($input["documentSystemID"] == 63) { //Create Asset Disposal
                                $assetDisposal = self::generateAssetDisposal($masterData);
                            }

                            if ($input["documentSystemID"] == 17) { //Create Accrual JV Reversal

                                $jvMasterData = $namespacedModel::find($input["documentSystemCode"]);

                                if ($jvMasterData->jvType == 1) {
                                    $accrualJournalVoucher = self::generateAccrualJournalVoucher($input["documentSystemCode"]);
                                } else if ($jvMasterData->jvType == 5) {
                                    $POAccrualJournalVoucher = self::generatePOAccrualJournalVoucher($input["documentSystemCode"]);
                                }

                            }

                            // insert the record to item ledger

                            if (in_array($input["documentSystemID"], [3, 8, 12, 13, 10, 61, 24, 7])) {

                                $jobIL = ItemLedgerInsert::dispatch($masterData);
                            }

                            // insert the record to general ledger

                            if (in_array($input["documentSystemID"], [3, 8, 12, 13, 10, 20, 61, 24, 7, 19, 15, 11, 4, 21, 22, 17, 23, 41])) {
                                $jobGL = GeneralLedgerInsert::dispatch($masterData);
                                if ($input["documentSystemID"] == 3) {
                                    $jobUGRV = UnbilledGRVInsert::dispatch($masterData);
                                    $jobSI = CreateGRVSupplierInvoice::dispatch($input["documentSystemCode"]);
                                }
                            }

                            if ($input["documentSystemID"] == 21) {
                                //$bankLedgerInsert = \App\Jobs\BankLedgerInsert::dispatch($masterData);
                                $bankLedgerInsert = self::appendToBankLedger($input["documentSystemCode"]);
                            }

                            $sourceModel = $namespacedModel::find($input["documentSystemCode"]);
                            if ($input["documentSystemID"] == 13 && !empty($sourceModel)) {
                                $jobCI = CreateStockReceive::dispatch($sourceModel);
                            }
                            if ($input["documentSystemID"] == 10 && !empty($sourceModel)) {
                                $jobSI = CreateSupplierInvoice::dispatch($sourceModel);
                            }
                            if ($input["documentSystemID"] == 4 && !empty($sourceModel)) {
                                //$jobPV = CreateReceiptVoucher::dispatch($sourceModel);
                                if ($sourceModel->invoiceType == 3) {
                                    $jobPV = self::generateCustomerReceiptVoucher($sourceModel);
                                } else {
                                    $bankLedger = BankLedgerInsert::dispatch($masterData);
                                }
                            }

                            if ($input["documentSystemID"] == 46 && !empty($sourceModel)) {
                                $jobBTN = BudgetAdjustment::dispatch($sourceModel);
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
                                                    $data['createdPcID'] = gethostname();
                                                    $data['createdUserID'] = \Helper::getEmployeeID();
                                                    $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
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
                                    $jobCI = CreateCustomerInvoice::dispatch($sourceModel);
                                }
                                $updateDisposed = Models\AssetDisposalDetail::ofMaster($input["documentSystemCode"])->get();
                                if (count($updateDisposed) > 0) {
                                    foreach ($updateDisposed as $val) {
                                        $faMaster = Models\FixedAssetMaster::find($val->faID)->update(['DIPOSED' => -1, 'disposedDate' => NOW(), 'assetdisposalMasterAutoID' => $input["documentSystemCode"]]);
                                    }
                                }
                            }

                            // insert the record to budget consumed data
                            if ($input["documentSystemID"] == 2 || $input["documentSystemID"] == 5 || $input["documentSystemID"] == 52) {
                                $budgetConsumeData = array();
                                $poMaster = $namespacedModel::selectRaw('MONTH(createdDateTime) as month, purchaseOrderCode,documentID,documentSystemID, financeCategory')->find($input["documentSystemCode"]);

                                if ($poMaster->financeCategory == 3) {
                                    $poDetail = \DB::select('SELECT SUM(erp_purchaseorderdetails.GRVcostPerUnitLocalCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitLocalCur,SUM(erp_purchaseorderdetails.GRVcostPerUnitComRptCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitComRptCur,erp_purchaseorderdetails.companyReportingCurrencyID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.financeGLcodePL,erp_purchaseorderdetails.companyID,erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.serviceLineCode,erp_purchaseorderdetails.budgetYear,erp_purchaseorderdetails.localCurrencyID FROM erp_purchaseorderdetails INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID  WHERE erp_purchaseorderdetails.purchaseOrderMasterID = ' . $input["documentSystemCode"] . ' AND erp_purchaseordermaster.poType_N IN(1,2,3,4,5) GROUP BY erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.budgetYear');
                                    if (!empty($poDetail)) {
                                        foreach ($poDetail as $value) {
                                            $budgetConsumeData[] = array(
                                                "companySystemID" => $value->companySystemID,
                                                "companyID" => $value->companyID,
                                                "serviceLineSystemID" => $value->serviceLineSystemID,
                                                "serviceLineCode" => $value->serviceLineCode,
                                                "documentSystemID" => $poMaster["documentSystemID"],
                                                "documentID" => $poMaster["documentID"],
                                                "documentSystemCode" => $input["documentSystemCode"],
                                                "documentCode" => $poMaster["purchaseOrderCode"],
                                                "chartOfAccountID" => 9,
                                                "GLCode" => 10000,
                                                "year" => $value->budgetYear,
                                                "month" => $poMaster["month"],
                                                "consumedLocalCurrencyID" => $value->localCurrencyID,
                                                "consumedLocalAmount" => $value->GRVcostPerUnitLocalCur,
                                                "consumedRptCurrencyID" => $value->companyReportingCurrencyID,
                                                "consumedRptAmount" => $value->GRVcostPerUnitComRptCur,
                                                "timestamp" => date('d/m/Y H:i:s A')
                                            );
                                        }
                                        $budgetConsume = Models\BudgetConsumedData::insert($budgetConsumeData);
                                    }
                                } else {
                                    $poDetail = \DB::select('SELECT SUM(erp_purchaseorderdetails.GRVcostPerUnitLocalCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitLocalCur,SUM(erp_purchaseorderdetails.GRVcostPerUnitComRptCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitComRptCur,erp_purchaseorderdetails.companyReportingCurrencyID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.financeGLcodePL,erp_purchaseorderdetails.companyID,erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.serviceLineCode,erp_purchaseorderdetails.budgetYear,erp_purchaseorderdetails.localCurrencyID FROM erp_purchaseorderdetails INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID  WHERE erp_purchaseorderdetails.purchaseOrderMasterID = ' . $input["documentSystemCode"] . ' AND erp_purchaseordermaster.poType_N IN(1,2,3,4,5) GROUP BY erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.budgetYear');
                                    if (!empty($poDetail)) {
                                        foreach ($poDetail as $value) {
                                            if ($value->financeGLcodePLSystemID != "") {
                                                $budgetConsumeData[] = array(
                                                    "companySystemID" => $value->companySystemID,
                                                    "companyID" => $value->companyID,
                                                    "serviceLineSystemID" => $value->serviceLineSystemID,
                                                    "serviceLineCode" => $value->serviceLineCode,
                                                    "documentSystemID" => $poMaster["documentSystemID"],
                                                    "documentID" => $poMaster["documentID"],
                                                    "documentSystemCode" => $input["documentSystemCode"],
                                                    "documentCode" => $poMaster["purchaseOrderCode"],
                                                    "chartOfAccountID" => $value->financeGLcodePLSystemID,
                                                    "GLCode" => $value->financeGLcodePL,
                                                    "year" => $value->budgetYear,
                                                    "month" => $poMaster["month"],
                                                    "consumedLocalCurrencyID" => $value->localCurrencyID,
                                                    "consumedLocalAmount" => $value->GRVcostPerUnitLocalCur,
                                                    "consumedRptCurrencyID" => $value->companyReportingCurrencyID,
                                                    "consumedRptAmount" => $value->GRVcostPerUnitComRptCur,
                                                    "timestamp" => date('d/m/Y H:i:s A')
                                                );
                                            }
                                        }
                                        $budgetConsume = Models\BudgetConsumedData::insert($budgetConsumeData);
                                    }
                                }
                            }

                        } else {
                            // update roll level in master table
                            $rollLevelUpdate = $namespacedModel::find($input["documentSystemCode"])->update(['RollLevForApp_curr' => $input["rollLevelOrder"] + 1]);
                        }
                        // update record in document approved table
                        $approvedeDoc = $docApproved::find($input["documentApprovedID"])->update(['approvedYN' => -1, 'approvedDate' => now(), 'approvedComments' => $input["approvedComments"], 'employeeID' => $empInfo->empID, 'employeeSystemID' => $empInfo->employeeSystemID]);

                        $sourceModel = $namespacedModel::find($input["documentSystemCode"]);
                        $currentApproved = Models\DocumentApproved::find($input["documentApprovedID"]);
                        $emails = array();
                        if (!empty($sourceModel)) {
                            $document = Models\DocumentMaster::where('documentSystemID', $currentApproved->documentSystemID)->first();
                            $subjectName = $document->documentDescription . ' ' . $currentApproved->documentCode;
                            $bodyName = $document->documentDescription . ' ' . '<b>' . $currentApproved->documentCode . '</b>';

                            if ($sourceModel[$docInforArr["confirmedYN"]] == 1 || $sourceModel[$docInforArr["confirmedYN"]] == -1) {

                                if ($approvalLevel->noOfLevels == $input["rollLevelOrder"]) { // if fully approved
                                    $subject = $subjectName . " is fully approved";
                                    $body = $bodyName . " is fully approved . ";
                                } else {

                                    $companyDocument = Models\CompanyDocumentAttachment::where('companySystemID', $currentApproved->companySystemID)
                                        ->where('documentSystemID', $currentApproved->documentSystemID)
                                        ->first();

                                    if (empty($companyDocument)) {
                                        return ['success' => false, 'message' => 'Policy not found for this document'];
                                    }

                                    $nextLevel = $currentApproved->rollLevelOrder + 1;

                                    $nextApproval = Models\DocumentApproved::where('companySystemID', $currentApproved->companySystemID)
                                        ->where('documentSystemID', $currentApproved->documentSystemID)
                                        ->where('documentSystemCode', $currentApproved->documentSystemCode)
                                        ->where('rollLevelOrder', $nextLevel)
                                        ->first();

                                    $approvalList = Models\EmployeesDepartment::where('employeeGroupID', $nextApproval->approvalGroupID)
                                        ->where('companySystemID', $currentApproved->companySystemID)
                                        ->where('documentSystemID', $currentApproved->documentSystemID);


                                    if ($companyDocument['isServiceLineApproval'] == -1) {
                                        $approvalList = $approvalList->where('ServiceLineSystemID', $currentApproved->serviceLineSystemID);
                                    }

                                    $approvalList = $approvalList
                                        ->with(['employee'])
                                        ->groupBy('employeeSystemID')
                                        ->get();


                                    $nextApprovalBody = '<p>' . $bodyName . ' Level ' . $currentApproved->rollLevelOrder . ' is approved and pending for your approval.</p>';
                                    $nextApprovalSubject = $subjectName . " Level " . $currentApproved->rollLevelOrder . " is approved and pending for your approval";
                                    $nextApproveNameList = "";
                                    foreach ($approvalList as $da) {
                                        if ($da->employee) {

                                            $nextApproveNameList = $nextApproveNameList . '<br>' . $da->employee->empName;

                                            $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                                                'companySystemID' => $nextApproval->companySystemID,
                                                'docSystemID' => $nextApproval->documentSystemID,
                                                'alertMessage' => $nextApprovalSubject,
                                                'emailAlertMessage' => $nextApprovalBody,
                                                'docSystemCode' => $nextApproval->documentSystemCode);
                                        }
                                    }

                                    $subject = $subjectName . " Level " . $currentApproved->rollLevelOrder . " is approved and sent to next level approval";
                                    $body = $bodyName . " Level " . $currentApproved->rollLevelOrder . " is approved and sent to next level approval to below employees < br>" . $nextApproveNameList;
                                }

                                $emails[] = array('empSystemID' => $sourceModel[$docInforArr["confirmedEmpSystemID"]],
                                    'companySystemID' => $currentApproved->companySystemID,
                                    'docSystemID' => $currentApproved->documentSystemID,
                                    'alertMessage' => $subject,
                                    'emailAlertMessage' => $body,
                                    'docSystemCode' => $input["documentSystemCode"]);
                            }
                        }

                        $sendEmail = \Email::sendEmail($emails);
                        if (!$sendEmail["success"]) {
                            return ['success' => false, 'message' => $sendEmail["message"]];
                        }

                    } else {
                        return ['success' => false, 'message' => 'Approval level not found'];
                    }
                    DB::commit();
                    return ['success' => true, 'message' => $userMessage];
                } else {
                    return ['success' => false, 'message' => 'Document is already approved'];
                }
            } else {
                return ['success' => false, 'message' => 'No records found'];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return ['success' => false, 'message' => 'Error Occurred'];
        }
    }


    /**
     * function to reject documents
     * @param $input - get line record
     * @return array
     */
    public static function rejectDocument($input)
    {
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
                    break;
                case 1:
                case 50:
                case 51:
                    $docInforArr["tableName"] = 'erp_purchaserequest';
                    $docInforArr["modelName"] = 'PurchaseRequest';
                    $docInforArr["primarykey"] = 'purchaseRequestID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 20:
                    $docInforArr["tableName"] = 'erp_custinvoicedirect';
                    $docInforArr["modelName"] = 'CustomerInvoiceDirect';
                    $docInforArr["primarykey"] = 'custInvoiceDirectAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 11:
                    $docInforArr["tableName"] = 'erp_bookinvsuppmaster';
                    $docInforArr["modelName"] = 'BookInvSuppMaster';
                    $docInforArr["primarykey"] = 'bookingSuppMasInvAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 46:
                    $docInforArr["tableName"] = 'erp_budgettransferform';
                    $docInforArr["modelName"] = 'BudgetTransferForm';
                    $docInforArr["primarykey"] = 'budgetTransferFormAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 65: // budget
                    $docInforArr["tableName"] = 'erp_budgetmaster';
                    $docInforArr["modelName"] = 'BudgetMaster';
                    $docInforArr["primarykey"] = 'budgetmasterID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 22: // Asset Costing
                    $docInforArr["tableName"] = 'erp_fa_asset_master';
                    $docInforArr["modelName"] = 'FixedAssetMaster';
                    $docInforArr["primarykey"] = 'faID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 23: // Asset Depreciation
                    $docInforArr["tableName"] = 'erp_fa_depmaster';
                    $docInforArr["modelName"] = 'FixedAssetDepreciationMaster';
                    $docInforArr["primarykey"] = 'depMasterAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 21: //  Customer Receipt Voucher
                    $docInforArr["tableName"] = 'erp_customerreceivepayment';
                    $docInforArr["modelName"] = 'CustomerReceivePayment';
                    $docInforArr["primarykey"] = 'custReceivePaymentAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 4: // Payment Voucher
                    $docInforArr["tableName"] = 'erp_paysupplierinvoicemaster';
                    $docInforArr["modelName"] = 'PaySupplierInvoiceMaster';
                    $docInforArr["primarykey"] = 'PayMasterAutoId';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 19: // Credit Note
                    $docInforArr["tableName"] = 'erp_creditnote';
                    $docInforArr["modelName"] = 'CreditNote';
                    $docInforArr["primarykey"] = 'creditNoteAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 13: // stock transfer
                    $docInforArr["tableName"] = 'erp_stocktransfer';
                    $docInforArr["modelName"] = 'StockTransfer';
                    $docInforArr["primarykey"] = 'stockTransferAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 10: // stock receive
                    $docInforArr["tableName"] = 'erp_stockreceive';
                    $docInforArr["modelName"] = 'StockReceive';
                    $docInforArr["primarykey"] = 'stockReceiveAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 15: // Debit Note
                    $docInforArr["tableName"] = 'erp_debitnote';
                    $docInforArr["modelName"] = 'DebitNote';
                    $docInforArr["primarykey"] = 'debitNoteAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 8: // Materiel Issue
                    $docInforArr["tableName"] = 'erp_itemissuemaster';
                    $docInforArr["modelName"] = 'ItemIssueMaster';
                    $docInforArr["primarykey"] = 'itemIssueAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 12: // Materiel Return
                    $docInforArr["tableName"] = 'erp_itemreturnmaster';
                    $docInforArr["modelName"] = 'ItemReturnMaster';
                    $docInforArr["primarykey"] = 'itemReturnAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 17: //  Journal Voucher
                    $docInforArr["tableName"] = 'erp_jvmaster';
                    $docInforArr["modelName"] = 'JvMaster';
                    $docInforArr["primarykey"] = 'jvMasterAutoId';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 9: // Materiel Request
                    $docInforArr["tableName"] = 'erp_request';
                    $docInforArr["modelName"] = 'MaterielRequest';
                    $docInforArr["primarykey"] = 'RequestID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 63: //  Asset Capitalization
                    $docInforArr["tableName"] = 'erp_fa_assetcapitalization';
                    $docInforArr["modelName"] = 'AssetCapitalization';
                    $docInforArr["primarykey"] = 'capitalizationID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 41: //  Asset Disposal
                    $docInforArr["tableName"] = 'erp_fa_asset_disposalmaster';
                    $docInforArr["modelName"] = 'AssetDisposalMaster';
                    $docInforArr["primarykey"] = 'assetdisposalMasterAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 64: // Payment bank transfer
                    $docInforArr["tableName"] = 'erp_paymentbanktransfer';
                    $docInforArr["modelName"] = 'PaymentBankTransfer';
                    $docInforArr["primarykey"] = 'paymentBankTransferID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 62: // Bank Reconciliation
                    $docInforArr["tableName"] = 'erp_bankrecmaster';
                    $docInforArr["modelName"] = 'BankReconciliation';
                    $docInforArr["primarykey"] = 'bankRecAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 57: // Item Master
                    $docInforArr["tableName"] = 'itemmaster';
                    $docInforArr["modelName"] = 'ItemMaster';
                    $docInforArr["primarykey"] = 'itemCodeSystem';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 3: // Good Receipt Voucher
                    $docInforArr["tableName"] = 'erp_grvmaster';
                    $docInforArr["modelName"] = 'GRVMaster';
                    $docInforArr["primarykey"] = 'grvAutoID';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                case 56: // Supplier master
                    $docInforArr["tableName"] = 'suppliermaster';
                    $docInforArr["modelName"] = 'SupplierMaster';
                    $docInforArr["primarykey"] = 'supplierCodeSystem';
                    $docInforArr["referredColumnName"] = 'timesReferred';
                    break;
                default:
                    return ['success' => false, 'message' => 'Document ID not set'];
            }
            //check document exist
            $docApprove = Models\DocumentApproved::find($input["documentApprovedID"]);
            if ($docApprove) {
                //check document is already rejected
                $isRejected = Models\DocumentApproved::where('documentApprovedID', $input["documentApprovedID"])->where('rejectedYN', -1)->first();
                if (!$isRejected) {
                    $approvalLevel = Models\ApprovalLevel::find($input["approvalLevelID"]);
                    if ($approvalLevel) {
                        // get current employee detail
                        $empInfo = self::getEmployeeInfo();
                        // update record in document approved table
                        $approvedeDoc = $docApprove->update(['rejectedYN' => -1, 'rejectedDate' => now(), 'rejectedComments' => $input["rejectedComments"], 'employeeID' => $empInfo->empID, 'employeeSystemID' => $empInfo->employeeSystemID]);
                        if (in_array($input["documentSystemID"], [2, 5, 52, 1, 50, 51, 20, 11, 46, 22, 23, 21, 4, 19,13,10,15,8,12,17,9,63,41,64,62,3,57,56])) {
                            $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
                            $timesReferredUpdate = $namespacedModel::find($docApprove["documentSystemCode"])->increment($docInforArr["referredColumnName"]);
                            $refferedBackYNUpdate = $namespacedModel::find($docApprove["documentSystemCode"])->update(['refferedBackYN' => -1]);
                        }
                    } else {
                        return ['success' => false, 'message' => 'Approval level not found'];
                    }
                    DB::commit();
                    return ['success' => true, 'message' => 'Document is successfully rejected'];
                } else {
                    return ['success' => false, 'message' => 'Document is already rejected'];
                }
            } else {
                return ['success' => false, 'message' => 'No record found'];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e . 'Error Occurred'];
        }
    }

    /**
     * get current employee information
     * @return mixed
     */
    public static function getEmployeeInfo()
    {
        $user = Models\User::find(Auth::id());
        $employee = Models\Employee::with(['profilepic'])->find($user->employee_id);
        return $employee;
    }

    public static function getEmployeeInfoByURL($input)
    {

        if (!array_key_exists('Authorization', $input) || $input['Authorization'] == "") {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        if (strpos($input['Authorization'], 'Bearer') === false) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $token = trim(str_replace("", "", $input['Authorization']));

        $oauth = Models\AccessTokens::where('id', '<>', $token)
            //->where('id','like',"%{$token}%")
            ->where('revoked', 0)
            ->get();

        if (empty($oauth)) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $id = 2637;
        $user = Models\User::find($id);
        $employee = Models\Employee::find($user->employee_id);

        if ($employee) {
            return ['success' => true, 'message' => $employee];
        } else {
            return ['success' => false, 'message' => 'Unauthorized'];
        }
    }


    /**
     * @param $date
     * @return false|string
     */
    public static function dateFormat($date)
    {
        if ($date) {
            return date("d/m/Y", strtotime($date));
        } else {
            return '';
        }

    }

    public static function checkIsCompanyGroup($companyID)
    {
        $isCompaniesGroup = Models\Company::where('companySystemID', $companyID)->where('isGroup', -1)->exists();
        if ($isCompaniesGroup) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get employee system id
     * @return mixed
     */
    public static function getEmployeeSystemID()
    {
        $user = Models\User::find(Auth::id());
        return $user->employee_id;
    }

    public static function getEmployeeCode($empId)
    {
        $employee = Models\Employee::find($empId);
        if (!empty($employee)) {
            return $employee->empID;
        }
        return 0;
    }

    public static function getEmployeeID()
    {
        $user = Models\User::find(Auth::id());
        return $user->empID;
    }


    public static function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    public static function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }

    /**
     * Get all company company Finance Year
     * @param $companySystemID - current company id
     * @return array
     */
    public static function companyFinanceYear($companySystemID, $isAllowBackDate = 0)
    {
        $companyFinanceYear = Models\CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ', DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"))
            ->where('companySystemID', '=', $companySystemID)
            ->where('isActive', -1);

        if (!$isAllowBackDate) {
            $companyFinanceYear->where('isCurrent', -1);
        }
        $companyFinanceYear = $companyFinanceYear->get();
        return $companyFinanceYear;
    }

    /**
     * Get all company company Finance Year
     * @param $companySystemID - current company id
     * @return array
     */
    public static function companyFinancePeriod($companySystemID, $companyFinanceYearID, $departmentSystemID)
    {
        $companyFinancePeriod = Models\CompanyFinancePeriod::select(DB::raw("companyFinancePeriodID,isCurrent,CONCAT(DATE_FORMAT(dateFrom, '%d/%m/%Y'), ' | ', DATE_FORMAT(dateTo, '%d/%m/%Y')) as financePeriod"))
            ->where('companySystemID', '=', $companySystemID)
            ->where('companyFinanceYearID', $companyFinanceYearID)
            ->where('departmentSystemID', $departmentSystemID)
            ->where('isActive', -1)
            ->where('isCurrent', -1)
            ->get();
        return $companyFinancePeriod;
    }

    /**
     * Get company local and reporting currency
     * @param $companySystemID - current company id
     * @return array
     */

    public static function companyCurrency($companySystemID)
    {
        $companyCurrency = Models\Company::with(['localcurrency', 'reportingcurrency'])
            ->where('companySystemID', '=', $companySystemID)
            ->first();
        return $companyCurrency;
    }

    /**
     * Get company local and reporting currency
     * @param $companySystemID - current company id
     * @return array
     */

    public static function groupCompaniesCurrency($companySystemID)
    {
        $companyID = "";
        $checkIsGroup = Models\Company::find($companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($companySystemID);
        } else {
            $companyID = [$companySystemID];
        }

        $companyCurrency = Models\Company::with(['localcurrency', 'reportingcurrency'])
            ->whereIN('companySystemID', $companyID)
            ->get();
        $outputArr = [];
        if ($companyCurrency) {
            foreach ($companyCurrency as $val) {
                $outputArr[$val->CompanyID] = $val;
            }
        }
        return $outputArr;
    }

    /**
     * Get all Companies drop
     * @param $companySystemID - current company id
     * @return array
     */
    public static function allCompanies()
    {
        $allCompanies = Models\Company::where('isGroup', 0)->where('isActive', 1)
            ->get();
        return $allCompanies;
    }

    public static function getCurrencyDecimalPlace($currencyID)
    {
        $decimal = Models\CurrencyMaster::where('currencyID', $currencyID)->first();
        return $decimal['DecimalPlaces'];
    }

    public static function dateYear($date)
    {
        if ($date) {
            return date("Y", strtotime($date));
        } else {
            return '';
        }
    }

    public static function dateMonth($date)
    {
        if ($date) {
            return date("m", strtotime($date));
        } else {
            return '';
        }
    }

    public static function currentDate()
    {
        return date("Y-m-d");
    }

    public static function currentDateTime()
    {
        return date("Y-m-d H:i:s");
    }

    public static function getCompanyDocRefNo($companySystemID, $documentSystemID)
    {

        $docAttachment = Models\CompanyDocumentAttachment::where('companySystemID', $companySystemID)
            ->where('documentSystemID', $documentSystemID)
            ->first();

        if (!empty($docAttachment)) {
            return $docAttachment->docRefNumber;
        } else {
            return "";
        }
    }

    public static function roundValue($value)
    {
        return round($value, 7);
    }


    public static function companyFinanceYearCheck($input)
    {
        $companyFinanceYear = Models\CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])->first();
        if ($companyFinanceYear) {
            if ($companyFinanceYear->isActive != -1 && $companyFinanceYear->isCurrent != -1) {
                return ['success' => false, 'message' => 'Selected financial year is not active'];
            } else {
                return ['success' => true, 'message' => $companyFinanceYear];
            }
        } else {
            $companyFinanceYear = Models\CompanyFinanceYear::where('companySystemID', $input['companySystemID'])->where('isActive', -1)->where('isCurrent', -1)->first();
            if (empty($companyFinanceYear)) {
                return ['success' => false, 'message' => 'Financial year not selected/not active'];
            } else {
                return ['success' => false, 'message' => 'Please select a finance year'];
            }
        }
    }

    public static function companyFinancePeriodCheck($input)
    {
        $companyFinancePeriod = Models\CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        if ($companyFinancePeriod) {
            if ($companyFinancePeriod->isActive != -1 && $companyFinancePeriod->isCurrent != -1) {
                return ['success' => false, 'message' => 'Selected financial period is not active'];
            } else {
                return ['success' => true, 'message' => $companyFinancePeriod];
            }
        } else {
            $companyFinancePeriod = Models\CompanyFinancePeriod::where('companySystemID', $input['companySystemID'])->where('isActive', -1)->where('isCurrent', -1)->where('departmentSystemID', $input['departmentSystemID'])->where('companyFinanceYearID', $input['companyFinanceYearID'])->first();
            if (!$companyFinancePeriod) {
                return ['success' => false, 'message' => 'Financial period not selected/not active'];
            } else {
                return ['success' => false, 'message' => 'Please select a finance period'];
            }
        }
    }


    public static function convertAmountToLocalRpt($documentSystemID, $autoID, $transactionAmount)
    {
        $docInforArr = [];
        switch ($documentSystemID) { // check the document id and set relavant parameters
            case 3:
                $docInforArr["modelName"] = 'GRVMaster';
                $docInforArr["transCurrencyID"] = 'supplierTransactionCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'supplierDefaultCurrencyID';
                $docInforArr["rptCurrencyID"] = 'companyReportingCurrencyID';
                $docInforArr["localCurrencyID"] = 'localCurrencyID';
                $docInforArr["transCurrencyER"] = 'supplierTransactionER';
                $docInforArr["rptCurrencyER"] = 'companyReportingER';
                $docInforArr["localCurrencyER"] = 'localCurrencyER';
                $docInforArr["defaultCurrencyER"] = 'supplierDefaultER';
                break;

            case 19:
                $docInforArr["modelName"] = 'CreditNote';
                $docInforArr["transCurrencyID"] = 'customerCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'customerCurrencyID';
                $docInforArr["rptCurrencyID"] = 'companyReportingCurrencyID';
                $docInforArr["localCurrencyID"] = 'localCurrencyID';
                $docInforArr["transCurrencyER"] = 'customerCurrencyER';
                $docInforArr["rptCurrencyER"] = 'companyReportingER';
                $docInforArr["localCurrencyER"] = 'localCurrencyER';
                $docInforArr["defaultCurrencyER"] = 'customerCurrencyER';
                break;
            case 4:
                $docInforArr["modelName"] = 'PaySupplierInvoiceDetail';
                $docInforArr["transCurrencyID"] = 'supplierTransCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'supplierDefaultCurrencyID';
                $docInforArr["rptCurrencyID"] = 'comRptCurrencyID';
                $docInforArr["localCurrencyID"] = 'localCurrencyID';
                $docInforArr["transCurrencyER"] = 'supplierTransER';
                $docInforArr["rptCurrencyER"] = 'comRptER';
                $docInforArr["localCurrencyER"] = 'localER';
                $docInforArr["defaultCurrencyER"] = 'supplierDefaultCurrencyER';
                break;
            case 200: // This is for unbilled grv
                $docInforArr["modelName"] = 'UnbilledGrvGroupBy';
                $docInforArr["transCurrencyID"] = 'supplierTransactionCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'supplierTransactionCurrencyID';
                $docInforArr["rptCurrencyID"] = 'companyReportingCurrencyID';
                $docInforArr["localCurrencyID"] = 'localCurrencyID';
                $docInforArr["transCurrencyER"] = 'supplierTransactionCurrencyER';
                $docInforArr["rptCurrencyER"] = 'companyReportingER';
                $docInforArr["localCurrencyER"] = 'localCurrencyER';
                $docInforArr["defaultCurrencyER"] = 'localCurrencyER';
                break;
            case 11: // This is for Supplier Invoice
                $docInforArr["modelName"] = 'BookInvSuppMaster';
                $docInforArr["transCurrencyID"] = 'supplierTransactionCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'supplierTransactionCurrencyID';
                $docInforArr["rptCurrencyID"] = 'companyReportingCurrencyID';
                $docInforArr["localCurrencyID"] = 'localCurrencyID';
                $docInforArr["transCurrencyER"] = 'supplierTransactionCurrencyER';
                $docInforArr["rptCurrencyER"] = 'companyReportingER';
                $docInforArr["localCurrencyER"] = 'localCurrencyER';
                $docInforArr["defaultCurrencyER"] = 'localCurrencyER';
                break;
            case 21: // This is for receipt voucher - direct
                $docInforArr["modelName"] = 'CustomerReceivePayment';
                $docInforArr["transCurrencyID"] = 'custTransactionCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'custTransactionCurrencyID';
                $docInforArr["rptCurrencyID"] = 'companyRptCurrencyID';
                $docInforArr["localCurrencyID"] = 'localCurrencyID';
                $docInforArr["transCurrencyER"] = 'custTransactionCurrencyER';
                $docInforArr["rptCurrencyER"] = 'companyRptCurrencyER';
                $docInforArr["localCurrencyER"] = 'localCurrencyER';
                $docInforArr["defaultCurrencyER"] = 'localCurrencyER';
                break;
            case 201: // Advance Payment
                $docInforArr["modelName"] = 'AdvancePaymentDetails';
                $docInforArr["transCurrencyID"] = 'supplierTransCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'supplierDefaultCurrencyID';
                $docInforArr["rptCurrencyID"] = 'comRptCurrencyID';
                $docInforArr["localCurrencyID"] = 'localCurrencyID';
                $docInforArr["transCurrencyER"] = 'supplierTransER';
                $docInforArr["rptCurrencyER"] = 'comRptER';
                $docInforArr["localCurrencyER"] = 'localER';
                $docInforArr["defaultCurrencyER"] = 'supplierDefaultCurrencyER';
                break;
            case 202: // Direct Payment
                $docInforArr["modelName"] = 'DirectPaymentDetails';
                $docInforArr["transCurrencyID"] = 'supplierTransCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'bankCurrencyID';
                $docInforArr["rptCurrencyID"] = 'comRptCurrency';
                $docInforArr["localCurrencyID"] = 'localCurrency';
                $docInforArr["transCurrencyER"] = 'supplierTransER';
                $docInforArr["rptCurrencyER"] = 'comRptCurrencyER';
                $docInforArr["localCurrencyER"] = 'localCurrencyER';
                $docInforArr["defaultCurrencyER"] = 'bankCurrencyER';
                break;
            case 203: // Payment Master
                $docInforArr["modelName"] = 'PaySupplierInvoiceMaster';
                $docInforArr["transCurrencyID"] = 'supplierTransCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'BPVbankCurrency';
                $docInforArr["rptCurrencyID"] = 'companyRptCurrencyID';
                $docInforArr["localCurrencyID"] = 'localCurrencyID';
                $docInforArr["transCurrencyER"] = 'supplierTransCurrencyER';
                $docInforArr["rptCurrencyER"] = 'companyRptCurrencyER';
                $docInforArr["localCurrencyER"] = 'localCurrencyER';
                $docInforArr["defaultCurrencyER"] = 'BPVbankCurrencyER';
                break;
            case 14: // Logistic
                $docInforArr["modelName"] = 'Logistic';
                $docInforArr["transCurrencyID"] = 'customInvoiceCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'customInvoiceCurrencyID';
                $docInforArr["rptCurrencyID"] = 'customInvoiceRptCurrencyID';
                $docInforArr["localCurrencyID"] = 'customInvoiceLocalCurrencyID';
                $docInforArr["transCurrencyER"] = 'customInvoiceRptER';
                $docInforArr["rptCurrencyER"] = 'customInvoiceRptER';
                $docInforArr["localCurrencyER"] = 'customInvoiceLocalER';
                $docInforArr["defaultCurrencyER"] = 'customInvoiceRptER';
                break;
            case 204: // MatchingMaster
                $docInforArr["modelName"] = 'MatchDocumentMaster';
                $docInforArr["transCurrencyID"] = 'supplierTransCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'supplierDefCurrencyID';
                $docInforArr["rptCurrencyID"] = 'companyRptCurrencyID';
                $docInforArr["localCurrencyID"] = 'localCurrencyID';
                $docInforArr["transCurrencyER"] = 'supplierTransCurrencyER';
                $docInforArr["rptCurrencyER"] = 'companyRptCurrencyER';
                $docInforArr["localCurrencyER"] = 'localCurrencyER';
                $docInforArr["defaultCurrencyER"] = 'localCurrencyER';
                break;
            case 205: // Receipt Voucher Matching
                $docInforArr["modelName"] = 'CustomerReceivePaymentDetail';
                $docInforArr["transCurrencyID"] = 'custTransactionCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'custTransactionCurrencyID';
                $docInforArr["rptCurrencyID"] = 'companyReportingCurrencyID';
                $docInforArr["localCurrencyID"] = 'localCurrencyID';
                $docInforArr["transCurrencyER"] = 'custTransactionCurrencyER';
                $docInforArr["rptCurrencyER"] = 'companyReportingER';
                $docInforArr["localCurrencyER"] = 'localCurrencyER';
                $docInforArr["defaultCurrencyER"] = 'localCurrencyER';
                break;
            case 206: // Receipt Voucher
                $docInforArr["modelName"] = 'AccountsReceivableLedger';
                $docInforArr["transCurrencyID"] = 'custTransCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'custTransCurrencyID';
                $docInforArr["rptCurrencyID"] = 'comRptCurrencyID';
                $docInforArr["localCurrencyID"] = 'localCurrencyID';
                $docInforArr["transCurrencyER"] = 'custTransER';
                $docInforArr["rptCurrencyER"] = 'comRptER';
                $docInforArr["localCurrencyER"] = 'localER';
                $docInforArr["defaultCurrencyER"] = 'localER';
                break;
            default:
                return ['success' => false, 'message' => 'Document ID not found'];
        }

        $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
        $masterData = $namespacedModel::find($autoID);
        if ($masterData) {
            $transactionCurrencyID = $masterData[$docInforArr["transCurrencyID"]];
            $transactionDefaultCurrencyID = $masterData[$docInforArr["transDefaultCurrencyID"]];
            $reportingCurrencyID = $masterData[$docInforArr["rptCurrencyID"]];
            $locaCurrencyID = $masterData[$docInforArr["localCurrencyID"]];
            $trasToRptER = $masterData[$docInforArr["rptCurrencyER"]];
            $trasToTransER = $masterData[$docInforArr["transCurrencyER"]];
            $trasToLocER = $masterData[$docInforArr["localCurrencyER"]];
            $trasToDefaultER = $masterData[$docInforArr["defaultCurrencyER"]];
            $reportingAmount = 0;
            $localAmount = 0;
            $defaultAmount = 0;

            if ($transactionCurrencyID == $reportingCurrencyID) {
                $reportingAmount = $transactionAmount;
            } else {
                if ($trasToRptER > $trasToTransER) {
                    if ($trasToRptER > 1) {
                        $reportingAmount = $transactionAmount / $trasToRptER;
                    } else {
                        $reportingAmount = $transactionAmount * $trasToRptER;
                    }
                } else {
                    If ($trasToRptER > 1) {
                        $reportingAmount = $transactionAmount * $trasToRptER;
                    } else {
                        $reportingAmount = $transactionAmount / $trasToRptER;
                    }
                }
            }

            if ($transactionCurrencyID == $locaCurrencyID) {
                $localAmount = $transactionAmount;
            } else {
                if ($trasToLocER > $trasToTransER) {
                    if ($trasToLocER > 1) {
                        $localAmount = $transactionAmount / $trasToLocER;
                    } else {
                        $localAmount = $transactionAmount * $trasToLocER;
                    }
                } else {
                    If ($trasToLocER > 1) {
                        $localAmount = $transactionAmount * $trasToLocER;
                    } else {
                        $localAmount = $transactionAmount / $trasToLocER;
                    }
                }
            }

            if ($transactionCurrencyID == $transactionDefaultCurrencyID) {
                $defaultAmount = $transactionAmount;
            } else {
                if ($trasToDefaultER > $trasToTransER) {
                    if ($trasToDefaultER > 1) {
                        $defaultAmount = $transactionAmount / $trasToDefaultER;
                    } else {
                        $defaultAmount = $transactionAmount * $trasToDefaultER;
                    }
                } else {
                    If ($trasToDefaultER > 1) {
                        $defaultAmount = $transactionAmount * $trasToDefaultER;
                    } else {
                        $defaultAmount = $transactionAmount / $trasToDefaultER;
                    }
                }
            }

        } else {
            return ['success' => false, 'message' => 'No records found'];
        }


        $array = array(
            'reportingAmount' => self::roundValue($reportingAmount),
            'localAmount' => self::roundValue($localAmount),
            'defaultAmount' => self::roundValue($defaultAmount),
        );

        return $array;
    }

    public static function generateAssetDisposal($masterData)
    {
        $fixedCapital = Models\AssetCapitalization::find($masterData['autoID']);

        if ($fixedCapital->allocationTypeID == 1) {

            $companyFinanceYear = Models\CompanyFinanceYear::where('companySystemID', $fixedCapital['companySystemID'])->where('bigginingDate', '<', NOW())->where('endingDate', '>', NOW())->first();

            $companyFinancePeriod = Models\CompanyFinancePeriod::where('companySystemID', $fixedCapital['companySystemID'])->where('departmentSystemID', 9)->where('companyFinanceYearID', $companyFinanceYear['companyFinanceYearID'])->where('dateFrom', '<', NOW())->where('dateTo', '>', NOW())->first();

            $lastSerial = Models\AssetDisposalMaster::where('companySystemID', $fixedCapital['companySystemID'])
                ->where('companyFinanceYearID', $companyFinanceYear['companyFinanceYearID'])
                ->orderBy('assetdisposalMasterAutoID', 'desc')
                ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            $startYear = $companyFinanceYear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];

            $documentCode = ($fixedCapital['companyID'] . '\\' . $finYear . '\\FADS' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

            $dpMaster['companySystemID'] = $fixedCapital['companySystemID'];
            $dpMaster['companyID'] = $fixedCapital['companyID'];
            $dpMaster['companyFinanceYearID'] = $fixedCapital['companyFinanceYearID'];
            $dpMaster['FYBiggin'] = $companyFinanceYear['bigginingDate'];
            $dpMaster['FYEnd'] = $companyFinanceYear['endingDate'];
            $dpMaster['FYPeriodDateFrom'] = $companyFinancePeriod['dateFrom'];
            $dpMaster['FYPeriodDateTo'] = $companyFinancePeriod['dateTo'];
            $dpMaster['companyFinancePeriodID'] = $companyFinancePeriod['companyFinancePeriodID'];
            $dpMaster['documentSystemID'] = 41;
            $dpMaster['documentID'] = 'FADS';
            $dpMaster['serialNo'] = $lastSerialNumber;
            $dpMaster['disposalDocumentCode'] = $documentCode;
            $dpMaster['disposalDocumentDate'] = $fixedCapital['documentDate'];
            $dpMaster['narration'] = 'Asset Re-Allocation related to ' . $fixedCapital['capitalizationCode'];
            $dpMaster['disposalType'] = 8;
            $dpMaster['createdUserID'] = $fixedCapital['createdUserID'];
            $dpMaster['createdUserSystemID'] = $fixedCapital['createdUserSystemID'];

            $output = Models\AssetDisposalMaster::create($dpMaster);
            if ($output) {
                $asset = Models\FixedAssetMaster::find($fixedCapital['faID']);

                $depreciationLocal = Models\FixedAssetDepreciationPeriod::OfCompany([$fixedCapital['companySystemID']])->OfAsset($fixedCapital['faID'])->sum('depAmountLocal');
                $depreciationRpt = Models\FixedAssetDepreciationPeriod::OfCompany([$fixedCapital['companySystemID']])->OfAsset($fixedCapital['faID'])->sum('depAmountRpt');

                $nbvRpt = $asset->costUnitRpt - $depreciationRpt;
                $nbvLocal = $asset->COSTUNIT - $depreciationLocal;

                $dpDetail['assetdisposalMasterAutoID'] = $output['assetdisposalMasterAutoID'];
                $dpDetail['companySystemID'] = $fixedCapital['companySystemID'];
                $dpDetail['companyID'] = $fixedCapital['companyID'];
                $dpDetail['serviceLineSystemID'] = $asset['serviceLineSystemID'];
                $dpDetail['serviceLineCode'] = $asset['serviceLineCode'];
                $dpDetail['itemCode'] = $asset['itemSystemCode'];
                $dpDetail['faID'] = $asset['faID'];
                $dpDetail['faCode'] = $asset['faCode'];
                $dpDetail['faUnitSerialNo'] = $asset['faUnitSerialNo'];
                $dpDetail['assetDescription'] = $asset['assetDescription'];
                $dpDetail['COSTUNIT'] = $asset['COSTUNIT'];
                $dpDetail['costUnitRpt'] = $asset['costUnitRpt'];
                $dpDetail['netBookValueLocal'] = $nbvLocal;
                $dpDetail['depAmountLocal'] = $depreciationLocal;
                $dpDetail['depAmountRpt'] = $depreciationRpt;
                $dpDetail['netBookValueRpt'] = $nbvRpt;
                $dpDetail['COSTGLCODE'] = $asset['COSTGLCODE'];
                $dpDetail['COSTGLCODESystemID'] = $asset['costglCodeSystemID'];
                $dpDetail['ACCDEPGLCODE'] = $asset['ACCDEPGLCODE'];
                $dpDetail['ACCDEPGLCODESystemID'] = $asset['accdepglCodeSystemID'];
                $dpDetail['DISPOGLCODE'] = $asset['DISPOGLCODE'];
                $dpDetail['DISPOGLCODESystemID'] = $asset['dispglCodeSystemID'];

                $disposalDetail = Models\AssetDisposalDetail::create($dpDetail);
                if ($disposalDetail) {
                    $asset->DIPOSED = -1;
                    $asset->disposedDate = NOW();
                    $asset->assetdisposalMasterAutoID = $output['assetdisposalMasterAutoID'];
                    $asset->save();

                    $params = array('autoID' => $output['assetdisposalMasterAutoID'], 'company' => $fixedCapital['companySystemID'], 'document' => 41, 'segment' => '', 'category' => '', 'amount' => 0);
                    $assetDisposalDetail = self::confirmWithoutRuleDocument($params);
                }
            }

            $capitalizeDetail = Models\AssetCapitalizationDetail::where('capitalizationID', $masterData['autoID'])->get();
            if ($capitalizeDetail) {
                foreach ($capitalizeDetail as $val) {
                    $lastSerialNumber = 1;
                    $lastSerial = Models\FixedAssetMaster::selectRaw('MAX(serialNo) as serialNo')->first();
                    if ($lastSerial) {
                        $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                    }

                    $asset = Models\FixedAssetMaster::find($val['faID']);
                    $disposalDate = Carbon::parse($fixedCapital->documentDate);
                    $sod = Carbon::parse($asset->dateDEP);
                    $diffDays = $disposalDate->diffInDays($sod);
                    $noYears = $diffDays / 365;
                    $remainingLife = $asset->depMonth - $noYears;
                    $DEPpercentage = 100 / $remainingLife;

                    $data = $asset->toArray();
                    $documentCode = ($val["companyID"] . '\\FA' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));
                    $data["docOriginDocumentSystemID"] = $fixedCapital['documentSystemID'];
                    $data["docOriginDocumentID"] = $fixedCapital['documentID'];
                    $data["docOriginSystemCode"] = $masterData['autoID'];
                    $data["docOrigin"] = $fixedCapital['capitalizationCode'];
                    $data["docOriginDetailID"] = $val["capitalizationDetailID"];
                    $data["serialNo"] = $lastSerialNumber;
                    $data["itemCode"] = $asset["itemSystemCode"];
                    $data["faCode"] = $documentCode;
                    $data["assetDescription"] = 'Allocation of Logistics from ' . $output['disposalDocumentCode'] . ' related to ' . $fixedCapital['capitalizationCode'];
                    $data["dateAQ"] = $fixedCapital['documentDate'];
                    $data["dateDEP"] = $fixedCapital['documentDate'];
                    $data["depMonth"] = $remainingLife;
                    $data["DEPpercentage"] = $DEPpercentage;
                    $data["groupTO"] = $val['faID'];
                    $data["COSTUNIT"] = $val["allocatedAmountLocal"];
                    $data["costUnitRpt"] = $val["allocatedAmountRpt"];
                    $data["RollLevForApp_curr"] = 1;
                    $data["confirmedYN"] = 0;
                    $data["confirmedByEmpSystemID"] = null;
                    $data["confirmedByEmpID"] = null;
                    $data["confirmedDate"] = null;
                    $data["approved"] = 0;
                    $data["assetType"] = 1;
                    $data["approvedDate"] = null;
                    $data["approvedByUserID"] = null;
                    $data["approvedByUserSystemID"] = null;
                    $data["selectedforJobYN"] = null;
                    $data["lastVerifiedDate"] = null;
                    $data["timesReferred"] = 0;
                    $data["refferedBackYN"] = 0;
                    $data['createdPcID'] = gethostname();
                    $data['createdUserID'] = \Helper::getEmployeeID();
                    $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                    $data['createdDateAndTime'] = date('Y-m-d H:i:s');
                    $data["modifiedUser"] = null;
                    $data["modifiedUserSystemID"] = null;
                    $data["modifiedPc"] = null;
                    $data["selectedForDisposal"] = 0;
                    $data["DIPOSED"] = 0;
                    $data["disposedDate"] = null;
                    $data["tempRecord"] = null;
                    $data["toolsCondition"] = null;

                    $fixedAsset = Models\FixedAssetMaster::create($data);

                    if ($fixedAsset) {
                        $params = array('autoID' => $fixedAsset['faID'], 'company' => $val["companySystemID"], 'document' => 22, 'segment' => '', 'category' => '', 'amount' => 0);
                        $assetConfirm = self::confirmWithoutRuleDocument($params);
                    }
                }
            }
        }
    }

    public static function generateAccrualJournalVoucher($masterData)
    {
        $jvMasterData = Models\JvMaster::find($masterData);

        if ($jvMasterData->jvType == 1) {

            $lastSerial = Models\JvMaster::where('companySystemID', $jvMasterData->companySystemID)
                ->where('companyFinanceYearID', $jvMasterData->companyFinanceYearID)
                ->orderBy('jvMasterAutoId', 'desc')
                ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            $firstDayNextMonth = date('Y-m-d', strtotime('first day of next month'));

            $companyfinanceyear = Models\CompanyFinanceYear::where('companyFinanceYearID', $jvMasterData->companyFinanceYearID)
                ->where('companySystemID', $jvMasterData->companySystemID)
                ->first();

            if ($companyfinanceyear) {
                $startYear = $companyfinanceyear->bigginingDate;
                $finYearExp = explode('-', $startYear);
                $finYear = $finYearExp[0];
            } else {
                $finYear = date("Y");
            }

            $jvCode = ($jvMasterData->companyID . '\\' . $finYear . '\\' . $jvMasterData->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

            $postJv = $jvMasterData->toArray();
            $postJv['JVcode'] = $jvCode;
            $postJv['serialNo'] = $lastSerialNumber;
            $postJv['JVdate'] = $firstDayNextMonth;
            $postJv['JVNarration'] = 'Reversal of Revenue Accrual for the month of ' . date('F Y') . '';
            $postJv['confirmedYN'] = 0;
            $postJv['confirmedByEmpSystemID'] = '';
            $postJv['confirmedByEmpID'] = '';
            $postJv['confirmedByName'] = '';
            $postJv['confirmedDate'] = null;
            $postJv['approved'] = 0;
            $postJv['approvedDate'] = null;
            $postJv['approvedByUserID'] = '';
            $postJv['approvedByUserSystemID'] = '';
            $postJv['RollLevForApp_curr'] = 1;

            $storeJV = Models\JvMaster::create($postJv);

            //inserting to jv detail
            $fetchJVDetail = Models\JvDetail::where('jvMasterAutoId', $masterData)->get();

            if (!empty($fetchJVDetail)) {
                foreach ($fetchJVDetail as $key => $val) {
                    $testDebitAmount = $fetchJVDetail[$key]['debitAmount'];
                    $testCreditAmount = $fetchJVDetail[$key]['creditAmount'];
                    $fetchJVDetail[$key]['jvMasterAutoId'] = $storeJV->jvMasterAutoId;
                    $fetchJVDetail[$key]['debitAmount'] = $testCreditAmount;
                    $fetchJVDetail[$key]['creditAmount'] = $testDebitAmount;
                    unset($fetchJVDetail[$key]['jvDetailAutoID']);
                }
            }

            $jvDetailArray = $fetchJVDetail->toArray();

            $storeJvDetail = Models\JvDetail::insert($jvDetailArray);
        }
    }

    public static function generatePOAccrualJournalVoucher($masterData)
    {
        $jvMasterData = Models\JvMaster::find($masterData);

        if ($jvMasterData->jvType == 5) {

            $lastSerial = Models\JvMaster::where('companySystemID', $jvMasterData->companySystemID)
                ->where('companyFinanceYearID', $jvMasterData->companyFinanceYearID)
                ->orderBy('jvMasterAutoId', 'desc')
                ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            $firstDayNextMonth = date('Y-m-d', strtotime('first day of next month'));

            $companyfinanceyear = Models\CompanyFinanceYear::where('companyFinanceYearID', $jvMasterData->companyFinanceYearID)
                ->where('companySystemID', $jvMasterData->companySystemID)
                ->first();

            if ($companyfinanceyear) {
                $startYear = $companyfinanceyear->bigginingDate;
                $finYearExp = explode('-', $startYear);
                $finYear = $finYearExp[0];
            } else {
                $finYear = date("Y");
            }

            $jvCode = ($jvMasterData->companyID . '\\' . $finYear . '\\' . $jvMasterData->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

            $postJv = $jvMasterData->toArray();
            $postJv['JVcode'] = $jvCode;
            $postJv['serialNo'] = $lastSerialNumber;
            $postJv['JVdate'] = $firstDayNextMonth;
            $postJv['JVNarration'] = 'Reversal of PO accrual for the month of ' . date('F Y') . '';
            $postJv['confirmedYN'] = 0;
            $postJv['confirmedByEmpSystemID'] = '';
            $postJv['confirmedByEmpID'] = '';
            $postJv['confirmedByName'] = '';
            $postJv['confirmedDate'] = null;
            $postJv['approved'] = 0;
            $postJv['approvedDate'] = null;
            $postJv['approvedByUserID'] = '';
            $postJv['approvedByUserSystemID'] = '';
            $postJv['RollLevForApp_curr'] = 1;

            $storeJV = Models\JvMaster::create($postJv);

            //inserting to jv detail
            $fetchJVDetail = Models\JvDetail::where('jvMasterAutoId', $masterData)->get();

            if (!empty($fetchJVDetail)) {
                foreach ($fetchJVDetail as $key => $val) {
                    $testDebitAmount = $fetchJVDetail[$key]['debitAmount'];
                    $testCreditAmount = $fetchJVDetail[$key]['creditAmount'];
                    $fetchJVDetail[$key]['jvMasterAutoId'] = $storeJV->jvMasterAutoId;
                    $fetchJVDetail[$key]['debitAmount'] = $testCreditAmount;
                    $fetchJVDetail[$key]['creditAmount'] = $testDebitAmount;
                    unset($fetchJVDetail[$key]['jvDetailAutoID']);
                }
            }

            $jvDetailArray = $fetchJVDetail->toArray();

            $storeJvDetail = Models\JvDetail::insert($jvDetailArray);
        }
    }

    public static function generateCustomerReceiptVoucher($pvMaster)
    {
        Log::useFiles(storage_path() . '/logs/create_receipt_voucher_jobs.log');
        if ($pvMaster->invoiceType == 3) {
            Log::info('started');
            Log::info($pvMaster->PayMasterAutoId);
            $dpdetails = Models\DirectPaymentDetails::where('directPaymentAutoID', $pvMaster->PayMasterAutoId)->get();
            if (count($dpdetails) > 0) {
                if ($pvMaster->expenseClaimOrPettyCash == 6 || $pvMaster->expenseClaimOrPettyCash == 7) {
                    $company = Models\Company::find($pvMaster->interCompanyToSystemID);
                    $receivePayment['companySystemID'] = $pvMaster->interCompanyToSystemID;
                    $receivePayment['companyID'] = $company->CompanyID;
                    $receivePayment['documentSystemID'] = 21;
                    $receivePayment['documentID'] = 'BRV';

                    $companyFinanceYear = Models\CompanyFinanceYear::where('companySystemID', $pvMaster->interCompanyToSystemID)->whereRaw('YEAR(bigginingDate) = ?', [date('Y')])->first();

                    $receivePayment['companyFinanceYearID'] = $companyFinanceYear->companyFinanceYearID;
                    $receivePayment['FYBiggin'] = $companyFinanceYear->bigginingDate;
                    $receivePayment['FYEnd'] = $companyFinanceYear->endingDate;

                    $companyFinancePeriod = Models\CompanyFinancePeriod::where('companySystemID', $pvMaster->interCompanyToSystemID)->where('departmentSystemID', 4)->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)->whereRaw('DATE_FORMAT(dateFrom,"%Y-%m") = ?', [date('Y-m')])->first();
                    if ($companyFinancePeriod) {
                        $receivePayment['companyFinancePeriodID'] = $companyFinancePeriod->companyFinancePeriodID;
                        $receivePayment['FYPeriodDateFrom'] = $companyFinancePeriod->dateFrom;
                        $receivePayment['FYPeriodDateTo'] = $companyFinancePeriod->dateTo;
                    }

                    $BRVLastSerial = Models\CustomerReceivePayment::where('companySystemID', $pvMaster->interCompanyToSystemID)
                        ->where('companyFinanceYearID', $companyFinancePeriod->companyFinanceYearID)
                        ->where('documentSystemID', 21)
                        ->where('serialNo', '>', 0)
                        ->orderBy('serialNo', 'desc')
                        ->first();

                    $cusInvLastSerialNumber = 1;
                    if ($BRVLastSerial) {
                        $cusInvLastSerialNumber = intval($BRVLastSerial->serialNo) + 1;
                    }
                    $receivePayment['serialNo'] = $cusInvLastSerialNumber;

                    if ($companyFinanceYear) {
                        $cusStartYear = $companyFinanceYear->bigginingDate;
                        $cusFinYearExp = explode('-', $cusStartYear);
                        $cusFinYear = $cusFinYearExp[0];
                    } else {
                        $cusFinYear = date("Y");
                    }
                    $docCode = ($company->CompanyID . '\\' . $cusFinYear . '\\' . $receivePayment['documentID'] . str_pad($cusInvLastSerialNumber, 6, '0', STR_PAD_LEFT));

                    $receivePayment['custPaymentReceiveCode'] = $docCode;
                    $receivePayment['custPaymentReceiveDate'] = $pvMaster->BPVdate;
                    $receivePayment['narration'] = "Inter Company Transfer from " . $pvMaster->companyID . " to " . $pvMaster->interCompanyToID . "  " . $pvMaster->BPVcode;
                    $receivePayment['intercompanyPaymentID'] = $pvMaster->PayMasterAutoId;
                    $receivePayment['intercompanyPaymentCode'] = $pvMaster->BPVcode;
                    $receivePayment['expenseClaimOrPettyCash'] = $pvMaster->expenseClaimOrPettyCash;

                    $dpdetails2 = Models\DirectPaymentDetails::where('directPaymentAutoID', $pvMaster->PayMasterAutoId)->first();
                    if ($dpdetails2) {
                        $receivePayment['custTransactionCurrencyID'] = $dpdetails2->toBankCurrencyID;
                        $receivePayment['custTransactionCurrencyER'] = 1;
                        $receivePayment['localCurrencyID'] = $dpdetails2->toCompanyLocalCurrencyID;
                        $receivePayment['localCurrencyER'] = $dpdetails2->toCompanyLocalCurrencyER;
                        $receivePayment['companyRptCurrencyID'] = $dpdetails2->toCompanyRptCurrencyID;
                        $receivePayment['companyRptCurrencyER'] = $dpdetails2->toCompanyRptCurrencyER;
                        $receivePayment['bankAmount'] = ABS($dpdetails2->toBankAmount) * -1;
                        $receivePayment['receivedAmount'] = ABS($dpdetails2->toBankAmount) * -1;
                        $receivePayment['localAmount'] = ABS($dpdetails2->toCompanyLocalCurrencyAmount) * -1;
                        $receivePayment['companyRptAmount'] = ABS($dpdetails2->toCompanyRptCurrencyAmount) * -1;
                        $receivePayment['bankID'] = $dpdetails2->toBankID;
                        $receivePayment['bankAccount'] = $dpdetails2->toBankAccountID;
                        $receivePayment['bankCurrency'] = $dpdetails2->toBankCurrencyID;
                        $receivePayment['bankCurrencyER'] = 1;
                    }

                    $receivePayment['documentType'] = 14;
                    $receivePayment['createdUserSystemID'] = $pvMaster->confirmedByEmpSystemID;
                    $receivePayment['createdUserID'] = $pvMaster->confirmedByEmpID;
                    $receivePayment['createdPcID'] = gethostname();

                    Log::info($receivePayment);

                    $custRecMaster = Models\CustomerReceivePayment::create($receivePayment);

                    if ($custRecMaster) {
                        foreach ($dpdetails as $val) {
                            $chartofAccount = Models\ChartOfAccount::where('interCompanySystemID', $pvMaster->companySystemID)->first();
                            $receivePaymentDetail['directReceiptAutoID'] = $custRecMaster->custReceivePaymentAutoID;
                            $receivePaymentDetail['companySystemID'] = $pvMaster->interCompanyToSystemID;
                            $receivePaymentDetail['companyID'] = $company->CompanyID;

                            $serviceLine = Models\SegmentMaster::ofCompany([$pvMaster->interCompanyToSystemID])->isPublic()->first();
                            if ($serviceLine) {
                                $receivePaymentDetail['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
                                $receivePaymentDetail['serviceLineCode'] = $serviceLine->ServiceLineCode;
                            }

                            $receivePaymentDetail['chartOfAccountSystemID'] = $chartofAccount->chartOfAccountSystemID;
                            $receivePaymentDetail['glCode'] = $chartofAccount->AccountCode;
                            $receivePaymentDetail['glCodeDes'] = $chartofAccount->AccountDescription;
                            $receivePaymentDetail['contractID'] = null;
                            $receivePaymentDetail['comments'] = $pvMaster->BPVNarration;
                            $receivePaymentDetail['DRAmountCurrency'] = $val->toBankCurrencyID;
                            $receivePaymentDetail['DDRAmountCurrencyER'] = 1;
                            $receivePaymentDetail['DRAmount'] = $val->toBankAmount;
                            $receivePaymentDetail['localCurrency'] = $val->toCompanyLocalCurrencyID;
                            $receivePaymentDetail['localCurrencyER'] = $val->toCompanyLocalCurrencyER;
                            $receivePaymentDetail['localAmount'] = $val->toCompanyLocalCurrencyAmount;
                            $receivePaymentDetail['comRptCurrency'] = $val->toCompanyRptCurrencyID;
                            $receivePaymentDetail['comRptCurrencyER'] = $val->toCompanyRptCurrencyER;
                            $receivePaymentDetail['comRptAmount'] = $val->toCompanyRptCurrencyAmount;
                            Log::info($receivePaymentDetail);
                            $custRecDetail = Models\DirectReceiptDetail::create($receivePaymentDetail);
                        }

                        $params = array('autoID' => $custRecMaster->custReceivePaymentAutoID, 'company' => $pvMaster->interCompanyToSystemID, 'document' => 21, 'segment' => '', 'category' => '', 'amount' => 0);
                        $confirm = self::confirmWithoutRuleDocument($params);
                        Log::info($confirm["message"]);
                    }
                } else {
                    $dpdetails = Models\DirectPaymentDetails::where('directPaymentAutoID', $pvMaster->PayMasterAutoId)->where('glCodeIsBank', 1)->get();
                    if (count($dpdetails) > 0) {
                        foreach ($dpdetails as $val) {
                            $receivePayment['companySystemID'] = $pvMaster->companySystemID;
                            $receivePayment['companyID'] = $pvMaster->companyID;
                            $receivePayment['documentSystemID'] = $pvMaster->documentSystemID;
                            $receivePayment['documentID'] = $pvMaster->documentID;

                            $companyFinanceYear = Models\CompanyFinanceYear::where('companySystemID', $pvMaster->companySystemID)->whereRaw('YEAR(bigginingDate) = ?', [date('Y')])->first();

                            $receivePayment['companyFinanceYearID'] = $companyFinanceYear->companyFinanceYearID;
                            $receivePayment['FYBiggin'] = $companyFinanceYear->bigginingDate;
                            $receivePayment['FYEnd'] = $companyFinanceYear->endingDate;

                            $companyFinancePeriod = Models\CompanyFinancePeriod::where('companySystemID', $pvMaster->companySystemID)->where('departmentSystemID', 4)->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)->whereRaw('DATE_FORMAT(dateFrom,"%Y-%m") = ?', [date('Y-m')])->first();
                            if ($companyFinancePeriod) {
                                $receivePayment['companyFinancePeriodID'] = $companyFinancePeriod->companyFinancePeriodID;
                                $receivePayment['FYPeriodDateFrom'] = $companyFinancePeriod->dateFrom;
                                $receivePayment['FYPeriodDateTo'] = $companyFinancePeriod->dateTo;
                            }
                            $receivePayment['PayMasterAutoId'] = $pvMaster->PayMasterAutoId;
                            $receivePayment['serialNo'] = $pvMaster->serialNo;
                            $receivePayment['custPaymentReceiveCode'] = $pvMaster->BPVcode;
                            $receivePayment['custPaymentReceiveDate'] = $pvMaster->BPVdate;
                            $receivePayment['narration'] = $pvMaster->BPVNarration;

                            $receivePayment['custTransactionCurrencyID'] = $val->bankCurrencyID;
                            $receivePayment['custTransactionCurrencyER'] = 1;

                            $account = Models\BankAccount::where('chartOfAccountSystemID', $val->chartOfAccountSystemID)->where('companySystemID', $pvMaster->companySystemID)->first();

                            $receivePayment['bankID'] = $account->bankmasterAutoID;
                            $receivePayment['bankAccount'] = $account->bankAccountAutoID;
                            $receivePayment['bankCurrency'] = $val->bankCurrencyID;
                            $receivePayment['bankCurrencyER'] = 1;

                            $companyCurrencyConversion = \Helper::currencyConversion($pvMaster->companySystemID, $val->bankCurrencyID, $val->bankCurrencyID, $val->bankAmount);

                            $receivePayment['localCurrencyID'] = $val->localCurrency;
                            $receivePayment['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                            $receivePayment['companyRptCurrencyID'] = $val->comRptCurrency;
                            $receivePayment['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                            $receivePayment['bankAmount'] = $val->bankAmount;
                            $receivePayment['localAmount'] = \Helper::roundValue($companyCurrencyConversion['localAmount']);
                            $receivePayment['companyRptAmount'] = \Helper::roundValue($companyCurrencyConversion['reportingAmount']);
                            $receivePayment['receivedAmount'] = $val->bankAmount;

                            $receivePayment['confirmedYN'] = 1;
                            $receivePayment['confirmedByEmpSystemID'] = $pvMaster->confirmedByEmpSystemID;
                            $receivePayment['confirmedByEmpID'] = $pvMaster->confirmedByEmpID;;
                            $receivePayment['confirmedByName'] = $pvMaster->confirmedByName;;
                            $receivePayment['confirmedDate'] = NOW();
                            $receivePayment['approved'] = -1;
                            $receivePayment['approvedDate'] = NOW();
                            $receivePayment['postedDate'] = NOW();
                            $receivePayment['createdUserSystemID'] = $pvMaster->confirmedByEmpSystemID;
                            $receivePayment['createdUserID'] = $pvMaster->confirmedByEmpID;
                            $receivePayment['createdPcID'] = gethostname();

                            $custRecMaster = Models\CustomerReceivePayment::create($receivePayment);
                            Log::info($receivePayment);
                        }
                    }
                }
            }
            Log::info('Successfully inserted to Customer receive voucher ' . date('H:i:s'));
            $masterData = ['documentSystemID' => $pvMaster->documentSystemID, 'autoID' => $pvMaster->PayMasterAutoId, 'companySystemID' => $pvMaster->companySystemID, 'employeeSystemID' => $pvMaster->confirmedByEmpSystemID];
            $jobPV = BankLedgerInsert::dispatch($masterData);
        }
    }


    /**
     * A common function to confirm document with approval creation
     * @param $params : accept parameters as an array
     * $param 1-documentSystemID : autoID
     * $param 2-company : company
     * $param 3-document : document
     * $param 4-segment : segment
     * $param 5-category : category
     * $param 6-amount : amount
     * no return values
     */
    public static function confirmWithoutRuleDocument($params)
    {
        /** check document is already confirmed*/
        if (!array_key_exists('autoID', $params)) {
            return ['success' => false, 'message' => 'Parameter documentSystemID is missing'];
        }

        if (!array_key_exists('company', $params)) {
            return ['success' => false, 'message' => 'Parameter company is missing'];
        }

        if (!array_key_exists('document', $params)) {
            return ['success' => false, 'message' => 'Parameter document is missing'];
        }


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
            default:
                return ['success' => false, 'message' => 'Document ID not found'];
        }

        $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
        $masterRec = $namespacedModel::find($params["autoID"]);

        $document = Models\DocumentMaster::where('documentSystemID', $params["document"])->first();
        if ($document) {
            // get current employee detail
            $empInfo = self::getEmployeeInfo();

            // get approval rolls
            $approvalLevel = Models\ApprovalLevel::with('approvalrole')->where('companySystemID', $params["company"])->where('documentSystemID', $params["document"])->where('departmentSystemID', $document["departmentSystemID"])->where('isActive', -1);
            $output = $approvalLevel->first();


            if ($output) {
                /** get source document master record*/
                $sorceDocument = $namespacedModel::find($params["autoID"]);
                $documentApproved = [];
                if ($output) {
                    if ($output->approvalrole) {
                        foreach ($output->approvalrole as $val) {
                            if ($val->approvalGroupID) {
                                $documentApproved[] = array('companySystemID' => $val->companySystemID, 'companyID' => $val->companyID, 'departmentSystemID' => $val->departmentSystemID, 'departmentID' => $val->departmentID, 'serviceLineSystemID' => $val->serviceLineSystemID, 'serviceLineCode' => $val->serviceLineID, 'documentSystemID' => $val->documentSystemID, 'documentID' => $val->documentID, 'documentSystemCode' => $params["autoID"], 'documentCode' => $sorceDocument[$docInforArr["documentCodeColumnName"]], 'approvalLevelID' => $val->approvalLevelID, 'rollID' => $val->rollMasterID, 'approvalGroupID' => $val->approvalGroupID, 'rollLevelOrder' => $val->rollLevel, 'docConfirmedDate' => now(), 'docConfirmedByEmpSystemID' => $empInfo->employeeSystemID, 'docConfirmedByEmpID' => $empInfo->empID);
                            }
                        }
                    }
                }
                if (count($documentApproved) > 0) {
                    // insert rolls to document approved table
                    $isDocumentApproved = Models\DocumentApproved::insert($documentApproved);
                    if ($isDocumentApproved) {
                        //confirm the document
                        $masterRec->update([$docInforArr["confirmColumnName"] => 1, $docInforArr["confirmedBy"] => $empInfo->empName, $docInforArr["confirmedByEmpID"] => $empInfo->empID, $docInforArr["confirmedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["confirmedDate"] => now(), 'RollLevForApp_curr' => 1]);
                    }
                }
            }
        }
    }

    public static function appendToBankLedger($autoID)
    {
        $custReceivePayment = Models\CustomerReceivePayment::with('finance_period_by')->find($autoID);
        if ($custReceivePayment) {
            $masterDocumentDate = date('Y-m-d H:i:s');
            if ($custReceivePayment->finance_period_by->isActive == -1) {
                $masterDocumentDate = $custReceivePayment->custPaymentReceiveDate;
            }
            $data['companySystemID'] = $custReceivePayment->companySystemID;
            $data['companyID'] = $custReceivePayment->companyID;
            $data['documentSystemID'] = $custReceivePayment->documentSystemID;
            $data['documentID'] = $custReceivePayment->documentID;
            $data['documentSystemCode'] = $custReceivePayment->custReceivePaymentAutoID;
            $data['documentCode'] = $custReceivePayment->custPaymentReceiveCode;
            $data['documentDate'] = $custReceivePayment->custPaymentReceiveDate;
            $data['postedDate'] = $masterDocumentDate;
            $data['documentNarration'] = $custReceivePayment->narration;
            $data['bankID'] = $custReceivePayment->bankID;
            $data['bankAccountID'] = $custReceivePayment->bankAccount;
            $data['bankCurrency'] = $custReceivePayment->bankCurrency;
            $data['bankCurrencyER'] = $custReceivePayment->bankCurrencyER;
            $data['documentChequeNo'] = $custReceivePayment->custChequeNo;
            $data['documentChequeDate'] = $custReceivePayment->custChequeDate;
            $data['payeeID'] = $custReceivePayment->customerID;

            $payee = Models\CustomerMaster::find($custReceivePayment->customerID);
            if ($payee) {
                $data['payeeCode'] = $payee->CutomerCode;
            }
            $data['payeeName'] = $custReceivePayment->PayeeName;
            $data['payeeGLCodeID'] = $custReceivePayment->customerGLCodeSystemID;
            $data['payeeGLCode'] = $custReceivePayment->customerGLCode;
            $data['supplierTransCurrencyID'] = $custReceivePayment->custTransactionCurrencyID;
            $data['supplierTransCurrencyER'] = $custReceivePayment->custTransactionCurrencyER;
            $data['localCurrencyID'] = $custReceivePayment->localCurrencyID;
            $data['localCurrencyER'] = $custReceivePayment->localCurrencyER;
            $data['companyRptCurrencyID'] = $custReceivePayment->companyRptCurrencyID;
            $data['companyRptCurrencyER'] = $custReceivePayment->companyRptCurrencyER;
            $data['payAmountBank'] = $custReceivePayment->bankAmount;
            $data['payAmountSuppTrans'] = $custReceivePayment->bankAmount;
            $data['payAmountCompLocal'] = $custReceivePayment->localAmount;
            $data['payAmountCompRpt'] = $custReceivePayment->companyRptAmount;
            $data['invoiceType'] = $custReceivePayment->documentType;
            $data['chequePaymentYN'] = -1;

            if ($custReceivePayment->trsCollectedYN == 0) {
                $data['trsCollectedYN'] = -1;
            } else {
                $data['trsCollectedYN'] = $custReceivePayment->trsCollectedYN;
            }

            $data['trsCollectedByEmpSystemID'] = $custReceivePayment->trsCollectedByEmpSystemID;
            $data['trsCollectedByEmpID'] = $custReceivePayment->trsCollectedByEmpID;
            $data['trsCollectedByEmpName'] = $custReceivePayment->trsCollectedByEmpName;
            $data['trsCollectedDate'] = $custReceivePayment->trsCollectedDate;

            $data['createdUserID'] = $custReceivePayment->createdUserID;
            $data['createdUserSystemID'] = $custReceivePayment->createdUserSystemID;
            $data['createdPcID'] = gethostname();
            $data['timestamp'] = NOW();
            Models\BankLedger::create($data);
        }
    }
}
