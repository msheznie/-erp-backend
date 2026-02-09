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

use App\Events\UnverifiedEmailEvent;
use App\helper\IvmsDeliveryOrderService;
use App\Jobs\BankLedgerInsert;
use App\Jobs\BudgetAdjustment;
use App\Jobs\CreateCustomerInvoice;
use App\Jobs\CreateGRVSupplierInvoice;
use App\Jobs\CreateRecurringVoucherSetupSchedules;
use App\Jobs\CreateStockReceive;
use App\Jobs\CreateSupplierInvoice;
use App\Jobs\CreateSupplierTransactions;
use App\Jobs\EliminationLedgerInsert;
use App\Jobs\GeneralLedgerInsert;
use App\Jobs\ItemLedgerInsert;
use App\Jobs\PushNotification;
use App\Jobs\SendEmail;
use App\Jobs\UnbilledGRVInsert;
use App\Jobs\WarehouseItemUpdate;
use App\Jobs\CreateConsoleJV;
use App\Models;
use App\Models\AssetVerificationDetail;
use App\Models\ChartOfAccount;
use App\Models\DeliveryOrderDetail;
use App\Models\InterCompanyAssetDisposal;
use App\Models\FixedAssetMaster;
use App\Models\Alert;
use App\Models\ERPAssetTransferDetail;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerMaster;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DocumentRestrictionAssign;
use App\Models\EmployeeNavigation;
use App\Models\GRVDetails;
use App\Models\TenderCircularsEditLog;
use App\Models\CircularAmendmentsEditLog;
use App\Models\CircularSuppliersEditLog;
use App\Models\PaymentTermConfig;
use App\Models\PaymentTermTemplate;
use App\Models\PaymentTermTemplateAssigned;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\GRVMaster;
use App\Models\ProcumentOrder;
use App\Models\Employee;
use App\Models\BookInvSuppMaster;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequestDetails;
use App\Models\PurchaseReturnDetails;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\RecurringVoucherSetup;
use App\Models\ReportTemplateDetails;
use App\Models\SalesReturnDetail;
use App\Models\SalesReturn;
use App\Models\DeliveryOrder;
use App\Models\SupplierInvoiceItemDetail;
use App\Models\SupplierMaster;
use App\Models\SystemConfigurationAttributes;
use App\Models\TaxVatCategories;
use App\Models\TenderMaster;
use App\Models\User;
use App\Models\DebitNote;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SupplierRegistrationLink;
use App\Services\ChartOfAccountValidationService;
use App\Services\UserTypeService;
use App\Services\DocumentAutoApproveService;
use App\Services\DocumentReportingManagerService;
use App\Traits\ApproveRejectTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use App\Utils\ResponseUtil;
use App\helper\CurrencyValidation;
use App\helper\BlockInvoice;
use App\helper\SupplierRegister;
use App\helper\SupplierAssignService;
use App\helper\BudgetReviewService;
use App\helper\StockCountService;
use App\helper\AssetTransferService;
use App\helper\BudgetHistoryService;
use App\helper\CustomerAssignService;
use App\helper\BudgetConsumptionService;
use App\helper\ChartOfAccountDependency;
use App\helper\CurrencyConversionService;
use App\Jobs\BudgetAdditionAdjustment;
use App\helper\SendEmailForDocument;
use App\helper\HrMonthlyDeductionService;
use Illuminate\Support\Facades\Schema;
use Response;
use App\Models\CompanyFinanceYear;
use App\Jobs\CreateAccumulatedDepreciation;
use App\Services\WebPushNotificationService;
use App\Services\GeneralLedger\GlPostedDateService;
use App\Models\TenderCirculars;
use App\Models\CircularAmendments;
use App\Models\CircularSuppliers;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailForQueuing;
use App\Models\DocumentModifyRequest;
use App\helper\TenderDetails;
use App\helper\email as Email;
use App\Models\BirthdayTemplate;

use App\Models\DirectInvoiceDetails;
use App\Models\BookInvSuppDet;
use App\Models\SupplierInvoiceDirectItem;
use App\Models\CurrencyMaster;
use App\helper\CreateCustomerThirdPartyInvoice;
use App\Models\DocumentAttachments;
use App\Models\SRMSupplierValues;
use App\Models\SupplierBlock;
use App\Models\TenderSupplierAssignee;
use App\helper\ExchangeSetupConfig;
use App\Services\AssignedServices\SegmentAssignedService;
use App\helper\Helper;

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

    public static function validateJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    public static function handleErrorData($inputData) {

        $errorMessage = $inputData;

        if (self::validateJson($inputData)) {
            $errorMessage = $inputData;

            $arrayData = json_decode($inputData, true);

            foreach ($arrayData as $key => $value) {
                if (is_array($value)) {
                    $errorMessage = $value[0];
                } else {
                    $errorMessage = $value;
                }

            }
        }

        return $errorMessage;
    }

    public static function getCompanyServicelineWithMaster($company)
    {
        $companiesByGroup = "";
        if (self::checkIsCompanyGroup($company)) {
            $companiesByGroup = self::getGroupCompany($company);
        } else {
            $companiesByGroup = (array)$company;
        }

        $serviceline = DB::table('serviceline')->selectRaw('serviceline.companySystemID,serviceline.serviceLineSystemID,serviceline.ServiceLineCode,serviceline.serviceLineMasterCode,CONCAT_WS(" - ",case when serviceline.masterID IS NULL then serviceline.ServiceLineCode else parents.ServiceLineCode end,serviceline.ServiceLineDes) as ServiceLineDes')
            ->leftJoin('serviceline as parents', 'serviceline.masterID', '=', 'parents.serviceLineSystemID')
            ->leftJoin('service_line_assigned', 'serviceline.serviceLineSystemID', '=', 'service_line_assigned.serviceLineSystemID')
            ->where('serviceline.approved_yn', 1)
            ->whereIn('service_line_assigned.companySystemID', $companiesByGroup)
            ->where('service_line_assigned.isActive', 1)
            ->where('service_line_assigned.isAssigned', 1)
            ->whereIN('serviceline.companySystemID', $companiesByGroup)
            ->where('serviceline.isFinalLevel', 1)
            ->where('serviceline.isDeleted', 0)
            ->get();
        return $serviceline;
    }



    /**
     * Get all companies related to a group
     * @param $selectedCompanyId - current company id
     * @return array
     */

    public static function checkDomai()
    {

        $redirectUrl =  env("ERP_APPROVE_URL"); //ex: change url to https://*.pl.uat-gears-int.com/#/approval/erp

        if (env('IS_MULTI_TENANCY') == true) {
            if (isset($_SERVER['HTTP_HOST'])) {
                $url = $_SERVER['HTTP_HOST'];
                $url_array = explode('.', $url);
                $subDomain = $url_array[0];

                $tenantDomain = (isset(explode('-', $subDomain)[0])) ? explode('-', $subDomain)[0] : "";

                $search = '*';
                $redirectUrl = str_replace($search, $tenantDomain, $redirectUrl);
            }
        }

        return $redirectUrl;
    }

    public static function getGroupCompany($selectedCompanyId, $excludeSameCompany = false)
    {
        $companiesByGroup = Models\Company::with(['child' => function($q) use($selectedCompanyId,$excludeSameCompany){
            if($excludeSameCompany){
                $q->where("companySystemID",'!=', $selectedCompanyId);
            }
        }])
            ->where("masterCompanySystemIDReorting", $selectedCompanyId)
            ->get();

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

    public static function getEmployeeInfoForApi()
    {
        $employee = Models\Employee::with(['profilepic', 'user_data' => function($query) {
            $query->select('uuid', 'employee_id');
        }])->find(11);
        return $employee;
    }


    public static function conversionCurrencyByER(
        $transactionCurrencyID,
        $convertCurrencyID,
        $transactionAmount,
        $convertCurrencyER
    ) {
        $transactionAmount = self::roundValue($transactionAmount);
        if ($transactionCurrencyID == $convertCurrencyID) {
            $finalAmount = $transactionAmount;
        } else {
            $finalAmount = $transactionAmount / $convertCurrencyER;
        }

        return $finalAmount;
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
    public static function currencyConversion($companySystemID, $transactionCurrencyID, $documentCurrencyID, $transactionAmount, $bankAccountAutoID = null,$roundOff = false)
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
        $transactionAmount = self::formatNumberWithPrecision($transactionAmount);

        // get company local and reporting currency conversion
        if ($companySystemID && $transactionCurrencyID) {
            $companyCurrency = Models\Company::find($companySystemID);
            if ($companyCurrency) {
                $locaCurrencyID = $companyCurrency->localCurrencyID;
                $reportingCurrencyID = $companyCurrency->reportingCurrency;
                $conversion = Models\CurrencyConversion::where('masterCurrencyID', $transactionCurrencyID)->where('subCurrencyID', $locaCurrencyID)->first();

                $trasToLocER = $roundOff ? self::roundValue($conversion->conversion) : $conversion->conversion;
                $conversion = Models\CurrencyConversion::where('masterCurrencyID', $transactionCurrencyID)->where('subCurrencyID', $reportingCurrencyID)->first();
                $trasToRptER = $roundOff ? self::roundValue($conversion->conversion) : $conversion->conversion;

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


                        if ($trasToRptER > 1) {
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
                        if ($trasToLocER > 1) {
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
                        if ($transToBankER > 1) {
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
                    if ($transToDocER > 1) {
                        $documentAmount = $transactionAmount * $transToDocER;
                    } else {
                        $documentAmount = $transactionAmount / $transToDocER;
                    }
                }
            }
        }
        $array = array(
            'trasToLocER' => $trasToLocER,
            'trasToRptER' => $trasToRptER,
            'transToBankER' => $transToBankER,
            'transToDocER' => $transToDocER,
            'reportingAmount' => $reportingAmount,
            'localAmount' => $localAmount,
            'documentAmount' => $documentAmount,
            'bankAmount' => $bankAmount
        );

        return $array;
    }


    /**
     * function to prompt posted date in final approval
     * @param $input - get line records
     * @return mixed
     */
    public static function postedDatePromptInFinalApproval($input)
    {
        $docInforArr = array('tableName' => '', 'modelName' => '', 'primarykey' => '', 'approvedColumnName' => '', 'approvedBy' => '', 'approvedBySystemID' => '', 'approvedDate' => '', 'approveValue' => '', 'confirmedYN' => '', 'confirmedEmpSystemID' => '');
        switch ($input["documentSystemID"]) { // check the document id and set relavant parameters
            case 20: // customer invoice
                $docInforArr["tableName"] = 'erp_custinvoicedirect';
                $docInforArr["modelName"] = 'CustomerInvoiceDirect';
                $docInforArr["primarykey"] = 'custInvoiceDirectAutoID';
                $docInforArr["documentDate"] = "bookingDate";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 19: // credit note
                $docInforArr["tableName"] = 'erp_creditnote';
                $docInforArr["modelName"] = 'CreditNote';
                $docInforArr["primarykey"] = 'creditNoteAutoID';
                $docInforArr["documentDate"] = "creditNoteDate";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 15: // debit note
                $docInforArr["tableName"] = 'erp_debitnote';
                $docInforArr["modelName"] = 'DebitNote';
                $docInforArr["primarykey"] = 'debitNoteAutoID';
                $docInforArr["documentDate"] = "debitNoteDate";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 21: // Receipt voucher
                $docInforArr["tableName"] = 'erp_customerreceivepayment';
                $docInforArr["modelName"] = 'CustomerReceivePayment';
                $docInforArr["primarykey"] = 'custReceivePaymentAutoID';
                $docInforArr["documentDate"] = "custPaymentReceiveDate";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 17: // Journal Voucher
                $docInforArr["tableName"] = 'erp_jvmaster';
                $docInforArr["modelName"] = 'JvMaster';
                $docInforArr["primarykey"] = 'jvMasterAutoId';
                $docInforArr["documentDate"] = "JVdate";
                $docInforArr["financePeriod"] = "financeperiod_by";
                break;
            default:
                return ['success' => true, 'message' => '', 'type' => 5];
        }

        //break this function for the requirment of GCP-515
        return ['success' => true, 'message' => '', 'type' => 5];

        //check document exist
        $docApprove = Models\DocumentApproved::find($input["documentApprovedID"]);

        if (!$docApprove) {
            return ['success' => false, 'message' => trans('custom.no_records_found_caps'), 'type' => 2];
        }

        $empInfo = self::getEmployeeInfo();


        $companyDocument = Models\CompanyDocumentAttachment::where('companySystemID', $docApprove->companySystemID)
                    ->where('documentSystemID', $input["documentSystemID"])
                    ->first();

        if (empty($companyDocument)) {
            return ['success' => false, 'message' => trans('custom.policy_not_found_general')];
        }

        $checkUserHasApprovalAccess = Models\EmployeesDepartment::where('employeeGroupID', $docApprove->approvalGroupID)
                                ->where('companySystemID', $docApprove->companySystemID)
                                ->where('employeeSystemID', $empInfo->employeeSystemID)
                                ->where('documentSystemID', $input["documentSystemID"])
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
            return ['success' => false, 'message' => trans('custom.access_denied')];
        }

        $approvalLevel = Models\ApprovalLevel::find($input["approvalLevelID"]);

        if ($approvalLevel) {
            if ($approvalLevel->noOfLevels == $input["rollLevelOrder"]) { // check for final approval
                $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
                $masterRec = $namespacedModel::with([$docInforArr["financePeriod"]])->find($input["documentSystemCode"]);
                $financePeriod = $docInforArr["financePeriod"];
                $documentDate = $docInforArr["documentDate"];
                if ($masterRec) {
                    if ($masterRec->$financePeriod) {
                        $isActive = $masterRec->$financePeriod->isActive;
                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if ($isActive == -1) {
                            $masterDocumentDate = $masterRec->$documentDate;
                        }

                        // today date for item sales invoice
                        if ($input["documentSystemID"] == 20) {
                            if (isset($masterRec->isPerforma) && ($masterRec->isPerforma == 2 || $masterRec->isPerforma == 4 || $masterRec->isPerforma == 5)) {
                                $masterDocumentDate = date('Y-m-d H:i:s');
                            }
                        }
                        $masterDocumentDate = Carbon::parse($masterDocumentDate);
                        $masterDocumentDate = $masterDocumentDate->format('d/m/Y');
                        return ['success' => true, 'message' => trans('custom.document_will_be_posted_on') . ' ' . $masterDocumentDate . '. ' . trans('custom.are_you_sure_continue'), 'type' => 1];
                    } else {
                        return ['success' => false, 'message' => trans('custom.financial_period_not_found'), 'type' => 3];
                    }
                } else {
                    return ['success' => false, 'message' => trans('custom.no_records_found_caps'), 'type' => 2];
                }
            } else {
                return ['success' => true, 'message' => trans('custom.success'), 'type' => 5];
            }
        } else {
            return ['success' => false, 'message' => trans('custom.no_records_found_caps'), 'type' => 2];
        }
    }


    public static function updateReturnQtyInGrvDetails($masterData)
    {
        $prDetails = PurchaseReturnDetails::where('purhaseReturnAutoID', $masterData['autoID'])->get();

        foreach ($prDetails as $key => $value) {
            $totalQty = PurchaseReturnDetails::selectRaw('SUM(noQty) as totalRtnQty')
                ->where('grvDetailsID', $value->grvDetailsID)
                ->whereHas('master', function ($query) {
                    $query->where('approved', -1);
                })
                ->groupBy('grvDetailsID')
                ->first();

            $updateData = [
                'returnQty' => $totalQty->totalRtnQty
            ];


            $updateRes = GRVDetails::where('grvDetailsID', $value->grvDetailsID)
                ->update($updateData);
        }

        return ['success' => true];
    }

    public static function updateReturnQtyInDeliveryOrderDetails($id)
    {
        $srDetails = SalesReturnDetail::where('salesReturnID', $id)->get();
        $checkSR = SalesReturn::find($id);
        if($checkSR->returnType == 1) {
            foreach ($srDetails as $value) {
                $deliveryOrderData = DeliveryOrderDetail::find($value->deliveryOrderDetailID);

                $checkDO = DeliveryOrder::find($deliveryOrderData->deliveryOrderID);

                if($checkDO->orderType != 1) {

                    $detailExistQODetail = QuotationDetails::find($deliveryOrderData->quotationDetailsID);

                    $returnQty = isset($deliveryOrderData->returnQty) ? $deliveryOrderData->returnQty : 0;
                    $qtyIssuedDefault = isset($deliveryOrderData->qtyIssuedDefaultMeasure) ? $deliveryOrderData->qtyIssuedDefaultMeasure : 0;
                    $doQty = $qtyIssuedDefault - $returnQty;

                    $deliveryOrderData->update(['approvedReturnQty' => $returnQty]);

                    $updatePO = QuotationMaster::find($deliveryOrderData->quotationMasterID)
                        ->update(['closedYN' => 0, 'selectedForDeliveryOrder' => 0]);
                }

            }
        }
        return ['success' => true];
    }

    public static function updateReturnQtyInPoDetails($masterData)
    {
        $prDetails = PurchaseReturnDetails::where('purhaseReturnAutoID', $masterData['autoID'])->get();

        foreach ($prDetails as $key => $value) {
            $grvDetailsData = GRVDetails::with(['grv_master'])->find($value->grvDetailsID);

            if (isset($grvDetailsData->grv_master->grvTypeID) && $grvDetailsData->grv_master->grvTypeID == 2) {
                $detailExistPODetail = PurchaseOrderDetails::find($grvDetailsData->purchaseOrderDetailsID);

                $detailPOSUM = GRVDetails::selectRaw('SUM(noQty - returnQty) as newNoQty')
                    ->whereHas('grv_master', function ($query) {
                        $query->where('grvCancelledYN', '!=', -1);
                    })
                    ->WHERE('purchaseOrderMastertID', $grvDetailsData->purchaseOrderMastertID)
                    ->WHERE('purchaseOrderDetailsID', $grvDetailsData->purchaseOrderDetailsID)
                    ->first();

                // get the total received qty
                $masterPOSUM = GRVDetails::selectRaw('SUM(noQty - returnQty) as newNoQty')
                    ->whereHas('grv_master', function ($query) {
                        $query->where('grvCancelledYN', '!=', -1);
                    })
                    ->WHERE('purchaseOrderMastertID', $grvDetailsData->purchaseOrderMastertID)
                    ->first();

                $receivedQty = 0;
                $goodsRecievedYN = 0;
                $GRVSelectedYN = 0;
                if ($detailPOSUM->newNoQty > 0) {
                    $receivedQty = $detailPOSUM->newNoQty;
                }

                $checkQuantity = $detailExistPODetail->noQty - $receivedQty;
                if ($receivedQty == 0) {
                    $goodsRecievedYN = 0;
                    $GRVSelectedYN = 0;
                } else {
                    if ($checkQuantity == 0) {
                        $goodsRecievedYN = 2;
                        $GRVSelectedYN = 1;
                    } else {
                        $goodsRecievedYN = 1;
                        $GRVSelectedYN = 0;
                    }
                }

                $updateDetail = PurchaseOrderDetails::where('purchaseOrderDetailsID', $detailExistPODetail->purchaseOrderDetailsID)
                    ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $receivedQty]);

                $balanceQty = PurchaseOrderDetails::selectRaw('SUM(noQty) as noQty,SUM(receivedQty) as receivedQty,SUM(noQty) - SUM(receivedQty) as balanceQty')
                    ->WHERE('purchaseOrderMasterID', $grvDetailsData->purchaseOrderMastertID)
                    ->first();


                if ($balanceQty["balanceQty"] == 0) {
                    $updatePO = ProcumentOrder::find($grvDetailsData->purchaseOrderMastertID)
                        ->update(['poClosedYN' => 1, 'grvRecieved' => 2]);
                } else {
                    if ($masterPOSUM->newNoQty > 0) {
                        $updatePO = ProcumentOrder::find($grvDetailsData->purchaseOrderMastertID)
                            ->update(['poClosedYN' => 0, 'grvRecieved' => 1]);
                    } else {
                        $updatePO = ProcumentOrder::find($grvDetailsData->purchaseOrderMastertID)
                            ->update(['poClosedYN' => 0, 'grvRecieved' => 0]);
                    }
                }
            }

        }

        return ['success' => true];
    }

    public static function documentListForClickHere()
    {
        return [2, 5, 52, 1, 50, 51];
    }

    public static function documentListForValidateSupplierBlockedStatus()
    {
        return [2, 5, 52, 3, 11, 4, 15];
    }

    public static function documentListForValidateCurrency()
    {
        return [4, 11, 15, 19, 20, 21];
    }


    public static function validateSupplierBlockedStatus($documentSystemID, $masterRecord)
    {
        $supplierColoumnKey = null;
        switch ($documentSystemID) {
            case 2:
            case 5:
            case 52:
                $supplierColoumnKey = 'supplierID';
                break;
            case 3:
                $supplierColoumnKey = 'supplierID';
                break;
            case 11:
                $supplierColoumnKey = 'supplierID';
                break;
            case 4:
                $supplierColoumnKey = 'BPVsupplierID';
                break;
            case 15:
                $supplierColoumnKey = 'supplierID';
                break;

            default:
                # code...
                break;
        }

        if (!is_null($supplierColoumnKey)) {
            if ($documentSystemID == 4) {
                if ($masterRecord->invoiceType == 3) {
                    if ($masterRecord->directPaymentpayeeYN == 0 && $masterRecord->directPaymentPayeeSelectEmp == 0 && $masterRecord->directPaymentPayeeEmpID == null) {
                        $supplierCodeSystem = $masterRecord[$supplierColoumnKey];
                        return self::checkSupplierBlocked($supplierCodeSystem);
                    }
                } else {
                    $supplierCodeSystem = $masterRecord[$supplierColoumnKey];
                    return self::checkSupplierBlocked($supplierCodeSystem);
                }
            } else {
                $supplierCodeSystem = $masterRecord[$supplierColoumnKey];
                return self::checkSupplierBlocked($supplierCodeSystem);
            }
        }

        return false;
    }

    public static function checkSupplierBlocked($supplierCodeSystem)
    {
        $supplier = SupplierMaster::find($supplierCodeSystem);

        if (!$supplier) {
            return false;
        }

        if ($supplier->isBlocked == 1) {
            return true;
        }

        return false;
    }

    /**
     * get current employee information
     * @return mixed
     */
    public static function getEmployeeInfo()
    {
        $user = Models\User::find(Auth::id());

        if(empty($user)){
            return  new \stdClass();
        }

        $employee = Models\Employee::with(['profilepic', 'user_data' => function($query) {
            $query->select('uuid', 'employee_id');
        },'language' => function ($q) {
            $q->with(['language' => function ($l) {
                $l->select(['languageID','languageShortCode','icon']);
            }]);
        }])->find($user->employee_id);

        return $employee;
    }

    public static function getEmployeeInfoByEmployeeID($employee_id)
    {
        $employee = Models\Employee::with(['profilepic', 'user_data' => function($query) {
            $query->select('uuid', 'employee_id');
        },'language' => function ($q) {
            $q->with(['language' => function ($l) {
                $l->select(['languageID','languageShortCode','icon']);
            }]);
        }])->find($employee_id);

        return $employee;
    }

    public static function getEmployeeInfoByURL($input)
    {

        if (!array_key_exists('Authorization', $input) || $input['Authorization'] == "") {
            return ['success' => false, 'message' => trans('custom.unauthorized')];
        }

        if (strpos($input['Authorization'], 'Bearer') === false) {
            return ['success' => false, 'message' => trans('custom.unauthorized')];
        }

        $token = trim(str_replace("", "", $input['Authorization']));

        $oauth = Models\AccessTokens::where('id', '<>', $token)
            //->where('id','like',"%{$token}%")
            ->where('revoked', 0)
            ->get();

        if (empty($oauth)) {
            return ['success' => false, 'message' => trans('custom.unauthorized')];
        }

        $id = 2637;
        $user = Models\User::find($id);
        $employee = Models\Employee::find($user->employee_id);

        if ($employee) {
            return ['success' => true, 'message' => $employee];
        } else {
            return ['success' => false, 'message' => trans('custom.unauthorized')];
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

    public static function amountInWords($amount)
    {
        $numFormatterEn = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

        $amountInWordsEnglish = ucwords($numFormatterEn->format($amount));

        return $amountInWordsEnglish;
    }

    public static function dateAddTime($date)
    {
        if ($date) {

            $time = (new Carbon($date))->format('H:i:s');
            if ($time != '00:00:00') {
                return new Carbon($date);
            }

            $date = self::dateOnlyFormat($date);
            return new Carbon($date . ' ' . date("h:i:sa"));
        } else {
            return null;
        }
    }

    public static function convertDateWithTime($date)
    {
        if ($date) {

            return self::dateOnlyFormat($date) ." ". date("g:i A", strtotime($date));

        } else {
            return null;
        }
    }

    public static function dateOnlyFormat($date)
    {
        if ($date) {
            return (new Carbon($date))->format('Y-m-d');
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
        if (!empty($user)) {
            return $user->employee_id;
        }
        return 0;
    }

    public static function getEmployeeUUID()
    {
        $user = Models\User::find(Auth::id());
        if (!empty($user)) {
            return $user->uuid;
        }
        return 0;
    }

    public static function getEmployeeCode($empId)
    {
        $employee = Models\Employee::find($empId);
        if (!empty($employee)) {
            return $employee->empID;
        }
        return 0;
    }

    public static function getEmployeeName()
    {
        $employee = Models\Employee::find(self::getEmployeeSystemID());
        if (!empty($employee)) {
            return $employee->empName;
        }
        return 0;
    }

    public static function getEmployeeID()
    {

        $user = Models\User::find(Auth::id());
        if (!empty($user)) {
            return $user->empID;
        }
        return 0;
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
        $companyFinanceYear = Models\CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ', DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear,bigginingDate"))
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
            $companyID = Helper::getGroupCompany($companySystemID);
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
        if (empty($decimal)) {
            $decimal['DecimalPlaces'] = 2;
        }
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

    public static function roundFloatValue($value)
    {
        return number_format((float) $value, 7, '.', '');
    }


    public static function companyFinanceYearCheck($input)
    {
        $input['companyFinanceYearID'] = isset($input['companyFinanceYearID']) ? $input['companyFinanceYearID'] : 0;
        $input['companySystemID'] = isset($input['companySystemID']) ? $input['companySystemID'] : 0;
        $companyFinanceYear = Models\CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])->first();
        if ($companyFinanceYear) {
            if ($companyFinanceYear->isActive != -1 && $companyFinanceYear->isCurrent != -1) {
                return ['success' => false, 'message' => trans('custom.selected_financial_year_not_active')];
            } else {
                return ['success' => true, 'message' => $companyFinanceYear];
            }
        } else {
            $companyFinanceYear = Models\CompanyFinanceYear::where('companySystemID', $input['companySystemID'])->where('isActive', -1)->where('isCurrent', -1)->first();
            if (empty($companyFinanceYear)) {
                return ['success' => false, 'message' => trans('custom.financial_year_not_selected_active')];
            } else {
                return ['success' => false, 'message' => trans('custom.please_select_finance_year')];
            }
        }
    }

    public static function companyFinancePeriodCheck($input)
    {
        $input['companyFinancePeriodID'] = isset($input['companyFinancePeriodID']) ? $input['companyFinancePeriodID'] : 0;
        $input['companySystemID'] = isset($input['companySystemID']) ? $input['companySystemID'] : 0;
        $companyFinancePeriod = Models\CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        if ($companyFinancePeriod) {
            if ($companyFinancePeriod->isActive != -1 && $companyFinancePeriod->isCurrent != -1) {
                return ['success' => false, 'message' => trans('custom.selected_financial_period_not_active')];
            } else {
                return ['success' => true, 'message' => $companyFinancePeriod];
            }
        } else {
            $companyFinancePeriod = Models\CompanyFinancePeriod::where('companySystemID', $input['companySystemID'])->where('isActive', -1)->where('isCurrent', -1)->where('departmentSystemID', $input['departmentSystemID'])->where('companyFinanceYearID', $input['companyFinanceYearID'])->first();
            if (!$companyFinancePeriod) {
                return ['success' => false, 'message' => trans('custom.financial_period_not_selected_active')];
            } else {
                return ['success' => false, 'message' => trans('custom.please_select_finance_period')];
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
            case 207: // Pos shift details
                $docInforArr["modelName"] = 'ShiftDetails';
                $docInforArr["transCurrencyID"] = 'transactionCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'transactionCurrencyID';
                $docInforArr["rptCurrencyID"] = 'comRptCurrencyID';
                $docInforArr["localCurrencyID"] = 'companyLocalCurrencyID';
                $docInforArr["transCurrencyER"] = 'companyReportingCurrencyID';
                $docInforArr["rptCurrencyER"] = 'companyReportingExchangeRate';
                $docInforArr["localCurrencyER"] = 'companyLocalExchangeRate';
                $docInforArr["defaultCurrencyER"] = 'companyLocalExchangeRate';
                break;
            case 208: // Pos invoice details
                $docInforArr["modelName"] = 'GposInvoice';
                $docInforArr["transCurrencyID"] = 'transactionCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'transactionCurrencyID';
                $docInforArr["rptCurrencyID"] = 'companyReportingCurrencyID';
                $docInforArr["localCurrencyID"] = 'companyLocalCurrencyID';
                $docInforArr["transCurrencyER"] = 'transactionExchangeRate';
                $docInforArr["rptCurrencyER"] = 'companyReportingExchangeRate';
                $docInforArr["localCurrencyER"] = 'transactionExchangeRate';
                $docInforArr["defaultCurrencyER"] = 'transactionExchangeRate';
                break;
            case 69: // Console JV
                $docInforArr["modelName"] = 'ConsoleJVMaster';
                $docInforArr["transCurrencyID"] = 'currencyID';
                $docInforArr["transDefaultCurrencyID"] = 'currencyID';
                $docInforArr["rptCurrencyID"] = 'rptCurrencyID';
                $docInforArr["localCurrencyID"] = 'localCurrencyID';
                $docInforArr["transCurrencyER"] = 'currencyER';
                $docInforArr["rptCurrencyER"] = 'rptCurrencyER';
                $docInforArr["localCurrencyER"] = 'localCurrencyER';
                $docInforArr["defaultCurrencyER"] = 'currencyER';
                break;
            case 20:
                $docInforArr["modelName"] = 'CustomerInvoice';
                $docInforArr["transCurrencyID"] = 'custTransactionCurrencyID';
                $docInforArr["transDefaultCurrencyID"] = 'custTransactionCurrencyID';
                $docInforArr["rptCurrencyID"] = 'companyReportingCurrencyID';
                $docInforArr["localCurrencyID"] = 'localCurrencyID';
                $docInforArr["transCurrencyER"] = 'custTransactionCurrencyER';
                $docInforArr["rptCurrencyER"] = 'companyReportingER';
                $docInforArr["localCurrencyER"] = 'localCurrencyER';
                $docInforArr["defaultCurrencyER"] = 'localCurrencyER';
                break;
            default:
                return ['success' => false, 'message' => trans('custom.document_id_not_found')];
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
                        if (is_numeric($transactionAmount) && is_numeric($trasToRptER)) {
                            $reportingAmount = $transactionAmount / $trasToRptER;
                        } else {
                            $reportingAmount = 0;
                        }
                    } else {
                        if (is_numeric($transactionAmount) && is_numeric($trasToRptER)) {
                            $reportingAmount = $transactionAmount * $trasToRptER;
                        } else {
                            $reportingAmount = 0;
                        }
                    }
                } else {
                    if ($trasToRptER > 1) {
                        if (is_numeric($transactionAmount) && is_numeric($trasToRptER)) {
                            $reportingAmount = $transactionAmount * $trasToRptER;
                        } else {
                            $reportingAmount = 0;
                        }
                    } else {
                        if (is_numeric($transactionAmount) && is_numeric($trasToRptER)) {
                            $reportingAmount = $transactionAmount / $trasToRptER;
                        } else {
                            $reportingAmount = 0;
                        }
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
                    if ($trasToLocER > 1) {
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
                    if ($trasToDefaultER > 1) {
                        $defaultAmount = $transactionAmount * $trasToDefaultER;
                    } else {
                        $defaultAmount = $transactionAmount / $trasToDefaultER;
                    }
                }
            }
        } else {
            return ['success' => false, 'message' => trans('custom.no_records_found')];
        }


        $array = array(
            'reportingAmount' => self::roundValue($reportingAmount),
            'localAmount' => self::roundValue($localAmount),
            'defaultAmount' => self::roundValue($defaultAmount),
            'documentAmount' => self::roundValue($defaultAmount)
        );

        return $array;
    }

    public static function generateAssetDisposal($masterData)
    {
        $fixedCapital = Models\AssetCapitalization::find($masterData['autoID']);

        if ($fixedCapital->allocationTypeID == 1) {

            $documentDate = Carbon::parse($fixedCapital->documentDate);
            $documentYear = $documentDate->format('Y');
            $documentYearMonth = $documentDate->format('Y-m');

            $companyFinanceYear = Models\CompanyFinanceYear::where('companySystemID', $fixedCapital['companySystemID'])->whereRaw('YEAR(bigginingDate) = ?', [$documentYear])->first();

            $companyFinancePeriod = Models\CompanyFinancePeriod::where('companySystemID', $fixedCapital['companySystemID'])->where('departmentSystemID', 9)->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)->whereRaw('DATE_FORMAT(dateFrom,"%Y-%m") = ?', [$documentYearMonth])->first();

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
                    $data['createdUserID'] = Helper::getEmployeeID();
                    $data['createdUserSystemID'] = Helper::getEmployeeSystemID();
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

        $formattedDateJvN = Carbon::parse($jvMasterData->JVdate)->format('M Y');

        if ($jvMasterData->jvType == 1 && $jvMasterData->isReverseAccYN == 0) {

            $lastSerial = Models\JvMaster::where('companySystemID', $jvMasterData->companySystemID)
                ->where('companyFinanceYearID', $jvMasterData->companyFinanceYearID)
                ->orderBy('jvMasterAutoId', 'desc')
                ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            $formattedJvDateR =  Carbon::parse($jvMasterData->JVdate)->format('Y-m-01');
            $firstDayNextMonth = Carbon::parse($formattedJvDateR)->addMonth()->firstOfMonth();
            $formattedDate = date("Y-m-d", strtotime($firstDayNextMonth));

            $companyFinanceYear = collect(\DB::select("SELECT companyFinanceYearID,bigginingDate,endingDate FROM companyfinanceyear WHERE companySystemID = " . $jvMasterData->companySystemID . " AND isActive = -1 AND isDeleted = 0 AND date('" . $formattedDate . "') BETWEEN bigginingDate AND endingDate"))->first();

            if ($companyFinanceYear) {
                $startYear = $firstDayNextMonth;
                $finYearExp = explode('-', $startYear);
                $finYear = $finYearExp[0];
            }

            $companyFinancePeriod = collect(\DB::select("SELECT companyFinancePeriodID,dateFrom, dateTo FROM companyfinanceperiod WHERE companySystemID = " . $jvMasterData->companySystemID . " AND departmentSystemID = 5 AND companyFinanceYearID = " . $companyFinanceYear->companyFinanceYearID . " AND date('" . $formattedDate . "') BETWEEN dateFrom AND dateTo"))->first();

            $jvCode = ($jvMasterData->companyID . '\\' . $finYear . '\\' . $jvMasterData->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

            $postJv = $jvMasterData->toArray();
            $postJv['JVcode'] = $jvCode;
            $postJv['serialNo'] = $lastSerialNumber;
            $postJv['JVdate'] = $firstDayNextMonth;

            $postJv['companyFinanceYearID'] = $companyFinanceYear->companyFinanceYearID;
            $postJv['FYBiggin'] = $companyFinanceYear->bigginingDate;
            $postJv['FYEnd'] = $companyFinanceYear->endingDate;
            $postJv['companyFinancePeriodID'] = $companyFinancePeriod->companyFinancePeriodID;
            $postJv['FYPeriodDateFrom'] = $companyFinancePeriod->dateFrom;
            $postJv['FYPeriodDateTo'] = $companyFinancePeriod->dateTo;

            $postJv['companyFinanceYearID'] = $companyFinanceYear->companyFinanceYearID;
            $postJv['JVNarration'] = 'Reversal of Revenue Accrual for the month of ' . $formattedDateJvN . '';
            $postJv['isReverseAccYN'] = -1;
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
                    $val->setAppends([]);
                }
            }

            $jvDetailArray = $fetchJVDetail->toArray();

            $storeJvDetail = Models\JvDetail::insert($jvDetailArray);
        }
    }

    public static function generatePOAccrualJournalVoucher($masterData)
    {
        $jvMasterData = Models\JvMaster::find($masterData);

        $formattedDateJvN = Carbon::parse($jvMasterData->JVdate)->format('M Y');

        if ($jvMasterData->jvType == 5 && $jvMasterData->isReverseAccYN == 0) {

            $lastSerial = Models\JvMaster::where('companySystemID', $jvMasterData->companySystemID)
                ->where('companyFinanceYearID', $jvMasterData->companyFinanceYearID)
                ->orderBy('jvMasterAutoId', 'desc')
                ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            $formattedJvDateR =  Carbon::parse($jvMasterData->JVdate)->format('Y-m-01');
            $firstDayNextMonth = Carbon::parse($formattedJvDateR)->addMonth()->firstOfMonth();
            $formattedDate = date("Y-m-d", strtotime($firstDayNextMonth));

            $companyFinanceYear = collect(\DB::select("SELECT companyFinanceYearID,bigginingDate,endingDate FROM companyfinanceyear WHERE companySystemID = " . $jvMasterData->companySystemID . " AND isActive = -1 AND isDeleted = 0 AND date('" . $formattedDate . "') BETWEEN bigginingDate AND endingDate"))->first();

            if ($companyFinanceYear) {
                $startYear = $companyFinanceYear->bigginingDate;
                $finYearExp = explode('-', $startYear);
                $finYear = $finYearExp[0];
            }

            $companyFinancePeriod = collect(\DB::select("SELECT companyFinancePeriodID,dateFrom, dateTo FROM companyfinanceperiod WHERE companySystemID = " . $jvMasterData->companySystemID . " AND departmentSystemID = 5 AND companyFinanceYearID = " . $companyFinanceYear->companyFinanceYearID . " AND date('" . $formattedDate . "') BETWEEN dateFrom AND dateTo"))->first();

            $jvCode = ($jvMasterData->companyID . '\\' . $finYear . '\\' . $jvMasterData->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

            $postJv = $jvMasterData->toArray();
            $postJv['JVcode'] = $jvCode;
            $postJv['serialNo'] = $lastSerialNumber;
            $postJv['JVdate'] = $firstDayNextMonth;

            $postJv['companyFinanceYearID'] = $companyFinanceYear->companyFinanceYearID;
            $postJv['FYBiggin'] = $companyFinanceYear->bigginingDate;
            $postJv['FYEnd'] = $companyFinanceYear->endingDate;
            $postJv['companyFinancePeriodID'] = $companyFinancePeriod->companyFinancePeriodID;
            $postJv['FYPeriodDateFrom'] = $companyFinancePeriod->dateFrom;
            $postJv['FYPeriodDateTo'] = $companyFinancePeriod->dateTo;

            $postJv['JVNarration'] = 'Reversal of PO accrual for the month of ' . $formattedDateJvN . '';
            $postJv['isReverseAccYN'] = -1;
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
                    $val->setAppends([]);
                }
            }

            $jvDetailArray = $fetchJVDetail->toArray();

            $storeJvDetail = Models\JvDetail::insert($jvDetailArray);
        }
    }

    public static function generatePaymentVoucher($pvMaster)
    {
        $masterData = ['documentSystemID' => $pvMaster->documentSystemID, 'autoID' => $pvMaster->PayMasterAutoId, 'companySystemID' => $pvMaster->companySystemID, 'employeeSystemID' => $pvMaster->confirmedByEmpSystemID];
        if ($pvMaster->pdcChequeYN == 0) {
            $jobPV = BankLedgerInsert::dispatch($masterData);
        }
        return ['success' => true, 'message' => "Payment voucher created successfully"];

    }

    public static function generateCustomerReceiptVoucher($pvMaster)
    {
        if ($pvMaster->invoiceType == 3) {
            $dpdetails = Models\DirectPaymentDetails::where('directPaymentAutoID', $pvMaster->PayMasterAutoId)->get();
            if (count($dpdetails) > 0) {
                if ($pvMaster->expenseClaimOrPettyCash == 6 || $pvMaster->expenseClaimOrPettyCash == 7) {
                    $company = Models\Company::find($pvMaster->interCompanyToSystemID);
                    $receivePayment['companySystemID'] = $pvMaster->interCompanyToSystemID;
                    $receivePayment['companyID'] = $company->CompanyID;
                    $receivePayment['documentSystemID'] = 21;
                    $receivePayment['documentID'] = 'BRV';

                    $documentDate = Carbon::parse($pvMaster->BPVdate);
                    $documentYear = $documentDate->format('Y');
                    $documentYearMonth = $documentDate->format('Y-m');

                    $companyFinanceYear = Models\CompanyFinanceYear::checkFinanceYear($pvMaster->interCompanyToSystemID, $documentDate->format('Y-m-d'));

                    if (empty($companyFinanceYear)) {
                        return ['success' => false, 'message' => "Inter company financial year not found"];
                    }
                    $receivePayment['companyFinanceYearID'] = $companyFinanceYear->companyFinanceYearID;
                    $receivePayment['FYBiggin'] = $companyFinanceYear->bigginingDate;
                    $receivePayment['FYEnd'] = $companyFinanceYear->endingDate;

                    $companyFinancePeriod = Models\CompanyFinancePeriod::where('companySystemID', $pvMaster->interCompanyToSystemID)
                        ->where('departmentSystemID', 4)
                        ->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)
                        ->whereRaw('DATE_FORMAT(dateFrom,"%Y-%m") = ?', [$documentYearMonth])
                        ->first();

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
                            $custRecDetail = Models\DirectReceiptDetail::create($receivePaymentDetail);
                        }

                        $params = array('autoID' => $custRecMaster->custReceivePaymentAutoID, 'company' => $pvMaster->interCompanyToSystemID, 'document' => 21, 'segment' => '', 'category' => '', 'amount' => 0);
                        $confirm = self::confirmWithoutRuleDocument($params);
                    }
                } else {
                    $dpdetails = Models\DirectPaymentDetails::where('directPaymentAutoID', $pvMaster->PayMasterAutoId)->where('glCodeIsBank', 1)->get();
                    if (count($dpdetails) > 0) {
                        foreach ($dpdetails as $val) {
                            $receivePayment['companySystemID'] = $pvMaster->companySystemID;
                            $receivePayment['companyID'] = $pvMaster->companyID;
                            $receivePayment['documentSystemID'] = $pvMaster->documentSystemID;
                            $receivePayment['documentID'] = $pvMaster->documentID;

                            $documentDate = Carbon::parse($pvMaster->BPVdate);
                            $documentYear = $documentDate->format('Y');
                            $documentYearMonth = $documentDate->format('Y-m');

                            $companyFinanceYear = Models\CompanyFinanceYear::checkFinanceYear($pvMaster->companySystemID, $documentDate->format('Y-m-d'));

                            if (empty($companyFinanceYear)) {
                                return ['success' => false, 'message' => "Financial year not found"];
                            }

                            $receivePayment['companyFinanceYearID'] = $companyFinanceYear->companyFinanceYearID;
                            $receivePayment['FYBiggin'] = $companyFinanceYear->bigginingDate;
                            $receivePayment['FYEnd'] = $companyFinanceYear->endingDate;



                            $companyFinancePeriod = Models\CompanyFinancePeriod::where('companySystemID', $pvMaster->companySystemID)->where('departmentSystemID', 4)->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)->whereRaw('DATE_FORMAT(dateFrom,"%Y-%m") = ?', [$documentYearMonth])->first();
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

                            $account = Models\BankAccount::where('chartOfAccountSystemID', $val->chartOfAccountSystemID)
                                ->where('companySystemID', $pvMaster->companySystemID)
                                ->first();

                            if (empty($account)) {
                                return ['success' => false, 'message' => "Bank account not found"];
                            }

                            $receivePayment['bankID'] = $account->bankmasterAutoID;
                            $receivePayment['bankAccount'] = $account->bankAccountAutoID;
                            $receivePayment['bankCurrency'] = $val->bankCurrencyID;
                            $receivePayment['bankCurrencyER'] = 1;

                            $companyCurrencyConversion = Helper::currencyConversion($pvMaster->companySystemID, $val->bankCurrencyID, $val->bankCurrencyID, $val->bankAmount);

                            $receivePayment['localCurrencyID'] = $val->localCurrency;
                            $receivePayment['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                            $receivePayment['companyRptCurrencyID'] = $val->comRptCurrency;
                            $receivePayment['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                            $receivePayment['bankAmount'] = $val->bankAmount;
                            $receivePayment['localAmount'] = Helper::roundValue($companyCurrencyConversion['localAmount']);
                            $receivePayment['companyRptAmount'] = Helper::roundValue($companyCurrencyConversion['reportingAmount']);
                            $receivePayment['receivedAmount'] = $val->bankAmount;

                            if(ExchangeSetupConfig::isMasterDocumentExchageRateChanged($pvMaster))
                            {
                                $receivePayment['localCurrencyID'] = $val->localCurrency;
                                $receivePayment['localCurrencyER'] = $pvMaster->localCurrencyER;
                                $receivePayment['companyRptCurrencyID'] = $val->comRptCurrency;
                                $receivePayment['companyRptCurrencyER'] = $pvMaster->comRptCurrencyER;
                                $receivePayment['bankAmount'] = ($pvMaster->payAmountSuppTrans + $pvMaster->VATAmount);
                                if($pvMaster->localCurrencyID == $pvMaster->supplierTransCurrencyID)
                                {
                                    $receivePayment['localAmount'] = ($pvMaster->payAmountSuppTrans + $pvMaster->VATAmount);
                                }else {
                                    $receivePayment['localAmount'] = Helper::roundValue(($pvMaster->payAmountSuppTrans + $pvMaster->VATAmount) / $pvMaster->localCurrencyER);
                                }

                                if($pvMaster->companyRptCurrencyID == $pvMaster->supplierTransCurrencyID)
                                {
                                    $receivePayment['companyRptAmount'] = ($pvMaster->payAmountSuppTrans + $pvMaster->VATAmount);
                                }else {
                                    if(isset($pvMaster->comRptCurrencyER))
                                    {
                                        $receivePayment['companyRptAmount'] = Helper::roundValue(($pvMaster->payAmountSuppTrans + $pvMaster->VATAmount) / $pvMaster->comRptCurrencyER);
                                    }else {
                                        $receivePayment['companyRptAmount'] = Helper::roundValue(($pvMaster->payAmountSuppTrans + $pvMaster->VATAmount));
                                    }
                                }

                                $receivePayment['receivedAmount'] = ($pvMaster->payAmountSuppTrans + $pvMaster->VATAmount);
                            }

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
                        }
                    }
                }
            }
            $masterData = ['documentSystemID' => $pvMaster->documentSystemID, 'autoID' => $pvMaster->PayMasterAutoId, 'companySystemID' => $pvMaster->companySystemID, 'employeeSystemID' => $pvMaster->confirmedByEmpSystemID];
            if ($pvMaster->pdcChequeYN == 0) {
                $jobPV = BankLedgerInsert::dispatch($masterData);
            }
        }
        return ['success' => true, 'message' => "Customer receive voucher created successfully"];
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
            return ['success' => false, 'message' => trans('custom.parameter_document_system_id_missing')];
        }

        if (!array_key_exists('company', $params)) {
            return ['success' => false, 'message' => trans('custom.parameter_company_missing')];
        }

        if (!array_key_exists('document', $params)) {
            return ['success' => false, 'message' => trans('custom.parameter_document_missing')];
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
                return ['success' => false, 'message' => trans('custom.document_id_not_found')];
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

    public static function appendToBankLedger($autoID, $isAutoCreateDoc = false)
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
                $data['payeeName'] = $payee->CustomerName;
            } else {
                $employeeData = Employee::find($custReceivePayment->PayeeEmpID);

                $data['payeeName'] = $employeeData ? $employeeData->empName: $custReceivePayment->PayeeName;
            }

            $data['payeeGLCodeID'] = $custReceivePayment->customerGLCodeSystemID;
            $data['payeeGLCode'] = $custReceivePayment->customerGLCode;
            $data['supplierTransCurrencyID'] = $custReceivePayment->custTransactionCurrencyID;
            $data['supplierTransCurrencyER'] = $custReceivePayment->custTransactionCurrencyER;
            $data['localCurrencyID'] = $custReceivePayment->localCurrencyID;
            $data['localCurrencyER'] = $custReceivePayment->localCurrencyER;
            $data['companyRptCurrencyID'] = $custReceivePayment->companyRptCurrencyID;
            $data['companyRptCurrencyER'] = $custReceivePayment->companyRptCurrencyER;
            $data['payAmountBank'] = Helper::roundValue($custReceivePayment->receivedAmount / $custReceivePayment->bankCurrencyER);
            $data['payAmountSuppTrans'] = Helper::roundValue($custReceivePayment->receivedAmount / $custReceivePayment->bankCurrencyER);
            $data['payAmountCompLocal'] = Helper::roundValue($custReceivePayment->receivedAmount / $custReceivePayment->localCurrencyER);
            $data['payAmountCompRpt'] = Helper::roundValue($custReceivePayment->receivedAmount / $custReceivePayment->companyRptCurrencyER);
            $data['invoiceType'] = $custReceivePayment->documentType;
            $data['chequePaymentYN'] = -1;

            $treasuryClearPolicy = CompanyPolicyMaster::where('companySystemID', $custReceivePayment->companySystemID)
                ->where('companyPolicyCategoryID', 96)
                ->where('isYesNO', 1)
                ->first();

            $documentFromBankReconciliation = Models\BankReconciliationDocuments::where('documentSystemID', $custReceivePayment->documentSystemID)->where('documentAutoId', $custReceivePayment->custReceivePaymentAutoID)->first();
            if (!empty($treasuryClearPolicy) || !empty($documentFromBankReconciliation)) {
                $empID = $isAutoCreateDoc ? UserTypeService::getSystemEmployee() : Helper::getEmployeeInfo();
                $data['trsClearedYN'] = -1;
                $data['trsClearedDate'] = NOW();
                $data['trsClearedByEmpSystemID'] = $empID->employeeSystemID;
                $data['trsClearedByEmpName'] = $empID->empFullName;
                $data['trsClearedByEmpID'] = $empID->empID;
                $data['trsClearedAmount'] = $data['payAmountBank'];
            }

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

    public static function  getCompanyById($companySystemID)
    {
        $company = Models\Company::select('CompanyID')->where("companySystemID", $companySystemID)->first();

        if (!empty($company)) {
            return $company->CompanyID;
        } else {
            return "";
        }
    }

    // storing records to budget
    public static function storeBudgetConsumption($params)
    {
        switch ($params["documentSystemID"]) { // check the document id
            case 11:
                $budgetConsumeData = array();
                $masterRec = Models\BookInvSuppMaster::find($params["autoID"]);
                if ($masterRec) {
                    if ($masterRec->documentType == 1 || $masterRec->documentType == 4) {
                        $directDetail = \DB::select('SELECT directInvoiceDetailsID,directInvoiceAutoID,serviceLineSystemID,serviceLineCode,chartOfAccountSystemID,glCode,budgetYear,SUM(localAmount) as localAmountTot, sum(comRptAmount) as comRptAmountTot, detail_project_id FROM erp_directinvoicedetails WHERE directInvoiceAutoID = ' . $params["autoID"] . ' GROUP BY serviceLineSystemID,chartOfAccountSystemID,detail_project_id ');

                        if (!empty($directDetail)) {
                            foreach ($directDetail as $value) {

                                $chartOfAccount = Models\ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $value->chartOfAccountSystemID)->first();

                                if ($chartOfAccount->catogaryBLorPLID == 2) {
                                    $budgetConsumeData[] = array(
                                        "companySystemID" => $masterRec->companySystemID,
                                        "companyID" => $masterRec->companyID,
                                        "serviceLineSystemID" => $value->serviceLineSystemID,
                                        "serviceLineCode" => $value->serviceLineCode,
                                        "documentSystemID" => $masterRec->documentSystemID,
                                        "documentID" => $masterRec->documentID,
                                        "documentSystemCode" => $params["autoID"],
                                        "documentCode" => $masterRec->bookingInvCode,
                                        "chartOfAccountID" => $value->chartOfAccountSystemID,
                                        "GLCode" => $value->glCode,
                                        "year" => $value->budgetYear,
                                        "companyFinanceYearID" => CompanyFinanceYear::financeYearID($value->budgetYear, $masterRec->companySystemID),
                                        "month" => date("m", strtotime($masterRec->bookingDate)),
                                        "consumedLocalCurrencyID" => $masterRec->localCurrencyID,
                                        "consumedLocalAmount" => abs($value->localAmountTot),
                                        "consumedRptCurrencyID" => $masterRec->companyReportingCurrencyID,
                                        "consumedRptAmount" => abs($value->comRptAmountTot),
                                        "timestamp" => date('d/m/Y H:i:s A'),
                                        "projectID" => $value->detail_project_id

                                    );
                                }
                            }
                            $budgetConsumeStore = Models\BudgetConsumedData::insert($budgetConsumeData);
                        }
                    } else if ($masterRec->documentType == 3) {
                        $budgetConsumeData = array();
                        $directDetail = \DB::select('SELECT SUM((supplier_invoice_items.costPerUnitLocalCur) *supplier_invoice_items.noQty) as costPerUnitLocalCur,SUM((supplier_invoice_items.costPerUnitComRptCur)*supplier_invoice_items.noQty) as costPerUnitComRptCur,supplier_invoice_items.companyReportingCurrencyID,supplier_invoice_items.financeGLcodePLSystemID,supplier_invoice_items.companySystemID,erp_bookinvsuppmaster.serviceLineSystemID,supplier_invoice_items.localCurrencyID, erp_bookinvsuppmaster.projectID, erp_bookinvsuppmaster.companyFinanceYearID, MONTH(createdDateAndTime) as month FROM supplier_invoice_items INNER JOIN erp_bookinvsuppmaster ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = supplier_invoice_items.bookingSuppMasInvAutoID  WHERE supplier_invoice_items.itemFinanceCategoryID != 3 AND supplier_invoice_items.bookingSuppMasInvAutoID = ' . $params["autoID"] . ' GROUP BY supplier_invoice_items.companySystemID,erp_bookinvsuppmaster.serviceLineSystemID,supplier_invoice_items.financeGLcodePLSystemID,erp_bookinvsuppmaster.projectID');

                        if (!empty($directDetail)) {
                            foreach ($directDetail as $value) {
                                if ($value->financeGLcodePLSystemID != "") {
                                    $budgetConsumeData[] = array(
                                        "companySystemID" => $value->companySystemID,
                                        "companyID" => Models\Company::getComanyCode($value->companySystemID),
                                        "serviceLineSystemID" => $value->serviceLineSystemID,
                                        "serviceLineCode" => Models\SegmentMaster::getSegmentCode($value->serviceLineSystemID),
                                        "documentSystemID" => $masterRec["documentSystemID"],
                                        "documentID" => $masterRec["documentID"],
                                        "documentSystemCode" => $params["autoID"],
                                        "documentCode" => $masterRec["bookingInvCode"],
                                        "chartOfAccountID" => $value->financeGLcodePLSystemID,
                                        "GLCode" => Models\ChartOfAccount::getAccountCode($value->financeGLcodePLSystemID),
                                        "year" => Models\CompanyFinanceYear::budgetYearByFinanceYearID($value->companyFinanceYearID),
                                        "companyFinanceYearID" => $value->companyFinanceYearID,
                                        "month" => $value->month,
                                        "consumedLocalCurrencyID" => $value->localCurrencyID,
                                        "consumedLocalAmount" => $value->costPerUnitLocalCur,
                                        "consumedRptCurrencyID" => $value->companyReportingCurrencyID,
                                        "consumedRptAmount" => $value->costPerUnitComRptCur,
                                        "projectID" => $value->projectID,
                                        "timestamp" => date('d/m/Y H:i:s A')
                                    );
                                }
                            }
                        }
                        $budgetConsume = Models\BudgetConsumedData::insert($budgetConsumeData);
                    }
                }
                break;
            case 4:
                $budgetConsumeData = array();
                $masterRec = Models\PaySupplierInvoiceMaster::find($params["autoID"]);
                if ($masterRec) {
                    if ($masterRec->invoiceType == 3) {
                        $directDetail = \DB::select('SELECT directPaymentDetailsID,directPaymentAutoID,serviceLineSystemID,serviceLineCode,chartOfAccountSystemID,glCode,budgetYear,SUM(localAmount) as localAmountTot, sum(comRptAmount) as comRptAmountTot FROM erp_directpaymentdetails WHERE directPaymentAutoID = ' . $params["autoID"] . ' GROUP BY serviceLineSystemID,chartOfAccountSystemID ');

                        if (!empty($directDetail)) {
                            foreach ($directDetail as $value) {

                                $chartOfAccount = Models\ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $value->chartOfAccountSystemID)->first();

                                if ($chartOfAccount->catogaryBLorPLID == 2) {
                                    $budgetConsumeData[] = array(
                                        "companySystemID" => $masterRec->companySystemID,
                                        "companyID" => $masterRec->companyID,
                                        "serviceLineSystemID" => $value->serviceLineSystemID,
                                        "serviceLineCode" => $value->serviceLineCode,
                                        "documentSystemID" => $masterRec->documentSystemID,
                                        "documentID" => $masterRec->documentID,
                                        "documentSystemCode" => $params["autoID"],
                                        "documentCode" => $masterRec->BPVcode,
                                        "chartOfAccountID" => $value->chartOfAccountSystemID,
                                        "GLCode" => $value->glCode,
                                        "year" => $value->budgetYear,
                                        "companyFinanceYearID" => CompanyFinanceYear::financeYearID($value->budgetYear, $masterRec->companySystemID),
                                        "month" => date("m", strtotime($masterRec->BPVdate)),
                                        "consumedLocalCurrencyID" => $masterRec->localCurrencyID,
                                        "consumedLocalAmount" => abs($value->localAmountTot),
                                        "consumedRptCurrencyID" => $masterRec->companyRptCurrencyID,
                                        "consumedRptAmount" => abs($value->comRptAmountTot),
                                        "timestamp" => date('d/m/Y H:i:s A'),
                                        "projectID" => $masterRec->projectID
                                    );
                                }
                            }
                            $budgetConsumeStore = Models\BudgetConsumedData::insert($budgetConsumeData);
                        }
                    }
                }
                break;
            case 15:
                $budgetConsumeData = array();
                $masterRec = Models\DebitNote::find($params["autoID"]);
                if ($masterRec) {
                    $directDetail = \DB::select('SELECT debitNoteDetailsID,debitNoteAutoID,serviceLineSystemID,serviceLineCode,chartOfAccountSystemID,glCode,budgetYear,sum(localAmount) as localAmountTot,sum(comRptAmount) as comRptAmountTot FROM erp_debitnotedetails WHERE debitNoteAutoID = ' . $params["autoID"] . ' GROUP BY serviceLineSystemID,chartOfAccountSystemID ');

                    if (!empty($directDetail)) {
                        foreach ($directDetail as $value) {

                            $chartOfAccount = Models\ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $value->chartOfAccountSystemID)->first();

                            if ($chartOfAccount->catogaryBLorPLID == 2) {
                                $budgetConsumeData[] = array(
                                    "companySystemID" => $masterRec->companySystemID,
                                    "companyID" => $masterRec->companyID,
                                    "serviceLineSystemID" => $value->serviceLineSystemID,
                                    "serviceLineCode" => $value->serviceLineCode,
                                    "documentSystemID" => $masterRec->documentSystemID,
                                    "documentID" => $masterRec->documentID,
                                    "documentSystemCode" => $params["autoID"],
                                    "documentCode" => $masterRec->debitNoteCode,
                                    "chartOfAccountID" => $value->chartOfAccountSystemID,
                                    "GLCode" => $value->glCode,
                                    "year" => $value->budgetYear,
                                    "companyFinanceYearID" => CompanyFinanceYear::financeYearID($value->budgetYear, $masterRec->companySystemID),
                                    "month" => date("m", strtotime($masterRec->debitNoteDate)),
                                    "consumedLocalCurrencyID" => $masterRec->localCurrencyID,
                                    "consumedLocalAmount" => ($value->localAmountTot * -1),
                                    "consumedRptCurrencyID" => $masterRec->companyReportingCurrencyID,
                                    "consumedRptAmount" => ($value->comRptAmountTot * -1),
                                    "timestamp" => date('d/m/Y H:i:s A'),
                                    "projectID" => $masterRec->projectID
                                );
                            }
                        }
                        $budgetConsumeStore = Models\BudgetConsumedData::insert($budgetConsumeData);
                    }
                }
                break;
            case 19:
                $budgetConsumeData = array();
                $masterRec = Models\CreditNote::find($params["autoID"]);
                if ($masterRec) {
                    $directDetail = \DB::select('SELECT creditNoteDetailsID,creditNoteAutoID,serviceLineSystemID,serviceLineCode,chartOfAccountSystemID,glCode,budgetYear,sum(localAmount) as localAmountTot,sum(comRptAmount) as comRptAmountTot FROM erp_creditnotedetails WHERE creditNoteAutoID = ' . $params["autoID"] . ' GROUP BY serviceLineSystemID,chartOfAccountSystemID ');

                    if (!empty($directDetail)) {
                        foreach ($directDetail as $value) {

                            $chartOfAccount = Models\ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $value->chartOfAccountSystemID)->first();

                            if ($chartOfAccount->catogaryBLorPLID == 2) {
                                $budgetConsumeData[] = array(
                                    "companySystemID" => $masterRec->companySystemID,
                                    "companyID" => $masterRec->companyID,
                                    "serviceLineSystemID" => $value->serviceLineSystemID,
                                    "serviceLineCode" => $value->serviceLineCode,
                                    "documentSystemID" => $masterRec->documentSystemiD,
                                    "documentID" => $masterRec->documentID,
                                    "documentSystemCode" => $params["autoID"],
                                    "documentCode" => $masterRec->creditNoteCode,
                                    "chartOfAccountID" => $value->chartOfAccountSystemID,
                                    "GLCode" => $value->glCode,
                                    "year" => $value->budgetYear,
                                    "companyFinanceYearID" => CompanyFinanceYear::financeYearID($value->budgetYear, $masterRec->companySystemID),
                                    "month" => date("m", strtotime($masterRec->creditNoteDate)),
                                    "consumedLocalCurrencyID" => $masterRec->localCurrencyID,
                                    "consumedLocalAmount" => ($value->localAmountTot * -1),
                                    "consumedRptCurrencyID" => $masterRec->companyReportingCurrencyID,
                                    "consumedRptAmount" => ($value->comRptAmountTot * -1),
                                    "timestamp" => date('d/m/Y H:i:s A'),
                                    "projectID" => $masterRec->projectID

                                );
                            }
                        }
                        $budgetConsumeStore = Models\BudgetConsumedData::insert($budgetConsumeData);
                    }
                }
                break;
            default:
        }
    }

    // sending email based on policy
    public static function sendingEmailNotificationPolicy($params)
    {
        switch ($params["documentSystemID"]) { // check the document id
            case 1:
            case 50:
            case 51:
                $masterRec = Models\PurchaseRequest::find($params["autoID"]);
                if ($masterRec) {
                    $fetchingUsers = \DB::select('SELECT employeeSystemID, sendYN,companySystemID FROM erp_documentemailnotificationdetail WHERE emailNotificationID = 1 AND sendYN = 1 AND companySystemID = ' . $params["companySystemID"] . '');
                    $emails = array();
                    if (!empty($fetchingUsers)) {
                        foreach ($fetchingUsers as $value) {

                            $subject = trans('email.new_request_approved', ['requestCode' => $masterRec->purchaseRequestCode]);
                            $body = trans('email.new_request_approved_body', ['requestCode' => $masterRec->purchaseRequestCode]);

                            $emails[] = array(
                                'empSystemID' => $value->employeeSystemID,
                                'companySystemID' => $value->companySystemID,
                                'docSystemID' => $masterRec->documentSystemID,
                                'alertMessage' => $subject,
                                'emailAlertMessage' => $body,
                                'docSystemCode' => $params["autoID"]
                            );
                        }
                        $sendEmail = Email::sendEmail($emails);
                    }
                }
                break;
            case 2:
            case 5:
            case 52:
                $masterRec = Models\ProcumentOrder::find($params["autoID"]);
                if ($masterRec) {
                    if ($masterRec->logisticsAvailable == -1) {

                        $fetchingUsers = \DB::select('SELECT employeeSystemID, sendYN,companySystemID FROM erp_documentemailnotificationdetail WHERE emailNotificationID = 2 AND sendYN = 1 AND companySystemID = ' . $params["companySystemID"] . '');
                        $emails = array();
                        if (!empty($fetchingUsers)) {
                            foreach ($fetchingUsers as $value) {

                                $subject = $masterRec->purchaseOrderCode . " marked as logistics available is approved.";
                                $body = '<p>A new order ' . $masterRec->purchaseOrderCode . ' marked as logistics available, is approved.</p>';

                                $emails[] = array(
                                    'empSystemID' => $value->employeeSystemID,
                                    'companySystemID' => $value->companySystemID,
                                    'docSystemID' => $masterRec->documentSystemID,
                                    'alertMessage' => $subject,
                                    'emailAlertMessage' => $body,
                                    'docSystemCode' => $params["autoID"]
                                );
                            }
                            $sendEmail = Email::sendEmail($emails);
                        }
                    }
                }
                break;
            case 4:
                $masterRec = Models\PaySupplierInvoiceMaster::find($params["autoID"]);
                if ($masterRec) {
                    $fetchingUsers = \DB::select('SELECT employeeSystemID, sendYN,companySystemID FROM erp_documentemailnotificationdetail WHERE emailNotificationID = 3 AND sendYN = 1 AND companySystemID = ' . $params["companySystemID"] . '');

                    $supplierDetail = Models\SupplierMaster::find($masterRec->BPVsupplierID);
                    $companyDetail = Models\Company::find($params["companySystemID"]);
                    $supplierName = '';
                    if ($supplierDetail) {
                        $supplierName = $supplierDetail->supplierName;
                    }
                    $emails = array();
                    if ($masterRec->BPVsupplierID != 0 || $masterRec->BPVsupplierID != null) {

                        if (!empty($fetchingUsers)) {
                            foreach ($fetchingUsers as $value) {

                                $subject = 'Payment ' . $masterRec->BPVcode . ' is released.';
                                $body = '<p>Payment ' . $masterRec->BPVcode . '  has been released to ' . $supplierName . ' from ' . $companyDetail->CompanyName . '</p>';

                                $emails[] = array(
                                    'empSystemID' => $value->employeeSystemID,
                                    'companySystemID' => $value->companySystemID,
                                    'docSystemID' => $masterRec->documentSystemID,
                                    'alertMessage' => $subject,
                                    'emailAlertMessage' => $body,
                                    'docSystemCode' => $params["autoID"]
                                );
                            }
                            $sendEmail = Email::sendEmail($emails);
                        }
                    }
                }
                break;
            default:
        }
    }

    public static function generateAssetCosting($masterData)
    {
        if ($masterData && is_null($masterData->docOriginSystemCode)) {
            $companyCurrency = self::companyCurrency($masterData->companySystemID);
            $cost['faID'] = $masterData->faID;
            $cost['assetID'] = $masterData->faCode;
            $cost['assetDescription'] = $masterData->assetDescription;
            $cost['costDate'] = $masterData->documentDate;
            $cost['localCurrencyID'] = $companyCurrency->localCurrencyID;
            $cost['localAmount'] = $masterData->COSTUNIT;
            $cost['rptCurrencyID'] = $companyCurrency->reportingCurrency;
            $cost['rptAmount'] = $masterData->costUnitRpt;
            $assetCosting = Models\FixedAssetCost::create($cost);
        }
    }

    public static function isLocalSupplier($supplierId, $companyId, $country_id = 0)
    {
        $check = 0;
        $company = Models\Company::find($companyId);

        if (!empty($company)) {
            $check = Models\SupplierMaster::where('supplierCountryID', $company->companyCountry)
                ->where('supplierCodeSystem', $supplierId)
                ->count();
        }

        if ($check > 0) {
            return true;
        }
        return false;
    }

    public static function getDocumentSystemIDByCode($code)
    {
        $doc = Models\DocumentMaster::where('documentID', $code)->first();
        if ($doc) {
            return $doc->documentSystemID;
        }
        return null;
    }

    public static function getCompanySystemIDByCode($code)
    {
        $company = Models\Company::where('companyID', $code)->first();
        if ($company) {
            return $company->companySystemID;
        }
        return null;
    }

    public static function checkRestrictionByPolicy($companySystemID, $documentRestrictionPolicyID)
    {

        $permission = false;
        if (!$companySystemID && $documentRestrictionPolicyID) {
            return $permission;
        }
        $id = Auth::id();
        $user = User::with(['employee'])->find($id);
        $empId = $user->employee['employeeSystemID'];
        $permission = false;
        $userGroup = EmployeeNavigation::where('employeeSystemID', $empId)
            ->where('companyID', $companySystemID)
            ->first();
        if (empty($userGroup)) {
            return $permission;
        }
        $userGroupID = $userGroup->userGroupID;
        $checkCount = DocumentRestrictionAssign::where('companySystemID', $companySystemID)
            ->where('documentRestrictionPolicyID', $documentRestrictionPolicyID)
            ->where('userGroupID', $userGroupID)
            ->count();
        if ($checkCount > 0) {
            $permission = true;
        }
        return $permission;
    }

    public static function checkCompanyForMasters($companyID, $documentSystemID = null, $documentType = null, $edit = false)
    {
        $isGroup = self::checkIsCompanyGroup($companyID);

        if ($isGroup) {
            if (is_null($documentType)) {
                return ['success' => false, 'message' => trans('custom.primary_company_cannot_group')];
            } else {
                return ['success' => false, 'message' => trans('custom.assigned_company_cannot_group')];
            }
        }

        switch ($documentType) {
            case 'supplier':
                $supplierAssigned = Models\SupplierAssigned::where('supplierCodeSytem', $documentSystemID)->where('companySystemID', $companyID)->first();
                if (!is_null($supplierAssigned)) {
                    return ['success' => false, 'message' => trans('custom.supplier_already_assign_company')];
                }
                break;
            case 'item':
                $itemAssigned = Models\ItemAssigned::where('itemCodeSystem', $documentSystemID)->where('companySystemID', $companyID)->first();
                if (!is_null($itemAssigned)) {
                    return ['success' => false, 'message' => trans('custom.item_already_assign_company')];
                }
                break;
            case 'customer':
                if (!$edit) {
                    $customerAssigned = Models\CustomerAssigned::where('customerCodeSystem', $documentSystemID)->where('companySystemID', $companyID)->first();
                    if (!is_null($customerAssigned)) {
                        return ['success' => false, 'message' => trans('custom.customer_already_assign_company')];
                    }
                }

                $customerMasterData = CustomerMaster::find($documentSystemID);
                if ($customerMasterData && !is_null($customerMasterData->customerCategoryID)) {
                    $checkAssignedStatusOfCategory = Models\CustomerMasterCategoryAssigned::checkCustomerCategoryAssignedStatus($customerMasterData->customerCategoryID, $companyID);
                    if (!$checkAssignedStatusOfCategory) {
                        return ['success' => false, 'message' => trans('custom.customer_category_not_assign_company')];
                    }
                }

                if ($customerMasterData && (!is_null($customerMasterData->custGLAccountSystemID) || !is_null($customerMasterData->custUnbilledAccountSystemID))) {
                    if (!is_null($customerMasterData->custGLAccountSystemID)) {
                        $checkAssignedStatusOfGL = Models\ChartOfAccountsAssigned::checkCOAAssignedStatus($customerMasterData->custGLAccountSystemID, $companyID);
                        if (!$checkAssignedStatusOfGL) {
                            return ['success' => false, 'message' => trans('custom.gl_account_not_assign_company')];
                        }
                    }


                    if (!is_null($customerMasterData->custUnbilledAccountSystemID)) {
                        $checkAssignedStatusOfGL = Models\ChartOfAccountsAssigned::checkCOAAssignedStatus($customerMasterData->custUnbilledAccountSystemID, $companyID);
                        if (!$checkAssignedStatusOfGL) {
                            return ['success' => false, 'message' => trans('custom.unbilled_account_not_assign_company')];
                        }
                    }
                }

                break;
            case 'customerCategory':
                $customerAssigned = Models\CustomerMasterCategoryAssigned::where('customerMasterCategoryID', $documentSystemID)->where('companySystemID', $companyID)->first();
                if (!is_null($customerAssigned)) {
                    return ['success' => false, 'message' => trans('custom.customer_category_already_assign_company')];
                }
                break;
            case 'chartofaccounts':
                $chartOfAccountAssigned = Models\ChartOfAccountsAssigned::where('chartOfAccountSystemID', $documentSystemID)->where('companySystemID', $companyID)->first();
                if (!is_null($chartOfAccountAssigned)) {
                    return ['success' => false, 'message' => trans('custom.chart_account_already_assign_company')];
                }
                break;

            default:
                # code...
                break;
        }

        return ['success' => true, 'message' => "success"];
    }


    /**
     * Get all companies related to a group
     * @param $selectedCompanyId - current company id
     * @return array
     */
    public static function getSimilarGroupCompanies($selectedCompanyId)
    {

        $masterCompany = Models\Company::find($selectedCompanyId);
        $companies = Models\Company::where('masterCompanySystemIDReorting', $masterCompany->masterCompanySystemIDReorting)->get();

        $groupCompany = [];
        if ($companies) {
            foreach ($companies as $val) {
                $groupCompany[] = array('companySystemID' => $val["companySystemID"], 'CompanyID' => $val["CompanyID"], 'CompanyName' => $val["CompanyName"]);
            }
        }
        $groupCompany = array_column($groupCompany, 'companySystemID');
        return $groupCompany;
    }

    public static function sendMail($id, $companySystemID)
    {

        $hasPolicy = CompanyPolicyMaster::where('companySystemID', $companySystemID)->where('companyPolicyCategoryID', 37)->where('isYesNO', 1)->exists();
        if ($hasPolicy) {
            $details = Alert::find($id);
            if ($details && $details->isEmailSend == 0) {
                $is_sent = SendEmail::dispatch($details->empEmail, $details->alertMessage, $details->emailAlertMessage, $details->alertID);
                if ($is_sent) {
                    Alert::where('alertID', $details->alertID)->update(['isEmailSend' => 1]);
                }
            }
        }
    }

    public static function checkEmployeeDischarchedYN()
    {
        $user = Models\User::find(Auth::id());
        if (!empty($user)) {
            $employee = Models\Employee::find($user->employee_id);
            if ($employee->discharegedYN == -1) {
                return 'true';
            } else {
                return 'false';
            }
        }
        return 'false';
    }

    public static function getFileUrlFromS3($key)
    {
        if($key) {            
            //return Storage::disk('s3')->url($key);

            $s3 = Storage::disk('s3');
            $client = $s3->getClient();
            $bucket = Config::get('filesystems.disks.s3.bucket');
            $command = $client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $key
            ]);
            $request = $client->createPresignedRequest($command, '+60 minutes');
            return (string)$request->getUri();
        }
        return '';
    }

    public static function stringToFloat($str)
    {
        return floatval(preg_replace('/[^0-9.]/', '', $str));
    }

    public static function formatNumberWithPrecision($value) { // this method check the number and if it is less than 1 then it will return the value with 7 decimal places and its comma separated and solve scienfivic notation issue

        $numericValue = (float) str_replace(',', '', $value);
    
        if ($numericValue >= 1) {
            return number_format($numericValue, 7, '.', '');
        }
    
        return number_format($value, 7, '.', '');
    }

    public static function checkPolicy($companySystemID = 0, $policyId = 0)
    {

        return CompanyPolicyMaster::where('companySystemID', $companySystemID)
            ->where('companyPolicyCategoryID', $policyId)
            ->where('isYesNO', 1)
            ->exists();
    }

    public static function policyWiseDisk($companySystemID, $currentDisk = null)
    {
        $awsPolicy = self::checkPolicy($companySystemID, 50);
        return 's3';
        // if ($awsPolicy) {
        //     return 's3';
        // } else {
        //     if (is_null($currentDisk)) {
        //         return 'public';
        //     } else {
        //         return $currentDisk;
        //     }
        // }
    }

    static function isArray($value, $default = 0)
    {
        return isset($value) ? (is_array($value) ? (isset($value[0]) ? $value[0] : $default) : $value) : $default;
    }

    public static function getDocumentDetails($companySystemID, $documentSystemID, $documentSystemCode, $isMatchingDoc = 0)
    {
        $output = [];
        if ($isMatchingDoc == 0) {
            switch ($documentSystemID) {
                case 1:
                case 50:
                case 51:
                    $output = PurchaseRequestDetails::where('purchaseRequestID', $documentSystemCode)
                        ->whereHas('purchase_request', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['purchase_request' => function ($query) {
                            $query->with(['currency_by']);
                        }, 'uom'])
                        ->get();
                    break;

                case 2:
                case 5:
                case 52:
                    $output = PurchaseOrderDetails::where('purchaseOrderMasterID', $documentSystemCode)
                        ->whereHas('order', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['order' => function ($query) {
                            $query->with(['transactioncurrency']);
                        }, 'unit'])
                        ->get();
                    break;
                case 3:
                    $output = GRVDetails::where('grvAutoID', $documentSystemCode)
                        ->whereHas('grv_master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['grv_master' => function ($query) {
                            $query->with(['currency_by']);
                        }, 'unit'])
                        ->get();
                    break;
                case 9:
                    $output = Models\MaterielRequestDetails::where('RequestID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master', 'item_by', 'uom_default', 'uom_issuing'])
                        ->get();
                    break;
                case 8:
                    $output = Models\ItemIssueDetails::where('itemIssueAutoID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master', 'uom_default', 'uom_issuing'])
                        ->get();
                    break;
                case 12:
                    $output = Models\ItemReturnDetails::where('itemReturnAutoID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master', 'issue', 'uom_receiving'])
                        ->get();
                    break;
                case 13:
                    $output = Models\StockTransferDetails::where('stockTransferAutoID', $documentSystemCode)
                        ->whereHas('master_by', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master_by', 'unit_by'])
                        ->get();
                    break;
                case 10:
                    $output = Models\StockReceiveDetails::where('stockReceiveAutoID', $documentSystemCode)
                        ->whereHas('stock_receive', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['stock_receive', 'unit_by'])
                        ->get();
                    break;
                case 7:
                    $output = Models\StockAdjustmentDetails::where('stockAdjustmentAutoID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master', 'local_currency', 'rpt_currency', 'uom'])
                        ->get();
                    break;
                case 24:
                    $output = Models\PurchaseReturnDetails::where('purhaseReturnAutoID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master', 'grv_master', 'unit'])
                        ->get();
                    break;
                case 61:
                    $output = Models\InventoryReclassificationDetail::where('inventoryreclassificationID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master', 'localcurrency', 'reportingcurrency', 'unit'])
                        ->get();
                    break;

                case 14:
                    $output = Models\LogisticDetails::where('logisticMasterID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master', 'supplier_by', 'warehouse_by', 'po', 'uom'])
                        ->get();
                    break;

                case 11:

                    $master = Models\BookInvSuppMaster::find($documentSystemCode);
                    if ($master->documentType == 1) {
                        $output = Models\DirectInvoiceDetails::where('directInvoiceAutoID', $documentSystemCode)
                            ->whereHas('supplier_invoice_master', function ($query) use ($companySystemID, $documentSystemID) {
                                $query->where('companySystemID', $companySystemID)
                                    ->where('documentSystemID', $documentSystemID);
                            })
                            ->with(['supplier_invoice_master' => function ($query) {
                                $query->with(['transactioncurrency']);
                            }, 'segment'])
                            ->get();
                    } else {
                        $output = Models\BookInvSuppDet::where('bookingSuppMasInvAutoID', $documentSystemCode)
                            ->whereHas('suppinvmaster', function ($query) use ($companySystemID, $documentSystemID) {
                                $query->where('companySystemID', $companySystemID)
                                    ->where('documentSystemID', $documentSystemID);
                            })
                            ->with(['suppinvmaster' => function ($query) {
                                $query->with(['transactioncurrency']);
                            }, 'pomaster', 'grvmaster'])
                            ->get();
                    }
                    break;

                case 15:
                    $output = Models\DebitNoteDetails::where('debitNoteAutoID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master' => function ($query) {
                            $query->with(['transactioncurrency']);
                        }, 'segment'])
                        ->get();
                    break;
                case 4:

                    $master = Models\PaySupplierInvoiceMaster::find($documentSystemCode);
                    if ($master->invoiceType == 2) {
                        $output = Models\PaySupplierInvoiceDetail::where('PayMasterAutoId', $documentSystemCode)
                            ->whereHas('payment_master', function ($query) use ($companySystemID, $documentSystemID) {
                                $query->where('companySystemID', $companySystemID)
                                    ->where('documentSystemID', $documentSystemID);
                            })
                            ->with(['payment_master' => function ($query) {
                                $query->with(['transactioncurrency']);
                            }, 'pomaster'])
                            ->get();
                    } elseif ($master->invoiceType == 5) {
                        $output = Models\AdvancePaymentDetails::where('PayMasterAutoId', $documentSystemCode)
                            ->whereHas('pay_invoice', function ($query) use ($companySystemID, $documentSystemID) {
                                $query->where('companySystemID', $companySystemID)
                                    ->where('documentSystemID', $documentSystemID);
                            })
                            ->with(['pay_invoice' => function ($query) {
                                $query->with(['transactioncurrency']);
                            }])
                            ->get();
                    } else {
                        $output = Models\DirectPaymentDetails::where('directPaymentAutoID', $documentSystemCode)
                            ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                                $query->where('companySystemID', $companySystemID)
                                    ->where('documentSystemID', $documentSystemID);
                            })
                            ->with(['master' => function ($query) {
                                $query->with(['transactioncurrency']);
                            }, 'segment'])
                            ->get();
                    }
                    break;
                case 6:
                    $output = Models\ExpenseClaimDetails::where('expenseClaimMasterAutoID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master', 'segment', 'category', 'currency', 'local_currency'])
                        ->get();
                    break;
                case 28:
                    $output = Models\MonthlyAdditionDetail::where('monthlyAdditionsMasterID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master', 'currency_ma', 'employee', 'department', 'expense_claim', 'chart_of_account'])
                        ->get();
                    break;

                case 20:
                    $master = Models\CustomerInvoiceDirect::find($documentSystemCode);

                    if ($master->isPerforma == 0 || $master->isPerforma == 1) {
                        $output = Models\CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $documentSystemCode)
                            ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                                $query->where('companySystemID', $companySystemID)
                                    ->where('documentSystemiD', $documentSystemID);
                            })
                            ->with(['master' => function ($query) {
                                $query->with(['currency']);
                            }, 'contract', 'department', 'unit'])
                            ->get();
                    } else {
                        $output = Models\CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $documentSystemCode)
                            ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                                $query->where('companySystemID', $companySystemID)
                                    ->where('documentSystemiD', $documentSystemID);
                            })
                            ->with(['master' => function ($query) {
                                $query->with(['currency', 'report_currency', 'local_currency']);
                            }, 'uom_default', 'uom_issuing'])
                            ->get();
                    }
                    break;
                case 19:
                    $output = Models\CreditNoteDetails::where('creditNoteAutoID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master' => function ($query) {
                            $query->with(['currency']);
                        }, 'segment'])
                        ->get();
                    break;
                case 21:

                    $output = CustomerReceivePayment::where('custReceivePaymentAutoID', $documentSystemCode)
                        ->with(['currency', 'details', 'directdetails.segment', 'advance_receipt_details'])
                        ->get();
                    break;
                case 39:
                    $output = Models\CustomerInvoiceTrackingDetail::where('customerInvoiceTrackingID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master' => function ($query) {
                            $query->with(['company.reportingcurrency']);
                        }, 'approved_by', 'rejected_by'])
                        ->get();
                    break;

                case 67:
                case 68:
                    $output = Models\QuotationDetails::where('quotationMasterID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master' => function ($query) {
                            $query->with(['transaction_currency', 'local_currency']);
                        }, 'uom_issuing'])
                        ->get();
                    break;

                case 71:
                    $output = Models\DeliveryOrderDetail::where('deliveryOrderID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master' => function ($query) {
                            $query->with(['transaction_currency', 'local_currency']);
                        }, 'quotation', 'uom_default', 'uom_issuing'])
                        ->get();
                    break;
                case 87:
                    $output = Models\SalesReturnDetail::where('salesReturnID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master' => function ($query) {
                            $query->with(['transaction_currency']);
                        }, 'delivery_order', 'uom_default', 'uom_issuing'])
                        ->get();
                    break;
                case 17:
                    $output = Models\JvDetail::where('jvMasterAutoId', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master', 'currency_by', 'segment'])
                        ->get();
                    break;
                case 46:
                    $output = Models\BudgetTransferFormDetail::where('budgetTransferFormAutoID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master.company.reportingcurrency', 'from_segment', 'to_segment', 'from_template', 'to_template'])
                        ->get();
                    break;
                case 69:
                    $output = Models\ConsoleJVDetail::where('consoleJvMasterAutoId', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master'])
                        ->get();
                    break;
                case 23:
                    $output = Models\FixedAssetDepreciationPeriod::where('depMasterAutoID', $documentSystemCode)
                        ->whereHas('master_by', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master_by' => function ($query) {
                            $query->with(['company' => function ($q) {
                                $q->with(['reportingcurrency', 'localcurrency']);
                            }]);
                        }, 'maincategory_by', 'financecategory_by', 'serviceline_by'])
                        ->get();
                    break;
                case 41:
                    $output = Models\AssetDisposalDetail::where('assetdisposalMasterAutoID', $documentSystemCode)
                        ->whereHas('master_by', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master_by' => function ($query) {
                            $query->with(['company' => function ($q) {
                                $q->with(['reportingcurrency', 'localcurrency']);
                            }]);
                        }, 'segment_by', 'item_by'])
                        ->get();
                    break;
                case 63:
                    $output = Models\AssetCapitalizationDetail::where('capitalizationID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master' => function ($query) {
                            $query->with(['company' => function ($q) {
                                $q->with(['reportingcurrency', 'localcurrency']);
                            }]);
                        }, 'segment'])
                        ->get();
                    break;
                case 97:
                    $output = Models\StockCountDetail::where('stockCountAutoID', $documentSystemCode)
                        ->whereHas('master', function ($query) use ($companySystemID, $documentSystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('documentSystemID', $documentSystemID);
                        })
                        ->with(['master', 'uom'])
                        ->get();
                    break;

                default:
                    $output = [];
            }
        } else {
            if ($isMatchingDoc == 1) {    // voucher matching

                $output = PaySupplierInvoiceDetail::where('matchingDocID', $documentSystemCode)
                    ->whereHas('matching_master', function ($query) use ($companySystemID, $documentSystemID) {
                        $query->where('companySystemID', $companySystemID)
                            ->where('documentSystemID', $documentSystemID);
                    })
                    ->with(['matching_master' => function ($query) {
                        $query->with(['transactioncurrency']);
                    }, 'pomaster'])
                    ->get();
            } elseif ($isMatchingDoc == 2) {  // receipt matching
                $output = CustomerReceivePaymentDetail::where('matchingDocID', $documentSystemCode)
                    ->whereHas('matching_master', function ($query) use ($companySystemID, $documentSystemID) {
                        $query->where('companySystemID', $companySystemID)
                            ->where('documentSystemID', $documentSystemID);
                    })
                    ->with(['matching_master' => function ($query) {
                        $query->with(['transactioncurrency']);
                    }])
                    ->get();
            }
        }
        return $output;
    }

    public static function exception_to_error($ex)
    {
        return [
            'exception' => $ex->getMessage(),
            'file' => $ex->getFile(),
            'line' => $ex->getLine()
        ];
    }

    public static function checkHrmsIntergrated($companySystemID)
    {
        $company = Company::find($companySystemID);
        return ($company) ? $company->isHrmsIntergrated : 0;
    }

    public static function headerCategoryOfReportTemplate($templateDetailID)
    {

        $detail = ReportTemplateDetails::find($templateDetailID);

        $headerDetail = ['description' => "", 'sortOrder' => 0];;
        if ($detail) {
            if (is_null($detail->masterID)) {
                $headerDetail = ['description' => $detail->description, 'sortOrder' => $detail->sortOrder];
            } else {
                $headerDetail = self::getHeaderDetailOfReportTemplate($detail->masterID);
            }
        }

        return $headerDetail;
    }

    public static function getHeaderDetailOfReportTemplate($templateDetailID)
    {

        $detail = ReportTemplateDetails::find($templateDetailID);

        $headerDetail = "";
        if ($detail) {
            if (is_null($detail->masterID)) {
                $headerDetail = ['description' => $detail->description, 'sortOrder' => $detail->sortOrder];
            } else {
                $headerDetail = self::getHeaderDetailOfReportTemplate($detail->masterID);
            }
        }

        return $headerDetail;
    }


    public static function getMasterLevelOfReportTemplate($detID)
    {

        $detail = ReportTemplateDetails::find($detID);

        $masterID = null;
        if ($detail) {
            if (is_null($detail->masterID)) {
                $masterID = $detail->detID;
            } else {
                $masterID = self::getMasterLevelOfReportTemplate($detail->masterID);
            }
        }

        return $masterID;
    }

    public static function dataTableSortOrder($input): string
    {
        $sort = 'desc';
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        }

        return $sort;
    }

    // check wether the item assgined to the company
    public static function IsItemAssigned($item_code, $company_id)
    {
        $itemAssigned =  Models\ItemAssigned::where('companySystemID', $company_id)->where('itemCodeSystem', $item_code)->get();
        if ($itemAssigned) {
            return true;
        }
        return false;
    }

    public static function bytesToHuman($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function customerLedgerReportSum($reportData, $type){
        $sumAmount = 0;

        if($type == 'invoice'){
            foreach($reportData as $data => $value){
                $amount = collect($value->invoiceAmount)->sum();
                $sumAmount = $sumAmount + $amount;
            }
        }

        if($type == 'paid'){
            foreach($reportData as $data => $value){
                $amount = collect($value->paidAmount)->sum();
                $sumAmount = $sumAmount + $amount;
            }
        }

        if($type == 'balance'){
            foreach($reportData as $data => $value){
                $amount = collect($value->balanceAmount)->sum();
                $sumAmount = $sumAmount + $amount;
            }
        }



        if($sumAmount){
            return $sumAmount ;
        } else{
            $sumAmount = 0;
            return $sumAmount ;
        }


    }


    public static function rowTotalOfReportTemplate($companyHeaderData, $columns, $data)
    {
        $total = 0;

        foreach ($companyHeaderData as $key1 => $company) {
            foreach ($columns as $key2 => $column) {
                if (isset($data['columnData'][$company['companyCode']])) {
                    $strings = explode('-',$column);
                    if($strings[0]) {
                        if ($strings[0] == 'CYYTD') {
                            $total += $data['columnData'][$company['companyCode']][$column];
                        }
                    }
                }
            }
        }

        return $total;
    }

    public static function rowTotalOfReportTemplateBalance($companyHeaderData, $columns, $data)
    {
        $total = 0;

        foreach ($companyHeaderData as $key1 => $company) {
            foreach ($columns as $key2 => $column) {
                if (isset($data[$company['companyCode']])) {
                    $strings = explode('-',$column);
                    if($strings[0]) {
                        if ($strings[0] == 'CYYTD') {
                            $total += $data[$company['companyCode']][$key2];
                        }
                    }
                }
            }
        }

        return $total;
    }

    public static function rowTotalOfReportTemplateGrandTotal($companyHeaderData, $columns, $data)
    {
        $total = 0;

        foreach ($companyHeaderData as $key1 => $company) {
            $code = $company['companyCode'];
            foreach ($columns as $key2 => $column) {
                if (isset($data[$code])) {
                    $strings = explode('-',$column);
                    if($strings[0]){
                        if($strings[0] == 'CYYTD') {
                            $total += $data[$code]->$column;
                        }
                    }
                }
            }
        }

        return $total;
    }

    public static function grandTotalValueOfReportTemplate($code, $column, $data)
    {
        $value = 0;
        if (isset($data[$code])) {
            $value = $data[$code]->$column;
        }

        return $value;
    }

    public static function currencyConversionByER($transactionCurrencyID, $documentCurrencyID, $transactionAmount, $transER)
    {
        $trasToSuppER = 1;
        $transactionAmount = self::stringToFloat($transactionAmount);
        $documentAmount = null;
        if ($documentCurrencyID) {
            $transToDocER = $transER;

            if ($transactionCurrencyID == $documentCurrencyID) {
                $documentAmount = $transactionAmount;
            } else {
                $documentAmount = $transactionAmount * $transToDocER;
            }
        }
        $array = array(
            'documentAmount' => $documentAmount
        );

        return $array;
    }


    public static function updateSupplierRetentionAmount($bookingSuppMasInvAutoID, $bookInvSuppMaster)
    {
        $directItems = DirectInvoiceDetails::where('directInvoiceAutoID', $bookingSuppMasInvAutoID)
            ->with(['segment', 'purchase_order'])
            ->get();



        $invDetailItems = BookInvSuppDet::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
            ->with(['grvmaster', 'pomaster'])
            ->get();


        $supplierItems = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
            ->with(['unit' => function ($query) {
            }, 'vat_sub_category'])->get();

        if(count($supplierItems) == 0) {
            $supplierItems = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                ->with(['unit' => function ($query) {
                }, 'vat_sub_category'])->get();
        }

        $tot = 0;
        $vatTot = 0;
        $totalNet = 0;
        for ($i = 0; $i < count($directItems); $i++) {
            $tot += doubleval($directItems[$i]->DIAmount);
            $vatTot += doubleval($directItems[$i]->VATAmount);
        }

        for ($i = 0; $i < count($invDetailItems); $i++) {
            $tot += doubleval($invDetailItems[$i]->supplierInvoAmount);
        }

        for ($i = 0; $i < count($supplierItems); $i++) {
            $tot += doubleval($supplierItems[$i]->netAmount);
            $vatTot += doubleval($supplierItems[$i]->VATAmount) * doubleval($supplierItems[$i]->noQty);
        }

        $totalVat = $bookInvSuppMaster->rcmActivated ? 0 : $vatTot;
        $totalNet = $tot + $totalVat;
        $retentionAmount = $totalNet * $bookInvSuppMaster->retentionPercentage/100;
        $decimalPlaces = 3;
        $currency = CurrencyMaster::select('DecimalPlaces')->where('currencyID',$bookInvSuppMaster->localCurrencyID)->first();
        if ($currency) {
            $decimalPlaces = $currency->DecimalPlaces;
        }

        $retentionAmountToFixed = round($retentionAmount,$decimalPlaces);
        $bookInvSuppMaster->retentionAmount = $retentionAmountToFixed;

        if($bookInvSuppMaster->retentionAmount != 0){
            $details = SupplierInvoiceItemDetail::with(['vat_sub_category'])->where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)->get();

            $totalStdVat = 0;
            foreach ($details as $detail) {
                if ($detail->vat_sub_category && $detail->vat_sub_category->subCatgeoryType == 1) {
                    $totalStdVat += $detail->VATAmount;
                }
            }

            $retentionVatAmount = ($totalStdVat * $bookInvSuppMaster->retentionPercentage) / 100;
            $bookInvSuppMaster->retentionVatAmount = round($retentionVatAmount,$decimalPlaces);
        }

        $bookInvSuppMaster->save();
    }


    public static function checkBlockSuppliers($date,$supplier_id)
    {
        $isValidate = true;

        $isPermenentExist = SupplierBlock::where('supplierCodeSytem',$supplier_id)->where('blockType',1)->exists();
        $isPeriodExist = SupplierBlock::where('supplierCodeSytem',$supplier_id)->where('blockType',2)->exists();
        $type = $isPermenentExist ? 1 : ($isPeriodExist ? 2 : 0);

        if($type == 1)
        {
            $isValidate = false;

        }
        else if(isset($date) && $type == 2)
        {
            $date =  ((new Carbon(($date)))->format('Y-m-d'));
            $check_date = Carbon::parse($date);


            $withinDateRanges = SupplierBlock::where('supplierCodeSytem',$supplier_id)->where('blockType',2)->where('blockFrom', '<=', $check_date)
                ->where('blockTo', '>=', $check_date)
                ->exists();


            if ($withinDateRanges) {
                $isValidate = false;

            }

        }
        if(!$isValidate)
        {

            return ['success' => false, 'message' => trans('custom.selected_supplier_blocked')];
        }

        return ['success' => true, 'message' => "supplier checked successfully"];

    }


    public static function getDocumentModifyRequestDetails($autoID)
    {
        $doucumentModifyComment = DocumentModifyRequest::select('description')
            ->where('id',$autoID)
            ->first();

        return $doucumentModifyComment;
    }

    public static function checkDomainErp($document,$id)
    {
        $redirectUrl =  env("ERP_APPROVE_URL"); //ex: change url to https://*.pl.uat-gears-int.com/#/approval/erp

        if($document == 107){
            $tenantDomain = self::getSupplierRegDomain($id);
            $search = '*';
            $redirectUrl = str_replace($search, $tenantDomain, $redirectUrl);
        }else {
            if (env('IS_MULTI_TENANCY') == true) {

                $url = $_SERVER['HTTP_HOST'];
                $url_array = explode('.', $url);
                $subDomain = $url_array[0];

                $tenantDomain = (isset(explode('-', $subDomain)[0])) ? explode('-', $subDomain)[0] : "";

                $search = '*';
                $redirectUrl = str_replace($search, $tenantDomain.'-erp', $redirectUrl);
            }
        }


        $lastSlashPos = strrpos($redirectUrl, '/');
        $baseUrl = substr($redirectUrl, 0, $lastSlashPos + 1);
        $redirectUrlNew = $baseUrl . 'all-document';

        return $redirectUrlNew;
    }

    public static function getSupplierRegDomain($id){
        $supplierReg =  SupplierRegistrationLink::select('sub_domain')
            ->where('id',$id)
            ->first();

        return $supplierReg['sub_domain'];

    }

    public static function getDomainForSrmDocuments($request)
    {
        $url = $request->getHttpHost();
        $url_array = explode('.', $url);
        $subDomain = $url_array[0];
        return $subDomain;
    }

    public static function getEmailConfiguration($slug='', $defaultValue = 'GEARS')
    {
        $emailConfiguration = SystemConfigurationAttributes::where('slug', $slug)
            ->whereHas('systemConfigurationDetail')
            ->with('systemConfigurationDetail')
            ->first();

        if(!$emailConfiguration) {
            return $defaultValue;
        }

        return $emailConfiguration['systemConfigurationDetail']['value'];
    }

    public static function getTenderCircularSupplierList($tenderObj, $circularId, $id, $companySystemID){
        if (($tenderObj->tender_type_id ?? null) == 2) {
            return self::tenderSupplierAssignedList($id, $companySystemID);
        } else {
            return CircularSuppliersEditLog::select('amd_id', 'id','supplier_id','circular_id','status')
                ->with([ 'supplier_registration_link', 'srm_circular_amendments.document_attachments'])
                ->where('circular_id', $circularId)
                ->where('is_deleted', 0)
                ->get();
        }
    }

    public static function tenderSupplierAssignedList($tenderId,$companyId){
        return TenderSupplierAssignee::select('id','tender_master_id','supplier_assigned_id','mail_sent')
            ->with(['supplierAssigned'=> function ($q) use ($companyId){
                $q->select('supplierAssignedID','companySystemID','supEmail','companySystemID')
                    ->where('companySystemID',$companyId);
            }])
            ->where('mail_sent',1)
            ->where('tender_master_id',$tenderId)
            ->where('company_id',$companyId)
            ->whereHas('supplierAssigned', function ($q) use ($companyId) {
                $q->where('companySystemID', $companyId);
            })
            ->get();
    }

    public static function getCircularAttachments($amendmentsList){
        $attachments = [];
        foreach ($amendmentsList as $amendment) {
            $attachments[$amendment->document_attachments->originalFileName] = Helper::getFileUrlFromS3($amendment->document_attachments->path);
        }
        return $attachments;
    }

    public static function sendCircularEmailToSuppliers(
        $supplierList, $circular, $companySystemID,
        $attachments, $companyName, $tenderObj
    ) {
        $documentSystemIds = [108, 113];
        $documentSystemId = $tenderObj->document_system_id;
        $documentName = $documentSystemId === 108 ? 'Tender' : 'RFX';

        foreach ($supplierList as $supplier) {
            $useAssignedEmail = in_array($documentSystemId, $documentSystemIds, true)
                && $tenderObj->tender_type_id == 2;

            $email = $useAssignedEmail
                ? $supplier->supplierAssigned->supEmail
                : $supplier->supplier_registration_link->email;

            $description = $circular['description'] ?? '';
            $descriptionHtml = $description
                ? "<b>Circular Description : </b>{$description}<br /><br />"
                : '';

            $emailMessage = "Dear Supplier,<br /><br />
            Please find published <span style='text-transform: lowercase;'>{$documentName}</span> circular details below.<br /><br />
            <b>Circular Name : </b>{$circular['circular_name']}<br /><br />
            {$descriptionHtml}{$companyName}<br /><br />
            Thank You<br /><br /><b>";

            $dataEmail = [
                'empEmail' => $email,
                'companySystemID' => $companySystemID,
                'attachmentList' => $attachments,
                'emailAlertMessage' => $emailMessage,
                'alertMessage' => $documentName . ' ' . trans('email.circular'),
            ];

            Email::sendEmailErp($dataEmail);
        }
    }


    public static function updateSupplierWhtAmount($bookingSuppMasInvAutoID, $bookInvSuppMaster)
    {

        $bookInvSuppMaster = BookInvSuppMaster::with(['supplier' => function($query){
            $query->with('tax');
        }])->find($bookingSuppMasInvAutoID);

        $percentage = 0;
        if(isset($bookInvSuppMaster->supplier->tax))
        {
            $percentage = $bookInvSuppMaster->supplier->tax->whtPercentage;
        }
        if($bookInvSuppMaster['documentType'] == 0 ||  $bookInvSuppMaster['documentType'] == 2)
        {
            $isWHTApplicableSupplier = $bookInvSuppMaster->supplier->whtApplicableYN == 1?true:false;
            if( $bookInvSuppMaster->supplier->whtApplicableYN == 1)
            {
                $isWHTApplicableSupplier = $bookInvSuppMaster->whtApplicableYN == 1?true:false;
            }
            $isDetailVat = false;
            $WhtTotalAmount = 0 ;
            $isGrvApplicable = false;
            $isWHTApplicableVat = false;
            $isPoApplicable = false;
            $rcmActive = false;
            $whtTrue = true;
            $items = BookInvSuppDet::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                ->with(['grvmaster' => function($q){
                    $q->with('details');
                }, 'pomaster','suppinvmaster'=>function($q){
                    $q->select('bookingSuppMasInvAutoID','documentType');
                }])
                ->get();


            foreach ($items as $i => $invDetailItem) {


                if (($invDetailItem->pomaster != null && $invDetailItem->pomaster->VATAmount == 0) ||( $invDetailItem->pomaster != null && $invDetailItem->pomaster->VATAmount != 0 && $invDetailItem->pomaster->rcmActivated == 1 )
                    && $bookInvSuppMaster['documentType'] == 0
                ) {
                    $isPoApplicable = true;
                    $WhtTotalAmount += $invDetailItem->supplierInvoAmount;
                }

                if ($invDetailItem->grvmaster != null && $bookInvSuppMaster['documentType'] == 2) {
                    $isGrvApp = true;
                    foreach ($invDetailItem->grvmaster->details as $k => $detail) {
                        if ($detail->VATAmount != 0) {
                            $isGrvApp = false;
                        }
                    }
                    if ($isGrvApp) {
                        $isGrvApplicable = true;
                        $WhtTotalAmount += $invDetailItem->supplierInvoAmount;
                    }
                }


                if($bookInvSuppMaster['documentType'] == 0 ||  $bookInvSuppMaster['documentType'] == 1)
                {
                    if($invDetailItem->pomaster != null && $invDetailItem->pomaster->rcmActivated == 1)
                    {
                        $rcmActive = true;
                    }
                }


            }


            if($isGrvApplicable &&  ($bookInvSuppMaster['documentType'] == 0 ||  $bookInvSuppMaster['documentType'] == 2))
            {
                $isWHTApplicableVat = true;
                $isDetailVat = true;
            }

            if($isPoApplicable &&  ($bookInvSuppMaster['documentType'] == 0 ||  $bookInvSuppMaster['documentType'] == 2))
            {
                $isWHTApplicableVat = true;
                $isDetailVat = true;
            }

            if(count($items) == 0 && ($bookInvSuppMaster['documentType'] == 0 ||  $bookInvSuppMaster['documentType'] == 2))
            {
                $isWHTApplicableVat = true;
                $isDetailVat = false;
            }

            if($bookInvSuppMaster['documentType'] == 0)
            {
                if(($isWHTApplicableVat == true && $isWHTApplicableSupplier == true) || ($isWHTApplicableSupplier == true && $rcmActive == true))
                {
                    $whtTrue = true;
                }
                else
                {
                    $whtTrue = false;
                }
            }

            if($bookInvSuppMaster['documentType'] == 2)
            {
                if(($isWHTApplicableVat == true && $isWHTApplicableSupplier == true))
                {
                    $whtTrue = true;
                }
                else
                {
                    $whtTrue = false;
                }
            }

            $amount = round($WhtTotalAmount*($percentage/100),2);
            $totalAmount = $whtTrue == true?$amount:0;
            $bookInvSuppMaster->whtAmount = $totalAmount;
            $bookInvSuppMaster->whtApplicable = $whtTrue;
            $bookInvSuppMaster->whtEdited = false;
            $bookInvSuppMaster->whtPercentage = $percentage;
            $bookInvSuppMaster->isWHTApplicableVat = $isDetailVat;
            $bookInvSuppMaster->save();
        }



    }


    public static function updateSupplierDirectWhtAmount($bookingSuppMasInvAutoID, $bookInvSuppMaster)
    {
        if($bookInvSuppMaster['documentType'] == 1 )
        {
            //$invmaster = BookInvSuppMaster::with('supplier')->find($bookingSuppMasInvAutoID);

            $invmaster = BookInvSuppMaster::with(['supplier' => function($query){
                $query->with('tax');
            }])->find($bookingSuppMasInvAutoID);

            $percentage = 0;
            if(isset($invmaster->supplier->tax))
            {
                $percentage = $invmaster->supplier->tax->whtPercentage;
            }

            if(isset($invmaster->supplier))
            {
                $isWHTApplicableSupplier = $invmaster->supplier->whtApplicableYN == 1?true:false;
                if($isWHTApplicableSupplier)
                {
                    $isWHTApplicableSupplier = $invmaster->whtApplicableYN == 1?true:false;
                }
            }
            else
            {
                $isWHTApplicableSupplier = false;
            }

          

            $whtTotalAmountDirect = 0;

            $directItems = DirectInvoiceDetails::where('directInvoiceAutoID', $bookingSuppMasInvAutoID)
                ->with(['segment', 'purchase_order','chartofaccount'])
                ->get();

            foreach ($directItems as $index => $item) {


                if ($item->VATAmount != 0) {
                    if ($invmaster->rcmActivated && $invmaster->documentType == 1) {
                        if ($item->whtEdited == 0) {
                            $item->whtAmount = $item->netAmount * ($percentage / 100);
                        }
                        if ($invmaster->documentType == 1 && $invmaster->whtApplicable == true) {
                            $whtTotalAmountDirect += $item->whtAmount;
                        }

                        if ($invmaster->whtApplicable == false) {
                            $item->whtApplicable = false;
                            $item->whtAmount = 0;
                        }
                    } else {
                        $item->whtApplicable = false;
                        $item->whtAmount = 0;
                    }
                } else {



                    if ($invmaster->whtApplicable == false) {
                        $item->whtApplicable = false;
                        $item->whtAmount = 0;
                    } else {
                        $isWhtapp = true;
                        $item->whtApplicable = true;
                        if ($item->whtEdited == 0) {
                            $item->whtAmount = $item->netAmount * ($percentage / 100);
                        }
                        if ($invmaster->documentType == 1 && $invmaster->whtApplicable == true) {
                            $whtTotalAmountDirect += $item->whtAmount;
                        }


                    }
                }


                if ($invmaster->rcmActivated && $invmaster->documentType == 1 && $invmaster->whtApplicable == true) {
                    $item->whtApplicable = true;
                }

                DirectInvoiceDetails::where('directInvoiceDetailsID', $item->directInvoiceDetailsID)->update([
                    'whtAmount' => $item->whtAmount,
                    'whtApplicable' => $item->whtApplicable,
                ]);

            }

            $invmaster->whtAmount = $whtTotalAmountDirect;
            $invmaster->whtPercentage = $percentage;
            // $bookInvSuppMaster->whtEdited = false;
            $invmaster->save();
        }

    }


    public static function updateSupplierItemWhtAmount($bookingSuppMasInvAutoID, $bookInvSuppMaster)
    {
        if($bookInvSuppMaster['documentType'] == 3)
        {
            //$invmaster = BookInvSuppMaster::with('supplier')->find($bookingSuppMasInvAutoID);

            $invmaster = BookInvSuppMaster::with(['supplier' => function($query){
                $query->with('tax');
            }])->find($bookingSuppMasInvAutoID);

            $percentage = 0;
            if(isset($invmaster->supplier->tax))
            {
                $percentage = $invmaster->supplier->tax->whtPercentage;
            }
            if(isset($invmaster->supplier))
            {
                $isWHTApplicableSupplier = $invmaster->supplier->whtApplicableYN == 1?true:false;
                if( $invmaster->supplier->whtApplicableYN == 1)
                {
                    $isWHTApplicableSupplier = $invmaster->whtApplicableYN == 1?true:false;
                }

                $whtTotalAmountDirect = 0;

                $items = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                    ->with(['unit' => function ($query) {
                    }, 'vat_sub_category'])->get();

                foreach ($items as $index => $item) {
                    if ($item->VATAmount != 0) {
                        $item->whtApplicable = false;
                        $item->whtAmount = 0;
                    } else {
                        if ($invmaster->whtApplicable == false) {
                            $item->whtApplicable = false;
                            $item->whtAmount = 0;
                        } else {
                            $isWhtapp = true;
                            $item->whtApplicable = true;
                            if ($item->whtEdited == 0) {
                                $item->whtAmount = $item->netAmount * ($percentage  / 100);
                            }
                            if ($invmaster->documentType == 3 && $invmaster->whtApplicable == true) {
                                $whtTotalAmountDirect += $item->whtAmount;
                            }
                        }
                    }

                    SupplierInvoiceDirectItem::where('id', $item->id)->update([
                        'whtAmount' => $item->whtAmount,
                        'whtApplicable' => $item->whtApplicable,
                    ]);
                }

                $invmaster->whtAmount = $whtTotalAmountDirect;
                $invmaster->whtPercentage = $percentage;
                // $bookInvSuppMaster->whtApplicable = $whtTrue;
                // $bookInvSuppMaster->whtEdited = false;
                $invmaster->save();
            }    
        }

    }

    public static function generateSRMUuid($length=16) : string
    {
        return bin2hex(random_bytes($length));
    }


    public static function getDefaultBirthdayTemplate()
    {
        return BirthdayTemplate::select('template', 'client_code', 'image_path')
            ->where('is_default', 1)
            ->first();
    }
    public static function getArrayIds($data_array)
    {
        return collect($data_array)->pluck('id')->filter()->values()->all();
    }

    public static function validateCurrencyRate($companyId, $transactionCurrencyId)
    {
        $company = Models\Company::find($companyId);

        if (!$company) {
            return false;
        }

        $localRate = Models\CurrencyConversion::where([
            ['masterCurrencyID', '=', $transactionCurrencyId],
            ['subCurrencyID', '=', $company->localCurrencyID]
        ])->value('conversion');

        $reportingRate = Models\CurrencyConversion::where([
            ['masterCurrencyID', '=', $transactionCurrencyId],
            ['subCurrencyID', '=', $company->reportingCurrency]
        ])->value('conversion');

        return $localRate > 0 && $reportingRate > 0;
    }

    /**
     * Get mPDF configuration with language-specific font settings
     *
     * @param array $additionalConfig Additional configuration to merge
     * @param string|null $lang Language code (e.g., 'en', 'ar'). If null, uses app locale
     * @return array mPDF configuration array
     */
    public static function getMpdfConfig($additionalConfig = [], $lang = null)
    {
        // Get language from parameter or app locale
        if ($lang === null) {
            $lang = app()->getLocale();
        }

        // Get base config from config file
//        $baseConfig = config('mpdf', []);

        // Set default font based on language
//        if ($lang === 'ar') {
//            $baseConfig['default_font'] = 'notosansarabic';
//        } else {
//            $baseConfig['default_font'] = 'poppins';
//        }

        // Merge with additional config (additional config takes precedence)
//        return array_merge($baseConfig, $additionalConfig);
        return $additionalConfig;
    }

    public static function getExcelFontFamily($lang = null)
    {
        // Get language from parameter or app locale
        if ($lang === null) {
            $lang = app()->getLocale();
        }

        // Return appropriate font based on language
        // Using exact font names as they appear in Excel
        if ($lang === 'ar') {
            // Try 'Noto Sans Arabic' first, Excel will fallback if not available
            return 'Noto Sans Arabic';
        } else {
            // Try 'Poppins' first, Excel will fallback if not available
            return 'Poppins';
        }
    }

}
