<?php

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

    /**
     * a common function to confirm document with approval creation
     * @param $params : accept parameters as an array
     * $param 1-documentSystemID : autoID
     * $param 2-company : company
     * $param 3-document : document
     * $param 4-segment : segment
     * $param 5-segment : department
     * $param 6-category : category
     * $param 7-amount : amount
     * no return values
     */
    public static function confirmDocument($params)
    {
        /** check document is already confirmed*/
        DB::beginTransaction();

        try {
            $docInforArr = array('documentCodeColumnName' => '', 'confirmColumnName' => '', 'confirmedBy' => '', 'confirmedBySystemID' => '', 'confirmedDate' => '', 'tableName' => '', 'modelName' => '', 'primarykey' => '');
            switch ($params["document"]) { // check the document id and set relavant parameters
                case 1:
                    $docInforArr["documentCodeColumnName"] = 'purchaseRequestCode';
                    $docInforArr["confirmColumnName"] = 'PRConfirmedYN';
                    $docInforArr["confirmedBy"] = 'PRConfirmedBy';
                    $docInforArr["confirmedBySystemID"] = 'PRConfirmedBySystemID';
                    $docInforArr["confirmedDate"] = 'PRConfirmedDate';
                    $docInforArr["tableName"] = 'erp_purchaserequest';
                    $docInforArr["modelName"] = 'PurchaseRequest';
                    $docInforArr["primarykey"] = 'purchaseRequestID';
                    break;
                case 2:
                    $docInforArr["documentCodeColumnName"] = 'purchaseOrderCode';
                    $docInforArr["confirmColumnName"] = 'poConfirmedYN';
                    $docInforArr["confirmedBy"] = 'poConfirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'poConfirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'poConfirmedDate';
                    $docInforArr["tableName"] = 'erp_purchaseordermaster';
                    $docInforArr["modelName"] = 'ProcumentOrder';
                    $docInforArr["primarykey"] = 'purchaseOrderID';
                    break;
                case 3:
                    $docInforArr["documentCodeColumnName"] = 'purchaseOrderCode';
                    $docInforArr["confirmColumnName"] = 'poConfirmedYN';
                    $docInforArr["confirmedBy"] = 'poConfirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'poConfirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'poConfirmedDate';
                    $docInforArr["tableName"] = 'erp_grvmaster';
                    $docInforArr["modelName"] = 'ProcumentOrder';
                    $docInforArr["primarykey"] = 'grvAutoID';
                    break;
                default:
            }

            $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
            //check document is already confirmed
            $isConfirm = $namespacedModel::where($docInforArr["primarykey"], $params["autoID"])->where($docInforArr["confirmColumnName"], 1)->first();
            if (!$isConfirm) {
                // get current employee detail
                $empInfo = self::getEmployeeInfo();
                //confirm the document
                $updateConfirm = $namespacedModel::find($params["autoID"])->update([$docInforArr["confirmColumnName"] => 1, $docInforArr["confirmedBy"] => $empInfo->empID, $docInforArr["confirmedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["confirmedDate"] => now()]);
                $policy = Models\CompanyDocumentAttachment::where('companySystemID', $params["company"])->where('documentSystemID', $params["document"])->first();
                $isSegmentWise = $policy->isServiceLineApproval;
                $isCategoryWise = $policy->isCategoryApproval;
                $isValueWise = $policy->isAmountApproval;

                // get approval rolls
                $approvalLevel = Models\ApprovalLevel::with('approvalrole')->where('companySystemID', $params["company"])->where('documentSystemID', $params["document"])->where('isActive', -1);
                if ($isSegmentWise) {
                    if ($params["segment"]) {
                        $approvalLevel->where('serviceLineSystemID', $params["segment"]);
                    } else {
                        return self::sendError('No approval setup created for this document');
                    }
                }

                if ($isCategoryWise) {
                    if ($params["category"]) {
                        $approvalLevel->where('categoryID', $params["category"]);
                    } else {
                        return self::sendError('No approval setup created for this document');
                    }
                }

                if ($isValueWise) {
                    if ($params["amount"]) {
                        $amount = $params["amount"];
                        $approvalLevel->where(function ($query) use ($amount) {
                            $query->where('valueFrom', '<=', $amount);
                            $query->where('valueTo', '>=', $amount);
                        });
                    } else {
                        return self::sendError('No approval setup created for this document');
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
                                $documentApproved[] = array('companySystemID' => $val->companySystemID, 'companyID' => $val->companyID, 'departmentSystemID' => $val->departmentSystemID, 'departmentID' => $val->departmentID, 'serviceLineSystemID' => $val->serviceLineSystemID, 'serviceLineCode' => $val->serviceLineID, 'documentSystemID' => $val->documentSystemID, 'documentID' => $val->documentID, 'documentSystemCode' => $params["autoID"], 'documentCode' => $sorceDocument[$docInforArr["documentCodeColumnName"]], 'approvalLevelID' => $val->approvalLevelID, 'rollID' => $val->rollMasterID, 'approvalGroupID' => $val->approvalGroupID, 'rollLevelOrder' => $val->rollLevel, 'docConfirmedDate' => now(), 'docConfirmedByEmpID' => Auth::id());
                            }
                        } else {
                            return self::sendError('No approval setup created for this document');
                        }
                    }
                    // insert rolls to document approved table
                    $insertDocumentApproved = Models\DocumentApproved::insert($documentApproved);
                    DB::commit();
                    return self::sendResponse(array(), 'Successfully document confirmed');
                } else {
                    return self::sendError('No approval setup created for this document');
                }
            } else {
                return self::sendError('Document is already confirmed');
            }
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            //dd($e);
            return self::sendError('Error Ocurred');
        }
    }

    /**
     * function to get conversion rate by company,supplier and bankaccount
     * @param $companySystemID - company
     * @param null $supplierSystemID - supplier
     * @param null $bankAccountAutoID - bank
     * return trasToLocER,trasToRptER,trasToSuppER,transToBankER
     */
    public static function currencyConversion($companySystemID,$transactionCurrencyID, $supplierSystemID = null, $bankAccountAutoID = null)
    {
        $locaCurrencyID = null;
        $reportingCurrencyID = null;
        $supplierCurrencyID  = null;
        $bankAccountCurrencyID   = null;

        $trasToSuppER = null;
        $trasToLocER  = null;
        $trasToRptER  = null;
        $transToBankER   = null;
        // get company local and reporting currency conversion
        if($companySystemID) {
            $companyCurrency = Models\Company::find($companySystemID);
            if ($companyCurrency) {
                $locaCurrencyID = $companyCurrency->localCurrencyID;
                $reportingCurrencyID = $companyCurrency->reportingCurrency;
                $conversion = Models\CurrencyConversion::where('masterCurrencyID',$transactionCurrencyID)->where('subCurrencyID',$locaCurrencyID)->first();
                $trasToLocER = $conversion->conversion;

                $conversion = Models\CurrencyConversion::where('masterCurrencyID',$transactionCurrencyID)->where('subCurrencyID',$reportingCurrencyID)->first();
                $trasToRptER = $conversion->conversion;
            }
        }
        // get supplier currency conversion
        if($supplierSystemID) {
            $supplierCurrency = Models\SupplierMaster::find($supplierSystemID);
            if ($supplierCurrency) {
                $supplierCurrencyID = $supplierCurrency->currencyID;
                $conversion = Models\CurrencyConversion::where('masterCurrencyID',$transactionCurrencyID)->where('subCurrencyID',$supplierCurrencyID)->first();
                $trasToSuppER = $conversion->conversion;
            }
        }

        // get bank currency conversion
        if($bankAccountAutoID) {
            $bankCurrency = Models\BankAccount::find($bankAccountAutoID);
            if ($bankCurrency) {
                $bankAccountCurrencyID = $bankCurrency->accountCurrencyID;
                $conversion = Models\CurrencyConversion::where('masterCurrencyID',$transactionCurrencyID)->where('subCurrencyID',$bankAccountCurrencyID)->first();
                $transToBankER = $conversion->conversion;
            }
        }

        return self::sendResponse(array('trasToLocER' => $trasToLocER,'trasToRptER' => $trasToRptER,'trasToSuppER' => $trasToSuppER,'transToBankER' => $transToBankER),"Record retrieved");
    }

    public static function getEmployeeInfo()
    {
        $user = Models\User::find(Auth::id());
        $employee = Models\Employee::find($user->employee_id);
        return $employee;
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
