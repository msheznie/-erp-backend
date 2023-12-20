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

use App\Mail\EmailForQueuing;
use App\Models\Alert;
use App\Models\AssetCapitalization;
use App\Models\VatReturnFillingMaster;
use App\Models\AssetDisposalMaster;
use App\Models\AssetVerification;
use App\Models\BankAccount;
use App\Models\BankReconciliation;
use App\Models\BookInvSuppMaster;
use App\Models\BudgetMaster;
use App\Models\BudgetTransferForm;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\CreditNote;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\DebitNote;
use App\Models\ErpBudgetAddition;
use App\Models\DocumentApproved;
use App\Models\SalesReturn;
use App\Models\DeliveryOrder;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\ExpenseClaim;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetMaster;
use App\Models\GRVMaster;
use App\Models\InventoryReclassification;
use App\Models\ItemIssueMaster;
use App\Models\ItemMaster;
use App\Models\ItemReturnMaster;
use App\Models\JvMaster;
use App\Models\LeaveDataMaster;
use App\Models\MaterielRequest;
use App\Models\MonthlyAdditionsMaster;
use App\Models\PaymentBankTransfer;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\ProcumentOrder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseReturn;
use App\Models\QuotationMaster;
use App\Models\RegisteredSupplier;
use App\Models\StockAdjustment;
use App\Models\StockCount;
use App\Models\StockReceive;
use App\Models\StockTransfer;
use App\Models\SupplierMaster;
use App\Models\CurrencyConversionMaster;
use App\Models\ERPAssetTransfer;
use App\Models\ContingencyBudgetPlan;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Response;

class ReversalDocument
{

    // cancel po,pr,mr and grv

    public static function sendEmail($array)
    {
        $confirmedUserID = 0;
        $documentSystemCode = 0;
        $reversedBy = Auth::user()->name;

        if(isset($array['created_by'])) {
            $createdBy = $array['created_by']['employeeSystemID'];
            $createdByUserName = $array['created_by']['empUserName'];
        }

        if(isset($array["documentSystemID"])) {
            $documentId = $array["documentSystemID"];
        }else {
            $materialRequest = MaterielRequest::find($array['RequestID'])->with(['confirmed_by','created_by'])->first();
            if($materialRequest) {
                $documentId = $materialRequest->documentSystemID;
            }
        }

        switch ($documentId) {

            // pr reqeuest
            case 1:
                $purchaseRequest = PurchaseRequest::find($array['purchaseRequestID'])->with(['confirmed_by','created_by'])->first();
                $documentSystemCode = $array['purchaseRequestID'];
                $array = $purchaseRequest;
                $array['doc_code'] = $array['purchaseRequestCode'];
                break;
            // po
            case 5:
                $purchaseOrder = ProcumentOrder::find($array['purchaseOrderID'])->with(['confirmed_by','created_by'])->first();
                $array = $purchaseOrder;
                $array['doc_code'] = $array['purchaseOrderCode'];
                $documentSystemCode = $array['purchaseOrderID'];
                break;
            //grv
            case 3:
                $grv_document = GRVMaster::find($array['grvAutoID'])->with(['confirmed_by','created_by'])->first();
                $documentSystemCode = $array['grvAutoID'];
                $array = $grv_document;
                $array['doc_code'] = $array['grvPrimaryCode'];
                break;
            //mr
            case 9:
                $documentSystemCode = $array['RequestID'];
                $array = $materialRequest;
                $array['doc_code'] = $array['RequestCode'];
                break;
            default:
                # code...
                break;
        }

        $approvedUsers = self::getApprovedUsers($array,$documentSystemCode);

        if($approvedUsers) {
            foreach($approvedUsers as $employee) {
                if ($employee && !is_null($employee->empEmail)) {
                    if(($employee->discharegedYN == 0) && ($employee->ActivationFlag == -1) && ($employee->empLoginActive == 1) && ($employee->empActive == 1)){
                        $dataEmail['empEmail'] = $employee->empEmail;
                        $dataEmail['companySystemID'] = $employee->companySystemID;
                        $temp = "<p>Dear " . $employee->empName . ',</p><p>Please be informed that '.$employee->documentID.' '.$employee->documentCode.' has been reversed by '.$reversedBy.'</p>';
                        $dataEmail['alertMessage'] = $employee->documentID." Document Reversed";
                        $dataEmail['emailAlertMessage'] = $temp;
                        $sendEmail = \Email::sendEmailErp($dataEmail);
                    }
                }
            }
        }


    }

    public static function getApprovedUsers($data,$documentSystemCode) {
        $users = [];
        return  DocumentApproved::join('employees','employees.employeeSystemID','=','erp_documentapproved.employeeSystemID')->where('documentSystemID',$data["documentSystemID"])->where('documentSystemCode',$documentSystemCode)->groupBy('employees.employeeSystemID')->get();
    }

}
