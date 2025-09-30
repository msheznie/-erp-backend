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
use App\Models\RecurringVoucherSetup;
use App\Models\SegmentMaster;
use App\Models\SRMTenderPaymentProof;
use App\Models\SupplierRegistrationLink;
use App\Models\SystemConfigurationAttributes;
use App\Models\TenderMaster;
use App\Models\VatReturnFillingMaster;
use App\Models\AssetDisposalMaster;
use App\Models\AssetVerification;
use App\Models\BankAccount;
use App\Models\BankReconciliation;
use App\Models\BookInvSuppMaster;
use App\Models\BudgetMaster;
use App\Models\BudgetTransferForm;
use App\Models\ConsoleJVMaster;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\CreditNote;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\DebitNote;
use App\Models\ErpBudgetAddition;
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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\DocumentModifyRequest;
use Response;
use App\Models\AppearanceSettings;

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

        $footer = "<font size='1.5'><i><p><br><br><br>" . trans('email.footer_save_paper') .
            "<br>" . trans('email.footer_auto_generated') . "</font>";
        $empInfoSkip = array(106, 107);
        $count = 0;
        Log::useFiles(storage_path() . '/logs/send_email_jobs.log');

        $hasPolicy = false;

        $unverifiedEmailArray = array();
        foreach ($array as $data) {

            $employee = Employee::where('employeeSystemID', $data['empSystemID'])
                ->where('discharegedYN', 0)
                ->where('ActivationFlag', -1)
                ->where('empLoginActive', 1)
                ->where('empActive', 1)->first();
            if(isset($employee)){
                if (!empty($employee)) {
                    $data['empID'] = $employee->empID;
                    $data['empName'] = $employee->empName;
                    $data['empEmail'] = $employee->empEmail;
                    $data['isEmailVerified'] = $employee->isEmailVerified;
                } else {
                    if (in_array($data['docSystemID'], $empInfoSkip)) {
                        continue;
                        // return ['success' => true, 'message' => 'Successfully Inserted'];
                    }
                    return ['success' => false, 'message' => trans('email.employee_not_found')];
                }

                $company = Company::where('companySystemID', $data['companySystemID'])->first();

                if (!empty($company)) {
                    $data['companyID'] = $company->CompanyID;
                } else {
                    return ['success' => false, 'message' => trans('email.company_not_found')];
                }

                $document = DocumentMaster::where('documentSystemID', $data['docSystemID'])->first();

                if (!empty($document)) {
                    $data['docID'] = $document->documentID;
                } else {
                    return ['success' => false, 'message' => trans('email.document_not_found')];
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
                            $data['docCode'] = $purchaseOrder->purchaseOrderCode;
                        }
                        break;
                    case 56:
                        $supplier = SupplierMaster::where('supplierCodeSystem', $data['docSystemCode'])->first();
                        if (!empty($supplier)) {
                            $data['docApprovedYN'] = $supplier->approvedYN;
                            $data['docCode'] = $supplier->primarySupplierCode;
                        }
                        break;
                    case 86:
                        $supplier = RegisteredSupplier::where('id', $data['docSystemCode'])->first();
                        if (!empty($supplier)) {
                            $data['docApprovedYN'] = $supplier->approvedYN;
                            $data['docCode'] = $supplier->supplierName;
                        }
                        break;
                    case 57:
                        $item = ItemMaster::where('itemCodeSystem', $data['docSystemCode'])->first();
                        if (!empty($item)) {
                            $data['docApprovedYN'] = $item->itemApprovedYN;
                            $data['docCode'] = $item->primaryCode;
                        }
                        break;
                    case 58:
                        $customer = CustomerMaster::where('customerCodeSystem', $data['docSystemCode'])->first();
                        if (!empty($customer)) {
                            $data['docApprovedYN'] = $customer->approvedYN;
                            $data['docCode'] = $customer->CutomerCode;
                        }
                        break;
                    case 59:
                        $chartOfAccount = ChartOfAccount::where('chartOfAccountSystemID', $data['docSystemCode'])->first();
                        if (!empty($chartOfAccount)) {
                            $data['docApprovedYN'] = $chartOfAccount->isApproved;
                            $data['docCode'] = $chartOfAccount->AccountCode;
                        }
                        break;
                    case 9:
                        $materielRequest = MaterielRequest::where('RequestID', $data['docSystemCode'])->first();
                        if (!empty($materielRequest)) {
                            $data['docApprovedYN'] = $materielRequest->approved;
                            $data['docCode'] = $materielRequest->RequestCode;
                        }
                        break;
                    case 3:
                        $grvMaster = GRVMaster::where('grvAutoID', $data['docSystemCode'])->first();
                        if (!empty($grvMaster)) {
                            $data['docApprovedYN'] = $grvMaster->approved;
                            $data['docCode'] = $grvMaster->grvPrimaryCode;
                        }
                        break;
                    case 8:
                        $materielIssue = ItemIssueMaster::where('itemIssueAutoID', $data['docSystemCode'])->first();
                        if (!empty($materielIssue)) {
                            $data['docApprovedYN'] = $materielIssue->approved;
                            $data['docCode'] = $materielIssue->itemIssueCode;
                        }
                        break;
                    case 13:
                        $stockTransfer = StockTransfer::where('stockTransferAutoID', $data['docSystemCode'])->first();
                        if (!empty($stockTransfer)) {
                            $data['docApprovedYN'] = $stockTransfer->approved;
                            $data['docCode'] = $stockTransfer->stockTransferCode;
                        }
                        break;
                    case 12:
                        $materielReturn = ItemReturnMaster::where('itemReturnAutoID', $data['docSystemCode'])->first();
                        if (!empty($materielReturn)) {
                            $data['docApprovedYN'] = $materielReturn->approved;
                            $data['docCode'] = $materielReturn->itemReturnCode;
                        }
                        break;
                    case 10:
                        $stockReceive = StockReceive::where('stockReceiveAutoID', $data['docSystemCode'])->first();
                        if (!empty($stockReceive)) {
                            $data['docApprovedYN'] = $stockReceive->approved;
                            $data['docCode'] = $stockReceive->stockReceiveCode;
                        }
                        break;
                    case 61:
                        $inventoryReclassification = InventoryReclassification::where('inventoryreclassificationID', $data['docSystemCode'])->first();
                        if (!empty($stockReceive)) {
                            $data['docApprovedYN'] = $inventoryReclassification->approved;
                            $data['docCode'] = $inventoryReclassification->documentCode;
                        }
                        break;
                    case 24:
                        $purchaseReturn = PurchaseReturn::find($data['docSystemCode']);
                        if (!empty($purchaseReturn)) {
                            $data['docApprovedYN'] = $purchaseReturn->approved;
                            $data['docCode'] = $purchaseReturn->purchaseReturnCode;
                        }
                        break;
                    case 20:
                        $inventoryReclassification = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $data['docSystemCode'])->first();
                        if (!empty($stockReceive)) {
                            $data['docApprovedYN'] = $inventoryReclassification->approved;
                            $data['docCode'] = $inventoryReclassification->bookingInvCode;
                        }
                        break;
                    case 7:
                        $stockAdjustment = StockAdjustment::where('stockAdjustmentAutoID', $data['docSystemCode'])->first();
                        if (!empty($stockAdjustment)) {
                            $data['docApprovedYN'] = $stockAdjustment->approved;
                            $data['docCode'] = $stockAdjustment->stockAdjustmentCode;
                        }
                        break;
                    case 97:
                        $stockAdjustment = StockCount::where('stockCountAutoID', $data['docSystemCode'])->first();
                        if (!empty($stockAdjustment)) {
                            $data['docApprovedYN'] = $stockAdjustment->approved;
                            $data['docCode'] = $stockAdjustment->stockCountCode;
                        }
                        break;
                    case 15:
                        $debitNote = DebitNote::where('debitNoteAutoID', $data['docSystemCode'])->first();
                        if (!empty($debitNote)) {
                            $data['docApprovedYN'] = $debitNote->approved;
                            $data['docCode'] = $debitNote->debitNoteCode;
                        }
                        break;
                    case 21:
                        $receiptVoucher = CustomerReceivePayment::where('custReceivePaymentAutoID', $data['docSystemCode'])->first();
                        if (!empty($receiptVoucher)) {
                            $data['docApprovedYN'] = $receiptVoucher->approved;
                            $data['docCode'] = $receiptVoucher->custPaymentReceiveCode;
                        }
                        break;
                    case 19:
                        $creditNote = CreditNote::where('creditNoteAutoID', $data['docSystemCode'])->first();
                        if (!empty($creditNote)) {
                            $data['docApprovedYN'] = $creditNote->approved;
                            $data['docCode'] = $creditNote->creditNoteCode;
                        }
                        break;
                    case 11:
                        $suppInv = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $data['docSystemCode'])->first();
                        if (!empty($creditNote)) {
                            $data['docApprovedYN'] = $suppInv->approved;
                            $data['docCode'] = $suppInv->bookingInvCode;
                        }
                        break;
                    case 4:
                        $payInv = PaySupplierInvoiceMaster::where('PayMasterAutoId', $data['docSystemCode'])->first();
                        if (!empty($creditNote)) {
                            $data['docApprovedYN'] = $payInv->approved;
                            $data['docCode'] = $payInv->BPVcode;
                        }
                        break;
                    case 62:
                        $bankRecMaster = BankReconciliation::where('bankRecAutoID', $data['docSystemCode'])->first();
                        if (!empty($bankRecMaster)) {
                            $data['docApprovedYN'] = $bankRecMaster->approved;
                            $data['docCode'] = $bankRecMaster->bankRecPrimaryCode;
                        }
                        break;
                    case 63:
                        $assetcapitalization = AssetCapitalization::where('capitalizationID', $data['docSystemCode'])->first();
                        if (!empty($assetcapitalization)) {
                            $data['docApprovedYN'] = $assetcapitalization->approved;
                            $data['docCode'] = $assetcapitalization->capitalizationCode;
                        }
                        break;
                    case 64:
                        $paymentBankTransfer = PaymentBankTransfer::where('paymentBankTransferID', $data['docSystemCode'])->first();
                        if (!empty($paymentBankTransfer)) {
                            $data['docApprovedYN'] = $paymentBankTransfer->approved;
                            $data['docCode'] = $paymentBankTransfer->bankTransferDocumentCode;
                        }
                        break;
                    case 17:
                        $journalVoucher = JvMaster::where('jvMasterAutoId', $data['docSystemCode'])->first();
                        if (!empty($journalVoucher)) {
                            $data['docApprovedYN'] = $journalVoucher->approved;
                            $data['docCode'] = $journalVoucher->JVcode;
                        }
                        break;
                    case 22:
                        $fixedAssetMaster = FixedAssetMaster::find($data['docSystemCode']);
                        if (!empty($fixedAssetMaster)) {
                            $data['docApprovedYN'] = $fixedAssetMaster->approved;
                            $data['docCode'] = $fixedAssetMaster->faCode;
                        }
                        break;
                    case 23:
                        $fixedAssetDep = FixedAssetDepreciationMaster::find($data['docSystemCode']);
                        if (!empty($fixedAssetDep)) {
                            $data['docApprovedYN'] = $fixedAssetDep->approved;
                            $data['docCode'] = $fixedAssetDep->depCode;
                        }
                        break;
                    case 46:
                        $budgetTransfer = BudgetTransferForm::find($data['docSystemCode']);
                        if (!empty($budgetTransfer)) {
                            $data['docApprovedYN'] = $budgetTransfer->approvedYN;
                            $data['docCode'] = $budgetTransfer->transferVoucherNo;
                        }
                        break;
                    case 65:
                        $budget = BudgetMaster::find($data['docSystemCode']);
                        if (!empty($budget)) {
                            $data['docApprovedYN'] = $budget->approvedYN;
                            $data['docCode'] = $budget->budgetmasterID;
                        }
                        break;
                    case 41:
                        $assetDisposal = AssetDisposalMaster::find($data['docSystemCode']);
                        if (!empty($assetDisposal)) {
                            $data['docApprovedYN'] = $assetDisposal->approvedYN;
                            $data['docCode'] = $assetDisposal->disposalDocumentCode;
                        }
                        break;
                    case 28:
                        $monthlyAddition = MonthlyAdditionsMaster::find($data['docSystemCode']);
                        if (!empty($monthlyAddition)) {
                            $data['docApprovedYN'] = $monthlyAddition->approvedYN;
                            $data['docCode'] = $monthlyAddition->monthlyAdditionsCode;
                        }
                        break;
                    case 66:
                        $bankAccount = BankAccount::find($data['docSystemCode']);
                        if (!empty($bankAccount)) {
                            $data['docApprovedYN'] = $bankAccount->approvedYN;
                            $data['docCode'] = $bankAccount->AccountNo;
                        }
                        break;
                    case 67:
                    case 68:
                        $quotationMaster = QuotationMaster::find($data['docSystemCode']);
                        if (!empty($quotationMaster)) {
                            $data['docApprovedYN'] = $quotationMaster->approvedYN;
                            $data['docCode'] = $quotationMaster->quotationCode;
                        }
                        break;
                    case 6:
                        $expenseClaim = ExpenseClaim::find($data['docSystemCode']);
                        if (!empty($expenseClaim)) {
                            $data['docApprovedYN'] = $expenseClaim->approved;
                            $data['docCode'] = $expenseClaim->expenseClaimCode;
                        }
                        break;
                    case 37:
                        $leaveMaster = LeaveDataMaster::find($data['docSystemCode']);
                        if (!empty($leaveMaster)) {
                            $data['docApprovedYN'] = $leaveMaster->approvedYN;
                            $data['docCode'] = $leaveMaster->leaveDataMasterCode;
                        }
                        break;
                    case 71:
                        $deliveryOrder = DeliveryOrder::find($data['docSystemCode']);
                        if (!empty($deliveryOrder)) {
                            $data['docApprovedYN'] = $deliveryOrder->approvedYN;
                            $data['docCode'] = $deliveryOrder->deliveryOrderCode;
                        }
                        break;
                    case 87:
                        $salesReturn = SalesReturn::find($data['docSystemCode']);
                        if (!empty($salesReturn)) {
                            $data['docApprovedYN'] = $salesReturn->approvedYN;
                            $data['docCode'] = $salesReturn->verficationCode;
                        }
                        break;
                    case 99:
                        $assetVerification = AssetVerification::find($data['docSystemCode']);
                        if (!empty($assetVerification)) {
                            $data['docApprovedYN'] = $assetVerification->approvedYN;
                            $data['docCode'] = $assetVerification->salesReturnCode;
                        }
                        break;
                    case 96:
                        $currencyConversion = CurrencyConversionMaster::find($data['docSystemCode']);
                        if (!empty($currencyConversion)) {
                            $data['docApprovedYN'] = $currencyConversion->approvedYN;
                            $data['docCode'] = $currencyConversion->conversionCode;
                        }
                        break;

                    case 103:
                        $erpAssetTransfer = ERPAssetTransfer::find($data['docSystemCode']);
                        if (!empty($erpAssetTransfer)) {
                            $data['docApprovedYN'] = $erpAssetTransfer->approved_yn;
                            $data['docCode'] = $erpAssetTransfer->document_code;
                        }
                        break;

                    case 100:
                        $contingencyBudgetPlan = ContingencyBudgetPlan::find($data['docSystemCode']);
                        if (!empty($contingencyBudgetPlan)) {
                            $data['docApprovedYN'] = $contingencyBudgetPlan->approvedYN;
                            $data['docCode'] = $contingencyBudgetPlan->contingencyBudgetNo;
                        }
                        break;
                    case 102:
                        $budgetAddition = ErpBudgetAddition::find($data['docSystemCode']);
                        if (!empty($budgetAddition)) {
                            $data['docApprovedYN'] = $budgetAddition->approvedYN;
                            $data['docCode'] = $budgetAddition->additionVoucherNo;
                        }
                        break;
                    case 104:
                        $vrf = VatReturnFillingMaster::find($data['docSystemCode']);
                        if (!empty($budget)) {
                            $data['docApprovedYN'] = $vrf->approvedYN;
                            $data['docCode'] = $vrf->returnFillingCode;
                        }
                        break;
                    case 106:
                        $appointment = Appointment::find($data['docSystemCode']);
                        if (!empty($appointment)) {
                            $data['docApprovedYN'] = $appointment->approved_yn;
                            $data['docCode'] = $appointment->primary_code;
                        }
                        break;
                    case 107:
                        $supplierLink = SupplierRegistrationLink::find($data['docSystemCode']);

                        if (!empty($supplierLink)) {
                            $data['docApprovedYN'] = $supplierLink->approved_yn;
                            $data['docCode'] = $supplierLink->id;
                        }
                        break;
                    case 108:
                        $tender = TenderMaster::find($data['docSystemCode']);

                        if (!empty($tender)) {
                            $data['docApprovedYN'] = $tender->approved;
                            $data['docCode'] = $tender->tender_code;
                        }
                        break;
                    case 113:
                        $tender = TenderMaster::find($data['docSystemCode']);

                        if (!empty($tender)) {
                            $data['docApprovedYN'] = $tender->approved;
                            $data['docCode'] = $tender->tender_code;
                        }
                        break;
                    case 69:
                        $journalVoucher = ConsoleJVMaster::where('consoleJvMasterAutoId', $data['docSystemCode'])->first();
                        if (!empty($journalVoucher)) {
                            $data['docApprovedYN'] = $journalVoucher->approved;
                            $data['docCode'] = $journalVoucher->consoleJVcode;
                        }
                        break;
                    case 117:
                        $editRequedt = DocumentModifyRequest::find($data['docSystemCode']);

                        if (!empty($editRequedt)) {
                            $data['docApprovedYN'] = $editRequedt->approved;
                            $data['docCode'] = $editRequedt->code;
                        }
                        break;
                    case 118:
                        $editRequedt = DocumentModifyRequest::find($data['docSystemCode']);

                        if (!empty($editRequedt)) {
                            $data['docApprovedYN'] = $editRequedt->confirmation_approved;
                            $data['docCode'] = $editRequedt->code;
                        }
                        break;
                    case 119:
                        $recurringVoucher = RecurringVoucherSetup::where('recurringVoucherAutoId', $data['docSystemCode'])->first();
                        if (!empty($recurringVoucher)) {
                            $data['docApprovedYN'] = $recurringVoucher->approved;
                            $data['docCode'] = $recurringVoucher->RRVcode;
                        }
                        break;
                    case 127:
                        $srmTenderPaymentProof = SRMTenderPaymentProof::where('id', $data['docSystemCode'])->first();
                        if (!empty($srmTenderPaymentProof)) {
                            $data['docApprovedYN'] = $srmTenderPaymentProof->approved_yn;
                            $data['docCode'] = $srmTenderPaymentProof->document_code;
                        }

                    case 132:
                        $segment = SegmentMaster::withoutGlobalScope('final_level')->where('serviceLineSystemID', $data['docSystemCode'])->first();
                        if (!empty($segment)) {
                            $data['docApprovedYN'] = $segment->approved_yn;
                            $data['docCode'] = $segment->serviceLineCode;
                        }
                        break;
                    default:
                        return ['success' => false, 'message' => trans('email.document_id_not_found')];
                }


                $data['isEmailSend'] = 0;
                $temp = trans('email.hi') . " " . $data['empName'] . ',' . $data['emailAlertMessage'] . $footer;

                $data['emailAlertMessage'] = $temp;

                $color = '#C23C32';
                $colorObj= AppearanceSettings::where('appearance_system_id', 1)->where('appearance_element_id', 1)->first();
                if($colorObj)
                {
                    $color = $colorObj->value;
                }

                $text = 'GEARS';
                $textObj= AppearanceSettings::where('appearance_system_id', 1)->where('appearance_element_id', 7)->first();
                if($textObj)
                {
                    $text = $textObj->value;
                }

                $fromName = \Helper::getEmailConfiguration('mail_name','GEARS');

                // IF Policy Send emails from Sendgrid is on -> send email through Sendgrid
                if ($data) {
                $hasPolicy = CompanyPolicyMaster::where('companySystemID', $data['companySystemID'])
                    ->where('companyPolicyCategoryID', 37)
                    ->where('isYesNO', 1)
                    ->exists();


                if ($hasPolicy) {
                        $data['attachmentFileName'] = isset($data['attachmentFileName']) ? $data['attachmentFileName'] : '';
                        $data['attachmentList'] = isset($data['attachmentList']) ? $data['attachmentList'] : [];
                        if (isset($data['empEmail']) && $data['empEmail']) {
                            $data['empEmail'] = self::emailAddressFormat($data['empEmail']);

                            if(!isset($data['isEmailVerified']) || !$data['isEmailVerified'])
                            {
                                array_push($unverifiedEmailArray,'<li style="text-align: left;">'.$data['empEmail'].'</li>');
                            }

                            if ($data['empEmail'] && $data['isEmailVerified']) {
                                Mail::to($data['empEmail'])->send(new EmailForQueuing($data['alertMessage'], $data['emailAlertMessage'], $data['attachmentFileName'],$data['attachmentList'],$color,$text,$fromName, app()->getLocale()));
                                $count = $count + 1;
                            }
                        }

                    } else {
                        Alert::create($data);
                    }
                }
            }
        }


        return ['success' => true, 'message' => trans('email.successfully_inserted'),'unverifiedEmail' => ($hasPolicy) ? count($unverifiedEmailArray) > 0 : 0, 'unverifiedEmailMsg' => ($hasPolicy && count($unverifiedEmailArray) > 0) ? trans('email.unverified_email_message') . '  <br/><br/> <ul>'. implode('',array_unique($unverifiedEmailArray)).'</ul>' : null];


    }

    public static function sendEmailErp($data)
    {
        $color = '#C23C32';
        $colorObj= AppearanceSettings::where('appearance_system_id', 1)->where('appearance_element_id', 1)->first();
        if($colorObj)
        {
             $color = $colorObj->value;
        }
 
        $text = 'GEARS';
        $textObj= AppearanceSettings::where('appearance_system_id', 1)->where('appearance_element_id', 7)->first();
        if($textObj)
        {
             $text = $textObj->value;
        }

        $fromName = \Helper::getEmailConfiguration('mail_name','GEARS');

        $hasPolicy = CompanyPolicyMaster::where('companySystemID', $data['companySystemID'])
            ->where('companyPolicyCategoryID', 37)
            ->where('isYesNO', 1)
            ->exists();
        if ($hasPolicy) {
            $data['attachmentFileName'] = isset($data['attachmentFileName']) ? $data['attachmentFileName'] : '';
            $data['attachmentList'] = isset($data['attachmentList']) ? $data['attachmentList'] : [];
            if (isset($data['empEmail']) && $data['empEmail']) {
                $data['empEmail'] = self::emailAddressFormat($data['empEmail']);
                if ($data['empEmail']) {
                    Mail::to($data['empEmail'])->send(new EmailForQueuing($data['alertMessage'], $data['emailAlertMessage'], $data['attachmentFileName'],$data['attachmentList'],$color,$text,$fromName, app()->getLocale()));
                }
            }
        } else {
            Alert::create($data);
        }

        return ['success' => true, 'message' => trans('email.successfully_inserted')];
    }

    public static function emailAddressFormat($email)
    {

        if ($email) {
            $email = str_replace(" ", "", $email);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = ''; // Email not valid
            }
        }

        return $email;
    }

    public static function sendEmailSRM($data)
    {
        $color = '#C23C32';
        $colorObj= AppearanceSettings::where('appearance_system_id', 1)->where('appearance_element_id', 1)->first();
        if($colorObj)
        {
            $color = $colorObj->value;
        }

        $text = 'GEARS';
        $textObj= AppearanceSettings::where('appearance_system_id', 1)->where('appearance_element_id', 7)->first();
        if($textObj)
        {
            $text = $textObj->value;
        }

        $fromName = \Helper::getEmailConfiguration('mail_name','GEARS');

        $hasPolicy = CompanyPolicyMaster::where('companySystemID', $data['companySystemID'])
            ->where('companyPolicyCategoryID', 37)
            ->where('isYesNO', 1)
            ->exists();
        if ($hasPolicy) {
            $data['attachmentFileName'] = isset($data['attachmentFileName']) ? $data['attachmentFileName'] : '';
            $data['attachmentList'] = isset($data['attachmentList']) ? $data['attachmentList'] : [];
            if (isset($data['empEmail']) && $data['empEmail']) {
                $data['empEmail'] = self::emailAddressFormat($data['empEmail']);
                if ($data['empEmail']) {
                    Mail::to($data['empEmail'])
                        ->cc(isset($data['ccEmail']) ? $data['ccEmail'] : [])
                        ->send(new EmailForQueuing($data['alertMessage'], $data['emailAlertMessage'], $data['attachmentFileName'],$data['attachmentList'],$color,$text,$fromName, app()->getLocale()));
                }
            }
        } else {
            Alert::create($data);
        }

        return ['success' => true, 'message' => trans('email.successfully_inserted')];
    }
}
