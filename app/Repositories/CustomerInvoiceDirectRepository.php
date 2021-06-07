<?php

namespace App\Repositories;

use App\Models\AccountsReceivableLedger;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\MatchDocumentMaster;
use App\Models\CustomerInvoiceDirect;
use InfyOm\Generator\Common\BaseRepository;
use Carbon\Carbon;

/**
 * Class CustomerInvoiceDirectRepository
 * @package App\Repositories
 * @version August 6, 2018, 10:02 am UTC
 *
 * @method CustomerInvoiceDirect findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoiceDirect find($id, $columns = ['*'])
 * @method CustomerInvoiceDirect first($columns = ['*'])
*/
class CustomerInvoiceDirectRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'transactionMode',
        'companySystemID',
        'companyID',
        'documentSystemiD',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'serviceLineSystemID',
        'serviceLineCode',
        'wareHouseSystemCode',
        'bookingInvCode',
        'bookingDate',
        'comments',
        'invoiceDueDate',
        'customerGRVAutoID',
        'bankID',
        'bankAccountID',
        'performaDate',
        'wanNO',
        'PONumber',
        'rigNo',
        'customerID',
        'customerGLCode',
        'customerInvoiceNo',
        'customerInvoiceDate',
        'custTransactionCurrencyID',
        'custTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'bookingAmountTrans',
        'bookingAmountLocal',
        'bookingAmountRpt',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'servicePeriod',
        'paymentInDaysForJob',
        'serviceStartDate',
        'serviceEndDate',
        'isPerforma',
        'documentType',
        'secondaryLogoCompID',
        'secondaryLogo',
        'timesReferred',
        'RollLevForApp_curr',
        'selectedForTracking',
        'customerInvoiceTrackingID',
        'interCompanyTransferYN',
        'canceledYN',
        'canceledByEmpSystemID',
        'canceledByEmpID',
        'canceledByEmpName',
        'vatOutputGLCodeSystemID',
        'vatOutputGLCode',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'discountLocalAmount',
        'discountAmount',
        'discountRptAmount',
        'canceledDateTime',
        'canceledComments',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoiceDirect::class;
    }

    function getAudit($id)
    {


        $customerInvoiceDirect = $this->with(['company', 'customer', 'tax', 'createduser', 'bankaccount', 'currency', 'local_currency','approved_by' => function ($query) {
            $query->with('employee.details.designation')
                ->where('documentSystemID', 20);
        }, 'invoicedetail'
        => function ($query) {
                $query1 = $query;
                $bill = $query1->first();
                $companyID=0;
                if(!empty($bill)){
                    $companyID = $bill->companyID;
                }


                $query->with(['unit', 'department','contract','billmaster'=>function ($query) use ($companyID){
                    $query->where('companyID',$companyID);
                    $query1 = $query;
                    $bill = $query1->first();
                    $Ticketno = $bill->Ticketno;
                    $query->with(['ticketmaster'=> function ($query){
                        $query->with(['field','rig']);
                    }  ,'performatemp'=>function($query) use($Ticketno) {
                        $query->where('Ticketno',$Ticketno);
                        $query->where('sumofsumofStandbyAmount','<>',0);
                    }]);
                },'performadetails'=> function ($query){

                    $query1 = $query;
                    $bill = $query1->first()->toArray();
                    $companyID = $bill['companyID'];

                    $query->with(['freebillingmaster' => function ($query) use($companyID) {
                        $query->where('companyID',$companyID);
                        $query->with(['ticketmaster' => function ($query)  {

                            $query->with(['field']);
                        }]);
                    }]);
                }]);
            }
        ])->findWithoutFail($id);


        return $customerInvoiceDirect;
    }

    function getAudit2($id)
    {
         $customerInvoiceDirect = $this->with(['company', 'customer', 'tax', 'createduser', 'bankaccount', 'currency','local_currency', 'approved_by' => function ($query) {
             $query->with('employee.details.designation')
                 ->where('documentSystemID', 20);
         }, 'invoicedetails'
         => function ($query) {
                 $query->with(['unit', 'department', 'performadetails' => function ($query) {
                     $query->with(['freebillingmaster' => function ($query) {
                         $query->with(['ticketmaster' => function ($query) {
                             $query->with(['field']);
                         }]);
                     }]);
                 }]);
             }
         ])->findWithoutFail($id);
        return $customerInvoiceDirect;
    }

    function getAuditItemInvoice($id)
    {
        $customerInvoiceDirect = $this->with(['company', 'customer', 'tax', 'createduser', 'bankaccount', 'currency','local_currency', 'approved_by' => function ($query) {
            $query->with('employee.details.designation')
                ->where('documentSystemID', 20);
        },
            'issue_item_details' => function ($query) {
                $query->with(['uom_issuing']);
            }
        ])->findWithoutFail($id);
        return $customerInvoiceDirect;
    }

    public function croneJobCustomerInvoiceReminder()
    {
        $threeDayBefore = Carbon::now()->addDays(3)->format('Y-m-d');
        $dueInvoices = AccountsReceivableLedger::where('documentSystemID', 20)
                                               ->with(['customer_invoice' => function($query) {
                                                    $query->with(['company']);
                                               }, 'transaction_currency', 'customer' => function($query) {
                                                    $query->with(['customer_contacts']);
                                               }])
                                               ->whereHas('customer_invoice', function($query) use ($threeDayBefore) {
                                                    $query->whereDate('invoiceDueDate', $threeDayBefore);
                                               })
                                               ->where('fullyInvoiced', '!=', 2)
                                               ->get();


        foreach ($dueInvoices as $key => $value) {
            $reciptVochers = CustomerReceivePaymentDetail::where('bookingInvCodeSystem', $value->documentCodeSystem)
                                                                    ->where('companySystemID', $value->companySystemID)
                                                                    ->where('addedDocumentSystemID', $value->documentSystemID)
                                                                    ->get();

            $totReceiveAmount = 0;
            foreach ($reciptVochers as $key1 => $row1) {
                $totalReceiveAmountTrans = CustomerReceivePaymentDetail::where('arAutoID', $value->arAutoID)
                        ->sum('receiveAmountTrans');

                $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, IFNULL(Sum(erp_matchdocumentmaster.matchedAmount),0) * -1 AS SumOfmatchedAmount')
                    ->where('companySystemID', $row1["companySystemID"])
                    ->where('PayMasterAutoId', $row1["bookingInvCodeSystem"])
                    ->where('documentSystemID', $row1["addedDocumentSystemID"])
                    ->groupBy('PayMasterAutoId', 'documentSystemID', 'BPVsupplierID', 'supplierTransCurrencyID')->first();

                if (!$matchedAmount) {
                    $matchedAmount['SumOfmatchedAmount'] = 0;
                }

                $totReceiveAmount = $totalReceiveAmountTrans + $matchedAmount['SumOfmatchedAmount'];
            }

            $remainingAmount = $value->custInvoiceAmount - $totReceiveAmount;

            if ($remainingAmount > 0) {
                foreach ($value->customer->customer_contacts as $key1 => $row) {

                    $dataEmail['empEmail'] = $row->contactPersonEmail;

                    $dataEmail['companySystemID'] = $value->companySystemID;

                    $temp = "Dear " . $value->customer->CustomerName . ',<p> This is a kind reminder of outstanding payment on invoice ' . $value->documentCode . " of Amount ".$value->transaction_currency->CurrencyCode." ".number_format($remainingAmount, $value->transaction_currency->DecimalPlaces)."<br> The Due date of payment is ".Carbon::parse($value->customer_invoice->invoiceDueDate)->format('Y-m-d').". Kindly note that the due date is approaching in 3 Days.</p><p>Please ignore if already paid.</p><br><p>Regards,</p><p>".$value->customer_invoice->company->CompanyName;
                    $dataEmail['alertMessage'] = "Invoice ".$value->documentCode." overdue notification";
                    $dataEmail['emailAlertMessage'] = $temp;
                    $sendEmail = \Email::sendEmailErp($dataEmail);
                }
            }
        }

        return ['status' => true];
    }

}
