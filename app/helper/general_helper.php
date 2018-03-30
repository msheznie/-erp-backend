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
     * @param $params : accept parameters as an array
     * $param 1-documentSystemID : autoID
     * $param 2-company : company
     * $param 3-document : document
     * $param 4-segment : segment
     * $param 5-segment : department
     * $param 6-category : category
     * $param 7-amount : amount
     */
    public static function confirmDocument($params)
    {
        /** check document is already confirmed*/
        $docInforArr = array('documentCodeColumnName' => '', 'confirmColumnName' => '', 'confirmedBy' => '', 'confirmedBySystemID' => '', 'confirmedDate' => '','tableName' => '','modelName' => '','primarykey' => '');
        switch ($params["document"]) {
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

        $namespacedModel = 'App\Models\\'.$docInforArr["modelName"]; // Model name
        $isConfirm = $namespacedModel::where($docInforArr["primarykey"],$params["autoID"])->where($docInforArr["confirmColumnName"], 1)->first();
        if (!$isConfirm) {
             $empInfo = self::getEmployeeInfo(); // get current employee detail
             //$updateConfirm = $namespacedModel::find($params["autoID"])->update([$docInforArr["confirmColumnName"] => 1,$docInforArr["confirmedBy"] => $empInfo->empID, $docInforArr["confirmedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["confirmedDate"] => now()]);
             $approvalLevel = Models\ApprovalLevel::where('companySystemID', $params["company"])->where('documentSystemID', $params["document"])->where('isActive', -1)->first();
             if ($approvalLevel) {

                 $isSegmentWise = $approvalLevel->serviceLineWise;
                 $isCategoryWise = $approvalLevel->isCategoryWiseApproval;
                 $isValueWise = $approvalLevel->valueWise;

                 DB::enableQueryLog();
                 $approvalLevel = Models\ApprovalLevel::with('approvalrole')->where('companySystemID', $params["company"])->where('documentSystemID', $params["document"]);
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

                 $output = $approvalLevel->get();
                 //dd(DB::getQueryLog());
                 return $output;
                 $sorceDocument = $namespacedModel::find($params["autoID"]);   /** get sorce document master record*/
                 $documentApproved = [];
                    if ($output) {
                        if ($output->approvalRole) {
                            foreach ($output->approvalRole as $val) {
                                $documentApproved[] = array('companySystemID' => $val->companySystemID, 'companyID' => $val->companyID, 'departmentSystemID' => $val->departmentSystemID, 'departmentID' => $val->departmentID, 'serviceLineSystemID' => $val->serviceLineSystemID, 'serviceLineCode' => $val->serviceLineCode, 'documentSystemID' => $val->documentSystemID, 'documentID' => $val->documentID, 'documentSystemCode' => $params["documentSystemID"], 'documentCode' => $sorceDocument[$docInforArr["documentCodeColumnName"]], 'approvalLevelID' => $val->approvalLevelID, 'rollID' => $val->rollMasterID, 'approvalGroupID' => $val->approvalGroupID, 'rollLevelOrder' => $val->rollLevel, 'docConfirmedDate' => now(), 'docConfirmedByEmpID' => Auth::id());
                            }
                        } else {
                            return self::sendError('No approval setup created for this document');
                        }
                    }
                    $insertDocumentApproved = Models\DocumentApproved::insert($documentApproved);
                    return self::sendResponse(array(),'Successfully document confirmed');
                } else {
                 return self::sendError('No approval setup created for this document');
             }
         } else {
             return self::sendError('Document is already confirmed');
         }

    }

    public static function getEmployeeInfo()
    {
        //$user = Models\User::find(Auth::id());
        $user = Models\User::find(11);
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
