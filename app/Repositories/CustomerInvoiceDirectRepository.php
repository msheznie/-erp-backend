<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceDirect;
use InfyOm\Generator\Common\BaseRepository;

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


        $customerInvoiceDirect = $this->with(['company', 'customer', 'tax', 'createduser', 'bankaccount', 'currency', 'approved_by' => function ($query) {
            $query->with('employee.details.designation')
                ->where('documentSystemID', 20);
        }, 'invoicedetail'
        => function ($query) {
                $query1 = $query;
                $bill = $query1->first()->toArray();
                $companyID = $bill['companyID'];

                $query->with(['unit', 'department','billmaster'=>function ($query) use ($companyID){
                    $query->where('companyID',$companyID);
                    $query1 = $query;
                    $bill = $query1->first()->toArray();
                    $Ticketno = $bill['Ticketno'];
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
         $customerInvoiceDirect = $this->with(['company', 'customer', 'tax', 'createduser', 'bankaccount', 'currency', 'approved_by' => function ($query) {
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

}
