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

use App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $companiesByGroup="";
        if(self::checkIsCompanyGroup($company)){
            $companiesByGroup = self::getGroupCompany($company);
        }else{
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
                    $docInforArr["confirmColumnName"]      = 'ConfirmedYN';
                    $docInforArr["confirmedBy"]            = 'confirmedEmpName';
                    $docInforArr["confirmedByEmpID"] = 'ConfirmedBy';
                    $docInforArr["confirmedBySystemID"] = 'ConfirmedBySystemID';
                    $docInforArr["confirmedDate"] = 'ConfirmedDate';
                    $docInforArr["tableName"] = 'erp_request';
                    $docInforArr["modelName"] = 'MaterielRequest';
                    $docInforArr["primarykey"] = 'RequestID';
                    break;
                case 3:
                    $docInforArr["documentCodeColumnName"] = 'grvPrimaryCode';
                    $docInforArr["confirmColumnName"]      = 'grvConfirmedYN';
                    $docInforArr["confirmedBy"]            = 'grvConfirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'grvConfirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'grvConfirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'grvConfirmedDate';
                    $docInforArr["tableName"] = 'erp_grvmaster';
                    $docInforArr["modelName"] = 'GRVMaster';
                    $docInforArr["primarykey"] = 'grvAutoID';
                    break;
                case 8:
                    $docInforArr["documentCodeColumnName"] = 'itemIssueCode';
                    $docInforArr["confirmColumnName"]      = 'confirmedYN';
                    $docInforArr["confirmedBy"]            = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_itemissuemaster';
                    $docInforArr["modelName"] = 'ItemIssueMaster';
                    $docInforArr["primarykey"] = 'itemIssueAutoID';
                    break;
                case 12:
                    $docInforArr["documentCodeColumnName"] = 'itemReturnCode';
                    $docInforArr["confirmColumnName"]      = 'confirmedYN';
                    $docInforArr["confirmedBy"]            = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_itemreturnmaster';
                    $docInforArr["modelName"] = 'ItemReturnMaster';
                    $docInforArr["primarykey"] = 'itemReturnAutoID';
                    break;
                case 13:
                    $docInforArr["documentCodeColumnName"] = 'stockTransferCode';
                    $docInforArr["confirmColumnName"]      = 'confirmedYN';
                    $docInforArr["confirmedBy"]            = 'confirmedByName';
                    $docInforArr["confirmedByEmpID"] = 'confirmedByEmpID';
                    $docInforArr["confirmedBySystemID"] = 'confirmedByEmpSystemID';
                    $docInforArr["confirmedDate"] = 'confirmedDate';
                    $docInforArr["tableName"] = 'erp_stocktransfer';
                    $docInforArr["modelName"] = 'StockTransfer';
                    $docInforArr["primarykey"] = 'stockTransferAutoID';
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
                    //check document is already confirmed
                    $isConfirm = $namespacedModel::where($docInforArr["primarykey"], $params["autoID"])->where($docInforArr["confirmColumnName"], 1)->first();
                    if (!$isConfirm) {
                        // get current employee detail
                        $empInfo = self::getEmployeeInfo();
                        //confirm the document
                        $masterRec->update([$docInforArr["confirmColumnName"] => 1, $docInforArr["confirmedBy"] => $empInfo->empName, $docInforArr["confirmedByEmpID"] => $empInfo->empID, $docInforArr["confirmedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["confirmedDate"] => now()]);

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
                                    return ['success' => false, 'message' => 'No attachment found'];
                                }
                            }
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
            case 3:
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
            case 8:
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
            case 9:
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
            case 12:
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
            case 13:
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
            default:
                return ['success' => false, 'message' => 'Document ID not found'];
        }
        //return ['success' => true , 'message' => $docInforArr];
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
                        if ($approvalLevel->noOfLevels == $input["rollLevelOrder"]) { // update the document after the final approval
                            $finalupdate = $namespacedModel::find($input["documentSystemCode"])->update([$docInforArr["approvedColumnName"] => $docInforArr["approveValue"], $docInforArr["approvedBy"] => $empInfo->empID, $docInforArr["approvedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["approvedDate"] => now()]);
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
                                    $body = $bodyName . " is fully approved.";
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
                                    $body = $bodyName . " Level " . $currentApproved->rollLevelOrder . " is approved and sent to next level approval to below employees <br>" . $nextApproveNameList;
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
            return ['success' => false, 'message' => $e . 'Error Ocurred'];
        }
    }

    /**
     * get current employee information
     * @return mixed
     */
    public static function getEmployeeInfo()
    {
        $user = Models\User::find(Auth::id());
        $employee = Models\Employee::find($user->employee_id);
        return $employee;
    }

    /**
     * @param $date
     * @return false|string
     */
    public static function dateFormat($date)
    {
        if($date){
            return date("d/m/Y", strtotime($date));
        }else{
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
    public static function companyFinanceYear($companySystemID)
    {
        $companyFinanceYear = Models\CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"))
            ->where('companySystemID', '=', $companySystemID)
            ->where('isActive', -1)
            ->get();
        return $companyFinanceYear;
    }

    /**
     * Get all company company Finance Year
     * @param $companySystemID - current company id
     * @return array
     */
    public static function companyFinancePeriod($companySystemID, $companyFinanceYearID, $departmentSystemID)
    {
        $companyFinancePeriod = Models\CompanyFinancePeriod::select(DB::raw("companyFinancePeriodID,isCurrent,CONCAT(DATE_FORMAT(dateFrom, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(dateTo, '%d/%m/%Y')) as financePeriod"))
            ->where('companySystemID', '=', $companySystemID)
            ->where('companyFinanceYearID', $companyFinanceYearID)
            ->where('departmentSystemID', $departmentSystemID)
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
        $companyCurrency = Models\Company::with(['localcurrency','reportingcurrency'])
            ->where('companySystemID', '=', $companySystemID)
            ->first();
        return $companyCurrency;
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


}
