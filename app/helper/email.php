<?php
/**
 * =============================================
 * -- File Name : email.php
 * -- Project Name : ERP
 * -- Module Name :  email class
 * -- Author : Mohamed Fayas
 * -- Create date : 26 - March 2018
 * -- Description : This file contains the all the common email function
 * -- REVISION HISTORY
 */

namespace App\helper;

use App\Models\Alert;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CustomerMaster;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\ItemMaster;
use App\Models\MaterielRequest;
use App\Models\ProcumentOrder;
use App\Models\PurchaseRequest;
use App\Models\SupplierMaster;
use App\Repositories\AlertRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use InfyOm\Generator\Utils\ResponseUtil;


class email
{

    /**
     * send emails
     * @param $array : accept parameters as an array
     * $array 1-documentSystemID : document master autoID
     * $array 2-empSystemID : email receiver employee auto id
     * $array 3-companySystemID : company auto id
     * $array 4-alertMessage : email subject
     * $array 5-emailAlertMessage : email body
     * $array 6-docSystemCode : entity auto id
     * @return mixed
     */
    public static function sendEmail($array)
    {

        $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!".
                    "<br>This is an auto generated email. Please do not reply to this email because we are not".
                    "monitoring this inbox. To get in touch with us, email us to systems@gulfenergy-int.com.</font>";


        foreach ($array as $data) {
            $employee = Employee::where('employeeSystemID', $data['empSystemID'])->first();


            if (!empty($employee)) {
                $data['empID'] = $employee->empID;
                $data['empName'] = $employee->empName;
                $data['empEmail'] = $employee->empEmail;
            }else{
                return ['success' => false, 'message' => 'Employee Not Found'];
            }

            $company = Company::where('companySystemID', $data['companySystemID'])->first();

            if (!empty($company)) {
                $data['companyID'] = $company->CompanyID;
            }else{
                return ['success' => false, 'message' => 'Company Not Found'];
            }

            $document = DocumentMaster::where('documentSystemID', $data['docSystemID'])->first();

            if (!empty($document)) {
                $data['docID'] = $document->documentID;
            }else{
                return ['success' => false, 'message' => 'Document Not Found'];
            }

            switch ($data['docSystemID']) { // check the document id and set relevant parameters
                case 1:
                case 50:
                case 51:
                    $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $data['docSystemCode'])->first();
                    if (!empty($purchaseRequest)) {
                        $data['docApprovedYN'] = $purchaseRequest->approved;
                        $data['docCode'] = $purchaseRequest->purchaseRequestCode;
                    }
                    break;
                case 2:
                case 5:
                case 52:
                    $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $data['docSystemCode'])->first();
                    if (!empty($purchaseOrder)) {
                        $data['docApprovedYN'] = $purchaseOrder->approved;
                        $data['docCode']       = $purchaseOrder->purchaseOrderCode;
                    }
                    break;
                case 56:
                    $supplier = SupplierMaster::where('supplierCodeSystem', $data['docSystemCode'])->first();
                    if (!empty($supplier)) {
                        $data['docApprovedYN'] = $supplier->approvedYN;
                        $data['docCode']       = $supplier->primarySupplierCode;
                    }
                    break;
                case 57:
                    $item = ItemMaster::where('itemCodeSystem', $data['docSystemCode'])->first();
                    if (!empty($item)) {
                        $data['docApprovedYN'] = $item->itemApprovedYN;
                        $data['docCode']       = $item->primaryCode;
                    }
                    break;
                case 58:
                    $customer = CustomerMaster::where('customerCodeSystem', $data['docSystemCode'])->first();
                    if (!empty($customer)) {
                        $data['docApprovedYN'] = $customer->approvedYN;
                        $data['docCode']       = $customer->CutomerCode;
                    }
                    break;
                case 59:
                    $chartOfAccount = ChartOfAccount::where('chartOfAccountSystemID', $data['docSystemCode'])->first();
                    if (!empty($chartOfAccount)) {
                        $data['docApprovedYN'] = $chartOfAccount->isApproved;
                        $data['docCode']       = $chartOfAccount->AccountCode;
                    }
                    break;
                case 9:
                    $materielRequest = MaterielRequest::where('RequestID', $data['docSystemCode'])->first();
                    if (!empty($materielRequest)) {
                        $data['docApprovedYN'] = $materielRequest->approved;
                        $data['docCode']       = $materielRequest->RequestCode;
                    }
                    break;
                default:
                    return ['success' => false, 'message' => 'Document ID not found'];

            }

            $data['isEmailSend'] = 0;

            $temp = "Hi ".$data['empName'].','.$data['emailAlertMessage'].$footer;

            $data['emailAlertMessage'] = $temp;
            Alert::create($data);
        }

        //$emails = Alert::insert($emailsArray);
        return ['success' => true, 'message' => 'Successfully Inserted'];
    }
}