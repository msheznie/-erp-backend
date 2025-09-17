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
use App\Models\SalesReturnDetail;
use Carbon\Carbon;
use App\Models\DeliveryOrder;
use App\Models\ItemIssueMaster;
use App\Models\StockTransfer;
use App\Models\StockAdjustment;
use App\Models\StockCount;
use App\Models\ErpItemLedger;
use App\Models\CustomerInvoiceItemDetails;

class CustomerInvoiceServices
{ 
    private $vatReturnFillingMasterRepo;

    public function __construct(VatReturnFillingMasterRepository $vatReturnFillingMasterRepo)
    {
        $this->vatReturnFillingMasterRepo = $vatReturnFillingMasterRepo;
    }

    public function amendCustomerInvoice($input,$id,$masterData,$isFromAPI = false)
	{
             $codes = [];
            // checking document matched in machmaster
             $checkDetailExistMatch = CustomerReceivePaymentDetail::where('bookingInvCodeSystem', $id)
            ->where('companySystemID', $masterData->companySystemID)
            ->where('addedDocumentSystemID', $masterData->documentSystemiD)
            ->first();
            
             $reciepts = CustomerReceivePaymentDetail::where('bookingInvCodeSystem', $id)
                                        ->where('companySystemID', $masterData->companySystemID)
                                        ->with(['master' => function($query) {
                                                  $query->select('custReceivePaymentAutoID', 'custPaymentReceiveCode');
                                         }])->select('custRecivePayDetAutoID','custReceivePaymentAutoID')->get();
    
                                        foreach ($reciepts as $reciept) {
                                            if (isset($reciept->master->custPaymentReceiveCode)) {
                                                $codes[] = $reciept->master->custPaymentReceiveCode . ' - Customer Invoice Receipt';
                                            }
                                        }

             $sales = SalesReturnDetail::where('custInvoiceDirectAutoID', $id)
                                        ->where('companySystemID', $masterData->companySystemID)
                                        ->with(['master' => function($query) {
                                                  $query->select('id', 'salesReturnCode');
                                         }])->select('salesReturnID')->get();

                                        foreach ($sales as $sale) {
                                            if (isset($sale->master->salesReturnCode)) {
                                                $codes[] = $sale->master->salesReturnCode . ' - Sales Return';
                                            }
                                        }
                                        
            if($isFromAPI && $checkDetailExistMatch){
                return ['status' => false,'message'=>'the Invoice has been pulled to a Receipt Voucher'];
            } else {
                if (!empty($codes)) {
                        $message = 'Selected sales order–based customer invoice, pulled to ' . implode(', ', $codes) . '.';
                        return ['status' => false, 'message' => $message];
                    }

            }

            
            $masterDataDetails = CustomerInvoiceDirect::where('isPerforma',4)->with('issue_item_details')
                                            ->whereHas('issue_item_details', function ($query) {
                                                $query->where('itemFinanceCategoryID', 1);
                                            })
                                            ->find($id);


            $isExist = false;                                    
            if ($masterDataDetails)
            {   
                $currentDate = Carbon::parse($masterDataDetails->postedDate);
                foreach ($masterDataDetails->issue_item_details as $detail) {
                    $item = $detail->itemCodeSystem;

                    $modelsToCheck = [
                        [CustomerInvoiceDirect::class, 'issue_item_details', ['isPerforma' => 0]],
                        [ItemIssueMaster::class, 'details'],
                        [StockTransfer::class, 'details'],
                        [StockAdjustment::class, 'details'],
                        [StockCount::class, 'details'],
                        [DeliveryOrder::class, 'detail'],
                    ];

                    foreach ($modelsToCheck as $modelInfo) {
                        [$model, $relation] = $modelInfo;
                        $additionalWhere = $modelInfo[2] ?? [];

                        $query = $model::with($relation)->where('companySystemID', $masterDataDetails->companySystemID)
                            ->whereHas($relation, function ($q) use ($item) {
                                $q->where('itemCodeSystem', $item);
                            });

                        foreach ($additionalWhere as $column => $value) {
                              $query->where($column, '!=', $value)->where('custInvoiceDirectAutoID', '!=', $id);
                        }

                        if ($query->exists()) {
                            $isExist = true;
                            break 2;
                        }
                    }
                }
            }
            if($isExist)
            {
                return ['status' => false,'message'=>'You cannot return  back to amend the Delivery Order  because a stock-out document already exists for one or more related items.
                                            Allowing amendments at this stage may impact the existing stock-out document and affect the Weighted Average Cost (WAC) calculation'];
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
                $this->vatReturnFillingMasterRepo->updateVatReturnFillingDetails($returnFilledDetailID);
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