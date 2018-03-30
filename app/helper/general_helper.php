<?php

namespace App\helper;

use App\Models;
use Illuminate\Support\Facades\Auth;
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
     * $param 5-category : category
     * $param 6-amount : amount
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
                $docInforArr["modelName"] = '';
                $docInforArr["primarykey"] = 'purchaseOrderID';
                break;
            default:
        }

        return $docInforArr;
        $namespacedModel = '\\Model\\'.$params["model"]; // Model name
        $isConfirm = $namespacedModel::find($params["autoID"])->where($docInforArr["confirmColumnName"], -1)->exists();
         if (!$isConfirm) {
             $empInfo = self::getEmployeeInfo(); // get current employee detail
             $confirmDoc = $namespacedModel::find($params["autoID"]);
             $confirmDoc->$docInforArr["confirmColumnName"] = -1;
             $confirmDoc->$docInforArr["confirmedBy"] = $empInfo->empID;
             $confirmDoc->$docInforArr["confirmedBySystemID"] = $empInfo->employeeSystemID;
             $confirmDoc->$docInforArr["confirmedDate"] = now();
             $confirmDoc->save();

             $approvalLevel = Models\ApprovalLevel::where('companySystemID', $params["company"])->where('documentSystemID', $params["document"])->where('isActive', -1)->first();
             if ($approvalLevel) {
                 $approvalLevel = Models\ApprovalLevel::with('approvalrole')->where('companySystemID', $params["company"])->where('documentSystemID', $params["document"]);
                 $isSegmentWise = $approvalLevel->serviceLineWise;
                 $isCategoryWise = $approvalLevel->isCategoryWiseApproval;
                 $isValueWise = $approvalLevel->valueWise;

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
                 $sorceDocument = $namespacedModel.$params["model"]::find($params["autoID"]);   /** get sorce document master record*/
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
        $employee = Models\Employee::find(Auth::id());
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
