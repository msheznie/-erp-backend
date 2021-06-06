<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceDirect;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\helper\StatusService;

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

    public function customerInvoiceListQuery($request, $input, $search = '') {

        $invMaster = DB::table('erp_custinvoicedirect')
            ->leftjoin('currencymaster', 'custTransactionCurrencyID', '=', 'currencyID')
            ->leftjoin('employees', 'erp_custinvoicedirect.createdUserSystemID', '=', 'employees.employeeSystemID')
            ->leftjoin('customermaster', 'customermaster.customerCodeSystem', '=', 'erp_custinvoicedirect.customerID')
            ->where('companySystemID', $input['companyId'])
            ->where('erp_custinvoicedirect.documentSystemID', $input['documentId']);


        /* $invMaster = CustomerInvoiceDirect::where('companySystemID', $input['companyId']);
         $invMaster->where('documentSystemID', $input['documentId']);
         $invMaster->with(['currency', 'createduser', 'customer']);*/


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
                $invMaster->where('isPerforma', $input['isProforma']);
            }
        }

        if (array_key_exists('customerID', $input)) {
            if (($input['customerID'] != '')) {
                $invMaster->where('erp_custinvoicedirect.customerID', $input['customerID']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $invMaster->whereMonth('bookingDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invMaster->whereYear('bookingDate', '=', $input['year']);
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
                    ->orWhere('customerInvoiceNo', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%")
                    ->orWhere('bookingAmountTrans', 'LIKE', "%{$search_without_comma}%");
            });
        }

        $request->request->remove('search.value');
        $invMaster->select('bookingInvCode', 'CurrencyCode', 'erp_custinvoicedirect.approvedDate', 'customerInvoiceNo', 'erp_custinvoicedirect.comments', 'empName', 'DecimalPlaces', 'erp_custinvoicedirect.confirmedYN', 'erp_custinvoicedirect.approved', 'erp_custinvoicedirect.canceledYN', 'erp_custinvoicedirect.customerInvoiceDate', 'erp_custinvoicedirect.refferedBackYN', 'custInvoiceDirectAutoID', 'customermaster.CutomerCode', 'customermaster.CustomerName', 'bookingAmountTrans', 'VATAmount', 'isPerforma', 'returnStatus');

        return $invMaster;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x]['Invoice Code'] = $val->bookingInvCode;
                $data[$x]['Approved Date'] = \Helper::dateFormat($val->approvedDate);
                $data[$x]['Invoice Type'] = StatusService::getCustomerInvoiceType($val->isPerforma);
                $data[$x]['Customer'] = $val->CustomerName;
                $data[$x]['Invoice'] = $val->customerInvoiceNo;
                $data[$x]['Invoice Date'] = \Helper::dateFormat($val->customerInvoiceDate);
                $data[$x]['Comments'] = $val->comments;
                $data[$x]['Created By'] = $val->empName;
                $data[$x]['Currency'] = $val->CurrencyCode;
                $data[$x]['Amount'] = number_format($val->bookingAmountTrans + $val->VATAmount, $val->DecimalPlaces? $val->DecimalPlaces : '', ".", "");
                $data[$x]['Status'] = StatusService::getStatus($val->canceledYN, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }

}
