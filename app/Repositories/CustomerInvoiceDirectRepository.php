<?php

namespace App\Repositories;

use App\Models\AccountsReceivableLedger;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\MatchDocumentMaster;
use App\Models\CustomerInvoiceDirect;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\helper\StatusService;
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
        'createdFrom',
        'createdDateTime',
        'timestamp',
        'isPOS'
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


        $customerInvoiceDirect = $this->with(['segment','company', 'customer', 'tax', 'createduser', 
        'bankaccount' => function ($query) {
            $query->with('currency');
        }, 'currency', 'local_currency',
        'approved_by' => function ($query) {
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
         $customerInvoiceDirect = $this->with(['segment','company', 'customer', 'tax', 'createduser', 
         'bankaccount' => function ($query) {
            $query->with('currency');
        }, 'currency','local_currency', 'approved_by' => function ($query) {
             $query->with('employee.details.designation')
                 ->where('documentSystemID', 20);
         }, 'invoicedetails'
         => function ($query) {
                 $query->with(['unit', 'department', 'project', 'performadetails' => function ($query) {
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
        $customerInvoiceDirect = $this->with([
            'segment', 'company', 'customer', 'tax', 'createduser',
            'bankaccount' => function ($query) {
                $query->with('currency');
            },
            'currency', 'local_currency',
            'approved_by' => function ($query) {
                $query->with('employee.details.designation')
                    ->where('documentSystemID', 20);
            },
            'issue_item_details' => function ($query) {
                $query->with(['uom_issuing', 'sales_quotation', 'project'])
                    ->selectRaw('*, 
                 CASE 
                    WHEN VATApplicableOn = 2 THEN (sellingCost * qtyIssuedDefaultMeasure) - discountAmount 
                    ELSE sellingCost * qtyIssuedDefaultMeasure 
                 END AS taxable_amount');
            }
        ])->find($id);
        return $customerInvoiceDirect;
    }

    public function customerInvoiceListQuery($request, $input, $search = '', $customerID) {

        $invMaster = DB::table('erp_custinvoicedirect')
        ->leftJoin('currencymaster as TransactionCurrency', 'erp_custinvoicedirect.custTransactionCurrencyID', '=', 'TransactionCurrency.currencyID')
        ->leftJoin('currencymaster as LocalCurrency', 'erp_custinvoicedirect.localCurrencyID', '=', 'LocalCurrency.currencyID')
        ->leftJoin('currencymaster as ReportingCurrency', 'erp_custinvoicedirect.companyReportingCurrencyID', '=', 'ReportingCurrency.currencyID')
        ->leftJoin('employees', 'erp_custinvoicedirect.createdUserSystemID', '=', 'employees.employeeSystemID')
        ->leftJoin('customermaster', 'customermaster.customerCodeSystem', '=', 'erp_custinvoicedirect.customerID')
        ->where('erp_custinvoicedirect.companySystemID', $input['companyId'])
        ->where('erp_custinvoicedirect.documentSystemID', $input['documentId']);
    

        /* $invMaster = CustomerInvoiceDirect::where('companySystemID', $input['companyId']);
         $invMaster->where('documentSystemID', $input['documentId']);
         $invMaster->with(['currency', 'createduser', 'customer']);*/
        if (array_key_exists('createdBy', $input)) {
            if($input['createdBy'] && !is_null($input['createdBy']))
            {
                $createdBy = collect($input['createdBy'])->pluck('id')->toArray();
                $invMaster->whereIn('erp_custinvoicedirect.createdUserSystemID', $createdBy);
            }

        }

        if (array_key_exists('invConfirmedYN', $input)) {
            if (($input['invConfirmedYN'] == 0 || $input['invConfirmedYN'] == 1) && !is_null($input['invConfirmedYN'])) {
                $invMaster->where('erp_custinvoicedirect.confirmedYN', $input['invConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $invMaster->where('erp_custinvoicedirect.approved', $input['approved']);
            }
        }

        if (array_key_exists('canceledYN', $input)) {
            if (($input['canceledYN'] == 0 || $input['canceledYN'] == -1) && !is_null($input['canceledYN'])) {
                $invMaster->where('erp_custinvoicedirect.canceledYN', $input['canceledYN']);
            }
        }

        if (array_key_exists('isProforma', $input)) {
            if (!is_null($input['isProforma'])) {
                $invMaster->where('erp_custinvoicedirect.isPerforma', $input['isProforma']);
            }
        }

        if (array_key_exists('customerID', $input)) {
            if (($input['customerID'] != '')) {
                $invMaster->whereIn('erp_custinvoicedirect.customerID', $customerID);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $invMaster->whereMonth('erp_custinvoicedirect.bookingDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invMaster->whereYear('erp_custinvoicedirect.bookingDate', '=', $input['year']);
            }
        }

        if (array_key_exists('fromDate', $input) && array_key_exists('toDate', $input)) {
            if (($input['fromDate'] && !is_null($input['fromDate'])) && ($input['toDate'] && !is_null($input['toDate']))) {
                $fromDate = Carbon::parse($input['fromDate'])->format('Y-m-d') . ' 00:00:00';
                $toDate = Carbon::parse($input['toDate'])->format('Y-m-d') . ' 23:59:59';

                $invMaster->whereBetween('erp_custinvoicedirect.bookingDate', [$fromDate, $toDate]);
            }
        }

        if (!isset($input['fromDate']) && array_key_exists('toDate', $input)) {
            if (($input['toDate'] && !is_null($input['toDate']))) {
                $toDate = Carbon::parse($input['toDate'])->format('Y-m-d') . ' 23:59:59';
                $invMaster->where('erp_custinvoicedirect.bookingDate', '<=' , $toDate);
            }
        }

        /*  if (array_key_exists('year', $input)) {
              if ($input['year'] && !is_null($input['year'])) {
                  $invoiceDate = $input['year'] . '-12-31';
                  if (array_key_exists('month', $input)) {
                      if ($input['month'] && !is_null($input['month'])) {
                          $invoiceDate = $input['year'] . '-' . $input['month'] . '-31';
                      }
                  }

                  $invMaster->where('bookingDate', '<=', $invoiceDate);

              }
          }*/

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $search_without_comma = str_replace(",", "", $search);
            $invMaster = $invMaster->where(function ($query) use ($search, $search_without_comma) {
                $query->Where('bookingInvCode', 'LIKE', "%{$search}%")
                    ->orwhere('employees.empName', 'LIKE', "%{$search}%")
                    ->orwhere('customermaster.CustomerName', 'LIKE', "%{$search}%")
                    ->orwhere('customermaster.CutomerCode', 'LIKE', "%{$search}%")
                    ->orWhere('erp_custinvoicedirect.customerInvoiceNo', 'LIKE', "%{$search}%")
                    ->orWhere('erp_custinvoicedirect.comments', 'LIKE', "%{$search}%")
                    ->orWhere('erp_custinvoicedirect.bookingAmountTrans', 'LIKE', "%{$search_without_comma}%");
            });
        }

        $request->request->remove('search.value');
    
    $invMaster->select(
        'erp_custinvoicedirect.isUpload',
        'erp_custinvoicedirect.isPOS',
        'erp_custinvoicedirect.bookingInvCode',
        'erp_custinvoicedirect.postedDate',
        'TransactionCurrency.CurrencyCode as CurrencyCode',
        'erp_custinvoicedirect.approvedDate',
        'erp_custinvoicedirect.customerInvoiceNo',
        'erp_custinvoicedirect.comments',
        'employees.empName',
        'TransactionCurrency.DecimalPlaces',
        'erp_custinvoicedirect.confirmedYN',
        'erp_custinvoicedirect.approved',
        'erp_custinvoicedirect.canceledYN',
        'erp_custinvoicedirect.customerInvoiceDate',
        'erp_custinvoicedirect.refferedBackYN',
        'erp_custinvoicedirect.custInvoiceDirectAutoID',
        'customermaster.CutomerCode',
        'customermaster.CustomerName',
        'erp_custinvoicedirect.bookingAmountTrans',
        'erp_custinvoicedirect.bookingAmountLocal',
        'erp_custinvoicedirect.bookingAmountRpt',
        'erp_custinvoicedirect.VATAmount',
        'erp_custinvoicedirect.VATAmountLocal',
        'erp_custinvoicedirect.VATAmountRpt',
        'erp_custinvoicedirect.isPerforma',
        'erp_custinvoicedirect.returnStatus',
        'erp_custinvoicedirect.createdFrom',
        
        'LocalCurrency.CurrencyCode as LocalCurrencyCode',
        'LocalCurrency.DecimalPlaces as LocalCurrencyDecimalPlaces',
        'ReportingCurrency.CurrencyCode as ReportingCurrencyCode',
        'ReportingCurrency.DecimalPlaces as ReportingCurrencyDecimalPlaces',

        'erp_custinvoicedirect.isAutoGenerated'
    );

        return $invMaster;
    }

    public function setExportExcelData($dataSet) {
        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.invoice_code')] = $val->bookingInvCode;
                $data[$x][trans('custom.approved_date')] = \Helper::dateFormat($val->approvedDate);
                $data[$x][trans('custom.invoice_type')] = StatusService::getCustomerInvoiceType($val->isPerforma);
                $data[$x][trans('custom.customer')] = $val->CustomerName;
                $data[$x][trans('custom.invoice')] = $val->customerInvoiceNo;
                $data[$x][trans('custom.invoice_date')] = \Helper::dateFormat($val->customerInvoiceDate);
                $data[$x][trans('custom.comments')] = $val->comments;
                $data[$x][trans('custom.created_by')] = $val->empName;
                $data[$x][trans('custom.transaction_currency')] = $val->CurrencyCode;
                $data[$x][trans('custom.transaction_amount')] = number_format($val->bookingAmountTrans + $val->VATAmount, $val->DecimalPlaces? $val->DecimalPlaces : 3, ".", "");

                $data[$x][trans('custom.local_currency')] = $val->LocalCurrencyCode? $val->LocalCurrencyCode : '';
                $data[$x][trans('custom.local_amount')] = $val->LocalCurrencyCode? number_format($val->bookingAmountLocal + $val->VATAmountLocal,  $val->LocalCurrencyDecimalPlaces, ".", "") : '';
                $data[$x][trans('custom.reporting_currency')] = $val->ReportingCurrencyCode? $val->ReportingCurrencyCode : '';
                $data[$x][trans('custom.reporting_amount')] = $val->ReportingCurrencyCode? number_format($val->bookingAmountRpt + $val->VATAmountRpt,  $val->ReportingCurrencyDecimalPlaces, ".", "") : '';
                
                $data[$x][trans('custom.status')] = StatusService::getStatus($val->canceledYN, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }

    public static function croneJobCustomerInvoiceReminder()
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

                    $temp = "<p>Dear " . $value->customer->CustomerName . ',</p><p>This is a kind reminder of outstanding payment on invoice ' . $value->documentCode . " of Amount ".$value->transaction_currency->CurrencyCode." ".number_format($remainingAmount, $value->transaction_currency->DecimalPlaces).".</p> <p>The Due date of payment is ".Carbon::parse($value->customer_invoice->invoiceDueDate)->format('Y-m-d').". Kindly note that the due date is approaching in 3 Days.</p><p>Please ignore if already paid.</p><br><p>Regards,</p><p>".$value->customer_invoice->company->CompanyName;
                    $dataEmail['alertMessage'] = "Invoice ".$value->documentCode." overdue notification";
                    $dataEmail['emailAlertMessage'] = $temp;
                    $sendEmail = \Email::sendEmailErp($dataEmail);
                }
            }
        }

        return ['status' => true];
    }

}
