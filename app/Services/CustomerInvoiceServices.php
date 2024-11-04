<?php

namespace App\Services;

use App\Models\AccountsReceivableLedger;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DocumentApproved;
use App\Models\GeneralLedger;
use App\Models\TaxLedger;
use App\Models\TaxLedgerDetail;
use App\Repositories\VatReturnFillingMasterRepository;
use App\Services\API\CustomerInvoiceAPIService;
use App\Traits\AuditTrial;

class CustomerInvoiceServices
{ 
    private $vatReturnFillingMasterRepo;

    public function __construct(VatReturnFillingMasterRepository $vatReturnFillingMasterRepo)
    {
        $this->vatReturnFillingMasterRepo = $vatReturnFillingMasterRepo;
    }

    public static function amendCustomerInvoice($input,$id,$masterData,$isFromAPI = false)
	{

            // checking document matched in machmaster
            $checkDetailExistMatch = CustomerReceivePaymentDetail::where('bookingInvCodeSystem', $id)
            ->where('companySystemID', $masterData->companySystemID)
            ->where('addedDocumentSystemID', $masterData->documentSystemiD)
            ->first();
    
            if ($checkDetailExistMatch) {
                if($isFromAPI){
                    return ['status' => false,'message'=>'the Invoice has been pulled to a Receipt Voucher'];
                } else {
                    return ['status' => false,'message'=>'Cannot return back to amend. Customer Invoice is added to receipt'];
                }
            }
    

            if($isFromAPI){
                $input['returnComment'] = 'Customer invoice cancellation from API';
            }

            //deleting from approval table
            $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $masterData->companySystemID)
            ->where('documentSystemID', $masterData->documentSystemiD)
            ->delete();
            

            //deleting from general ledger table
            $deleteGLData = GeneralLedger::where('documentSystemCode', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemiD)
                ->delete();

            //deleting records from accounts receivable
            $deleteARData = AccountsReceivableLedger::where('documentCodeSystem', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemiD)
                ->delete();

            //deleting records from tax ledger
            TaxLedger::where('documentMasterAutoID', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemiD)
                ->delete();


            $taxLedgerDetails = TaxLedgerDetail::where('documentMasterAutoID', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemiD)
                ->get();

            $returnFilledDetailID = null;
            foreach ($taxLedgerDetails as $taxLedgerDetail) {
                if($taxLedgerDetail->returnFilledDetailID != null){
                    $returnFilledDetailID = $taxLedgerDetail->returnFilledDetailID;
                }
                $taxLedgerDetail->delete();
            }

            if($returnFilledDetailID != null){
                self::$vatReturnFillingMasterRepo->updateVatReturnFillingDetails($returnFilledDetailID);
            }

            // updating fields
            $masterData->confirmedYN = 0;
            $masterData->confirmedByEmpSystemID = null;
            $masterData->confirmedByEmpID = null;
            $masterData->confirmedByName = null;
            $masterData->confirmedDate = null;
            $masterData->RollLevForApp_curr = 1;

            $masterData->approved = 0;
            $masterData->approvedByUserSystemID = null;
            $masterData->approvedByUserID = null;
            $masterData->approvedDate = null;
            $masterData->postedDate = null;
            $masterData->save();

            AuditTrial::createAuditTrial($masterData->documentSystemiD,$id,$input['returnComment'],'returned back to amend',null,$isFromAPI = true);


        return ['status' => true];
	}

    public static function deleteDetails($id ,$isFromAPI = true){

        $customerInvoiceDirectDetail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID',$id)->delete();

        $details = [
                        'bookingAmountTrans' => 0,
                        'bookingAmountLocal' => 0,
                        'bookingAmountRpt' => 0,
                    ];

        
        CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $id)->update($details);

        $master =  CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $id)->first();
        if($master->isPerforma != 2) {
            $resVat = CustomerInvoiceAPIService::updateTotalVAT($id);
            if (!$resVat['status']) {
                return ['status' => false,'message'=>$resVat['message']];
             } 
        }

        return ['status' => true];

    }

    public static function cancelCustomerInvoice($input,$id,$masterData,$isFromAPI = false)
	{
        
        if ($masterData->canceledYN == -1) {
            return ['status' => false,'message'=>'the Invoice is already cancelled'];
        }

        $employee = UserTypeService::getSystemEmployee();
        $canceledComments = 'Customer invoice cancellation from API';

        $masterData->canceledYN = -1;
        $masterData->canceledComments = $canceledComments;
        $masterData->canceledDateTime = NOW();
        $masterData->canceledByEmpSystemID = $employee->employeeSystemID;
        $masterData->canceledByEmpID = $employee->empID;
        $masterData->canceledByEmpName = $employee->empFullName;
        $masterData->customerInvoiceNo = null;
        $masterData->save();

        /*Audit entry*/
        AuditTrial::createAuditTrial($masterData->documentSystemiD,$id,$canceledComments,'Cancelled',null,$isFromAPI = true);
        return ['status' => true];

    }
}