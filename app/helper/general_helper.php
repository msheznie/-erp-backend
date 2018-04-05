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
 */

namespace App\helper;

use App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use InfyOm\Generator\Utils\ResponseUtil;

class Helper
{
    public static function getAllDocuments()
    {
        $document = Models\DocumentMaster::all();
        return $document;
    }

    public static function getCompanyServiceline($company)
    {
        $serviceline = Models\SegmentMaster::where('companySystemID', '=', $company)->get();
        return $serviceline;
    }

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
     * a common function to confirm document with approval creation
     * @param $params : accept parameters as an array
     * $param 1-documentSystemID : autoID
     * $param 2-company : company
     * $param 3-document : document
     * $param 4-segment : segment
     * $param 5-department : department
     * $param 6-category : category
     * $param 7-amount : amount
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
                    $docInforArr["confirmedBy"] = 'confirmedEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedEmpDate';
                    $docInforArr["tableName"] = 'chartofaccounts';
                    $docInforArr["modelName"] = 'ChartOfAccount';
                    $docInforArr["primarykey"] = 'chartOfAccountSystemID';
                    break;
                default:
                    return ['success' => false, 'message' => 'Document ID not found'];
            }

            $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
            $masterRec = $namespacedModel::find($params["autoID"]);
            if ($masterRec) {
                //check document is already confirmed
                $isConfirm = $namespacedModel::where($docInforArr["primarykey"], $params["autoID"])->where($docInforArr["confirmColumnName"], 1)->first();
                if (!$isConfirm) {
                    // get current employee detail
                    $empInfo = self::getEmployeeInfo();
                    //confirm the document
                    $updateConfirm = $masterRec->update([$docInforArr["confirmColumnName"] => 1, $docInforArr["confirmedBy"] => $empInfo->empName, $docInforArr["confirmedByEmpID"] => $empInfo->empID, $docInforArr["confirmedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["confirmedDate"] => now()]);
                    //get the policy
                    $policy = Models\CompanyDocumentAttachment::where('companySystemID', $params["company"])->where('documentSystemID', $params["document"])->first();
                    if ($policy) {
                        $isSegmentWise = $policy->isServiceLineApproval;
                        $isCategoryWise = $policy->isCategoryApproval;
                        $isValueWise = $policy->isAmountApproval;
                    } else {
                        return ['success' => false, 'message' => 'Policy not available for this document.'];
                    }

                    // get approval rolls
                    $approvalLevel = Models\ApprovalLevel::with('approvalrole')->where('companySystemID', $params["company"])->where('documentSystemID', $params["document"])->where('isActive', -1);
                    if ($isSegmentWise) {
                        if (array_key_exists('segment', $params)) {
                            if ($params["segment"]) {
                                $approvalLevel->where('serviceLineSystemID', $params["segment"]);
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
                            } else {
                                return ['success' => false, 'message' => 'No approval setup created for this document'];
                            }
                        } else {
                            return ['success' => false, 'message' => 'Category parameter are missing'];
                        }
                    }

                    if ($isValueWise) {
                        if (array_key_exists('amount', $params)) {
                            if ($params["amount"]) {
                                $amount = $params["amount"];
                                $approvalLevel->where(function ($query) use ($amount) {
                                    $query->where('valueFrom', '<=', $amount);
                                    $query->where('valueTo', '>=', $amount);
                                });
                            } else {
                                return ['success' => false, 'message' => 'No approval setup created for this document'];
                            }
                        } else {
                            return ['success' => false, 'message' => 'Amount parameter are missing'];
                        }
                    }
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
                                    } else {
                                        return ['success' => false, 'message' => 'Please set the approval group'];
                                    }
                                }
                            } else {
                                return ['success' => false, 'message' => 'No approval setup created for this document'];
                            }
                        }
                        // insert rolls to document approved table
                        $insertDocumentApproved = Models\DocumentApproved::insert($documentApproved);
                        DB::commit();
                        return ['success' => true, 'message' => 'Successfully document confirmed'];
                    } else {
                        return ['success' => false, 'message' => 'No approval setup created for this document'];
                    }
                } else {
                    return ['success' => false, 'message' => 'Document is already confirmed'];
                }
            } else {
                return ['success' => false, 'message' => 'No record found'];
            }
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            //dd($e);
            return ['success' => false, 'message' => $e . 'Error Ocurred'];
        }
    }

    /**
     * function to get conversion rate by company,supplier and bankaccount
     * @param $companySystemID - company
     * @param $transactionCurrencyID - transaction currency
     * @param $documentCurrency - document currency
     * @param null $supplierSystemID - supplier
     * @param null $bankAccountAutoID - bank
     * @return trasToLocER,trasToRptER,trasToSuppER,transToBankER
     */
    public static function currencyConversion($companySystemID, $transactionCurrencyID, $documentCurrency, $supplierSystemID = null, $bankAccountAutoID = null)
    {
        $locaCurrencyID = null;
        $reportingCurrencyID = null;
        $supplierCurrencyID = null;
        $bankAccountCurrencyID = null;

        $trasToSuppER = null;
        $trasToLocER = null;
        $trasToRptER = null;
        $transToBankER = null;
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
            }
        }
        // get supplier currency conversion
        if ($supplierSystemID) {
            $supplierCurrency = Models\SupplierMaster::find($supplierSystemID);
            if ($supplierCurrency) {
                $supplierCurrencyID = $supplierCurrency->currencyID;
                $conversion = Models\CurrencyConversion::where('masterCurrencyID', $transactionCurrencyID)->where('subCurrencyID', $supplierCurrencyID)->first();
                $trasToSuppER = $conversion->conversion;
            }
        }

        // get bank currency conversion
        if ($bankAccountAutoID) {
            $bankCurrency = Models\BankAccount::find($bankAccountAutoID);
            if ($bankCurrency) {
                $bankAccountCurrencyID = $bankCurrency->accountCurrencyID;
                $conversion = Models\CurrencyConversion::where('masterCurrencyID', $transactionCurrencyID)->where('subCurrencyID', $bankAccountCurrencyID)->first();
                $transToBankER = $conversion->conversion;
            }
        }

        return self::sendResponse(array('trasToLocER' => $trasToLocER, 'trasToRptER' => $trasToRptER, 'trasToSuppER' => $trasToSuppER, 'transToBankER' => $transToBankER), "Record retrieved");
    }


    /**
     * function to approve documents
     * @param $input - get line record
     * @return mixed
     */
    public static function approveDocument($input)
    {
        $docInforArr = array('tableName' => '', 'modelName' => '', 'primarykey' => '', 'approvedColumnName' => '', 'approvedBy' => '', 'approvedBySystemID' => '', 'approvedDate' => '');
        switch ($input["documentSystemID"]) { // check the document id and set relavant parameters
            case 57:
                $docInforArr["tableName"] = 'itemmaster';
                $docInforArr["modelName"] = 'ItemMaster';
                $docInforArr["primarykey"] = 'itemCodeSystem';
                $docInforArr["approvedColumnName"] = 'itemApprovedYN';
                $docInforArr["approvedBy"] = 'itemApprovedBy';
                $docInforArr["approvedBySystemID"] = 'itemApprovedBySystemID';
                $docInforArr["approvedDate"] = 'itemApprovedDate';
                break;
            case 56:
                $docInforArr["tableName"] = 'suppliermaster';
                $docInforArr["modelName"] = 'SupplierMaster';
                $docInforArr["primarykey"] = 'supplierCodeSystem';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedby';
                $docInforArr["approvedBySystemID"] = 'approvedEmpSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                break;
            case 58:
                $docInforArr["tableName"] = 'customermaster';
                $docInforArr["modelName"] = 'CustomerMaster';
                $docInforArr["primarykey"] = 'customerCodeSystem';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["approvedBy"] = 'approvedEmpID';
                $docInforArr["approvedBySystemID"] = 'approvedEmpSystemID';
                $docInforArr["approvedDate"] = 'approvedDate';
                break;
            default:
                return ['success' => false, 'message' => 'Document ID not found'];
        }

        DB::beginTransaction();
        try {
            $docApproved = Models\DocumentApproved::find($input["documentApprovedID"]);
            if ($docApproved) {
                $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
                //check document is already approved
                $isApproved = Models\DocumentApproved::where('documentApprovedID', $input["documentApprovedID"])->where('approvedYN', -1)->first();
                if (!$isApproved) {
                    $approvalLevel = Models\ApprovalLevel::find($input["approvalLevelID"]);
                    if ($approvalLevel) {
                        // get current employee detail
                        $empInfo = self::getEmployeeInfo();
                        if ($approvalLevel->rollLevelOrder == $input["rollLevelOrder"]) { // update the document after the final approval
                            $finalupdate = $namespacedModel::find($input["documentSystemCode"])->update([$docInforArr["approvedColumnName"] => 1, $docInforArr["approvedBy"] => $empInfo->empID, $docInforArr["approvedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["approvedDate"] => now()]);
                        }
                        // update roll level in master table
                        $rollLevelUpdate = $namespacedModel::find($input["documentSystemCode"])->update(['RollLevForApp_curr' => $input["rollLevelOrder"] + 1]);
                        // update record in document approved table
                        $approvedeDoc = $docApproved::find($input["documentApprovedID"])->update(['approvedYN' => -1, 'approvedDate' => now(), 'approvedComments' => $input["approvedComments"], 'employeeID' => $empInfo->empID, 'employeeSystemID' => $empInfo->employeeSystemID]);
                    } else {
                        return ['success' => false, 'message' => 'Approval level not found'];
                    }
                    DB::commit();
                    return ['success' => true, 'message' => 'Document is successfully approved'];
                } else {
                    return ['success' => false, 'message' => 'Document is already approved'];
                }
            } else {
                return ['success' => false, 'message' => 'No records found'];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e . 'Error Ocurred'];
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
            //check document is already rejected
            $isRejected = Models\DocumentApproved::where('documentApprovedID', $input["documentApprovedID"])->where('rejectedYN', -1)->first();
            if (!$isRejected) {
                $approvalLevel = Models\ApprovalLevel::find($input["approvalLevelID"]);
                if ($approvalLevel) {
                    // get current employee detail
                    $empInfo = self::getEmployeeInfo();
                    // update record in document approved table
                    $approvedeDoc = Models\DocumentApproved::find($input["documentApprovedID"])->update(['rejectedYN' => -1, 'rejectedDate' => now(), 'rejectedComments' => $input["rejectedComments"], 'employeeID' => $empInfo->empID, 'employeeSystemID' => $empInfo->employeeSystemID]);
                } else {
                    return ['success' => false, 'message' => 'Approval level not found'];
                }
                DB::commit();
                return ['success' => true, 'message' => 'Document is successfully rejected'];
            } else {
                return ['success' => false, 'message' => 'Document is already rejected'];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e . 'Error Ocurred'];
        }
    }

    public static function getEmployeeInfo()
    {
        $user = Models\User::find(Auth::id());
        $employee = Models\Employee::find($user->employee_id);
        return $employee;
    }

    public static function getEmployeeSystemID()
    {
        $user = Models\User::find(Auth::id());
        return $user->employee_id;
    }

    public static function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    public static function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }
}
