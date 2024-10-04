<?php

namespace App\Repositories;

use App\Models\CustomerReceivePayment;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class CustomerReceivePaymentRepository
 * @package App\Repositories
 * @version August 24, 2018, 11:58 am UTC
 *
 * @method CustomerReceivePayment findWithoutFail($id, $columns = ['*'])
 * @method CustomerReceivePayment find($id, $columns = ['*'])
 * @method CustomerReceivePayment first($columns = ['*'])
*/
class CustomerReceivePaymentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYPeriodDateFrom',
        'FYEnd',
        'FYPeriodDateTo',
        'PayMasterAutoId',
        'intercompanyPaymentID',
        'intercompanyPaymentCode',
        'custPaymentReceiveCode',
        'custPaymentReceiveDate',
        'narration',
        'customerID',
        'customerGLCodeSystemID',
        'customerGLCode',
        'custTransactionCurrencyID',
        'custTransactionCurrencyER',
        'bankID',
        'bankAccount',
        'bankCurrency',
        'bankCurrencyER',
        'payeeYN',
        'PayeeSelectEmp',
        'PayeeEmpID',
        'PayeeName',
        'PayeeCurrency',
        'custChequeNo',
        'custChequeDate',
        'custChequeBank',
        'receivedAmount',
        'localCurrencyID',
        'localCurrencyER',
        'localAmount',
        'companyRptCurrencyID',
        'companyRptCurrencyER',
        'companyRptAmount',
        'bankAmount',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'trsCollectedYN',
        'trsCollectedByEmpID',
        'trsCollectedByEmpName',
        'trsCollectedDate',
        'trsClearedYN',
        'trsClearedDate',
        'trsClearedByEmpID',
        'trsClearedByEmpName',
        'trsClearedAmount',
        'bankClearedYN',
        'bankClearedAmount',
        'bankReconciliationDate',
        'bankClearedDate',
        'bankClearedByEmpID',
        'bankClearedByEmpName',
        'documentType',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'RollLevForApp_curr',
        'expenseClaimOrPettyCash',
        'createdUserGroup',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp',
        'isFromApi'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerReceivePayment::class;
    }

    public function customerReceiveListQuery($request, $input, $search = '') {

        $master = CustomerReceivePayment::with('bank','project','localCurrency','rptCurrency')->where('erp_customerreceivepayment.companySystemID', $input['companyId'])
        ->leftjoin('currencymaster as transCurr', 'custTransactionCurrencyID', '=', 'transCurr.currencyID')
        ->leftjoin('currencymaster as bankCurr', 'bankCurrency', '=', 'bankCurr.currencyID')
        ->leftjoin('employees', 'erp_customerreceivepayment.createdUserSystemID', '=', 'employees.employeeSystemID')
        ->leftjoin('customermaster', 'customermaster.customerCodeSystem', '=', 'erp_customerreceivepayment.customerID')
        ->leftJoin('erp_bankledger', function ($join) {
            $join->on('erp_bankledger.documentSystemCode', '=', 'erp_customerreceivepayment.custReceivePaymentAutoID');
            $join->on('erp_bankledger.companySystemID', '=', 'erp_customerreceivepayment.companySystemID');
            $join->on('erp_bankledger.documentSystemID', '=', 'erp_customerreceivepayment.documentSystemID');
        })
        ->where('erp_customerreceivepayment.documentSystemID', $input['documentId']);

    if (array_key_exists('confirmedYN', $input)) {
        if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
            $master->where('erp_customerreceivepayment.confirmedYN', $input['confirmedYN']);
        }
    }
    if (array_key_exists('approved', $input)) {
        if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
            $master->where('erp_customerreceivepayment.approved', $input['approved']);
        }
    }

    if (array_key_exists('cancelYN', $input)) {
        if (($input['cancelYN'] == 0 || $input['cancelYN'] == -1) && !is_null($input['cancelYN'])) {
            $master->where('erp_customerreceivepayment.cancelYN', $input['cancelYN']);
        }
    }

    if (array_key_exists('month', $input)) {
        if ($input['month'] && !is_null($input['month'])) {
            $master->whereMonth('custPaymentReceiveDate', '=', $input['month']);
        }
    }

    if (array_key_exists('year', $input)) {
        if ($input['year'] && !is_null($input['year'])) {
            $master->whereYear('custPaymentReceiveDate', '=', $input['year']);
        }
    }
    if (array_key_exists('documentType', $input)) {
        if ($input['documentType'] && !is_null($input['documentType'])) {
            $master->where('documentType', '=', $input['documentType']);
        }
    }
    if (array_key_exists('trsClearedYN', $input)) {
        if ($input['trsClearedYN'] && !is_null($input['trsClearedYN'])) {
            $master->where('erp_bankledger.trsClearedYN', '=', $input['trsClearedYN']);
        }
    }

    if (array_key_exists('createdBy', $input)) {
        if ($input['createdBy'] && !is_null($input['createdBy'])) {

            $createdBy = $request['createdBy'];
            $createdBy = (array)$createdBy;
            $createdBy = collect($createdBy)->pluck('id');

            $master->whereIn('erp_customerreceivepayment.createdUserSystemID',$createdBy);
        }
    }

    $master = $master->select([
        'custPaymentReceiveCode',
        'erp_customerreceivepayment.localCurrencyID',
        'erp_customerreceivepayment.companyRptCurrencyID',
        'erp_customerreceivepayment.localAmount',
        'erp_customerreceivepayment.companyRptAmount',
        'transCurr.CurrencyCode as transCurrencyCode',
        'bankCurr.CurrencyCode as bankCurrencyCode',
        'documentType',
        'erp_customerreceivepayment.approvedDate',
        'erp_customerreceivepayment.confirmedDate',
        'erp_customerreceivepayment.createdDateTime',
        'custPaymentReceiveDate',
        'erp_customerreceivepayment.narration',
        'empName',
        'transCurr.DecimalPlaces as transDecimal',
        'bankCurr.DecimalPlaces as bankDecimal',
        'erp_customerreceivepayment.refferedBackYN',
        'erp_customerreceivepayment.confirmedYN',
        'erp_customerreceivepayment.approved',
        'erp_customerreceivepayment.cancelYN',
        'custReceivePaymentAutoID',
        'customermaster.CutomerCode',
        'customermaster.CustomerName',
        'receivedAmount as receivedAmount',
        'bankAmount as bankAmount',
        'erp_bankledger.trsClearedYN as trsClearedYN',
        'erp_customerreceivepayment.bankAccount',
        'erp_customerreceivepayment.payeeTypeID',
        'erp_customerreceivepayment.PayeeName',
        'employees.empName',
        'employees.empID',
        'erp_customerreceivepayment.projectID'
    ]);

    if ($search) {
        $search = str_replace("\\", "\\\\", $search);
        $search_without_comma = str_replace(",", "", $search);
        $master = $master->where(function ($query) use ($search, $search_without_comma) {
            $query->Where('custPaymentReceiveCode', 'LIKE', "%{$search}%")
                ->orwhere('employees.empName', 'LIKE', "%{$search}%")
                ->orwhere('customermaster.CutomerCode', 'LIKE', "%{$search}%")
                ->orwhere('customermaster.CustomerName', 'LIKE', "%{$search}%")
                ->orWhere('erp_customerreceivepayment.narration', 'LIKE', "%{$search}%")
                ->orWhere('erp_customerreceivepayment.receivedAmount', 'LIKE', "%{$search_without_comma}%")
                ->orWhere('erp_customerreceivepayment.bankAmount', 'LIKE', "%{$search_without_comma}%");
        });
    }

        return $master;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                if($val->documentType == 13){
                    $receiptType = 'Customer Invoice Receipt';
                }
                if($val->documentType == 14){
                    $receiptType = 'Direct Receipt';
                }
                if($val->documentType == 15){
                    $receiptType = 'Advance Receipt';
                }

                $payeeType = '';
                if($val->payeeTypeID ==1){
                    $payeeType = 'Customer';
                }
                if($val->payeeTypeID == 2){
                    $payeeType = 'Employee';
                }
                if($val->payeeTypeID == 3){
                    $payeeType = 'Other';
                }
                
                if(isset($val->transDecimal) && $val->transDecimal != null){
                    $transDecimal = $val->transDecimal;
                } else {
                    $transDecimal = 0;
                }

                if(isset($val->bankDecimal) && $val->bankDecimal != null){
                    $bankDecimal = $val->bankDecimal;
                } else {
                    $bankDecimal = 0;
                }
                $data[$x]['BRV Code'] = $val->custPaymentReceiveCode;
                $data[$x]['Receipt Type'] = $receiptType;
                $data[$x]['Customer'] = $val->CutomerCode;
                $data[$x]['Customer Name'] = $val->CustomerName;
                $data[$x]['Bank'] = $val->bank? $val->bank->bankName : '';
                $data[$x]['Account'] = $val->bank? $val->bank->AccountNo : '';
                $data[$x]['Payee Type'] = $payeeType? $payeeType : '';
                $data[$x]['Other'] = $val->PayeeName? $val->PayeeName : '';
                $data[$x]['Project'] = $val->project? $val->project->description : '';
                $data[$x]['BRV Date'] = \Helper::dateFormat($val->custPaymentReceiveDate);
                $data[$x]['Narration'] = $val->narration;
                $data[$x]['Created By'] = $val->empName;
                $data[$x]['Created At'] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x]['Confirmed on'] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x]['Approved on'] = \Helper::convertDateWithTime($val->approvedDate);
                $data[$x]['Transaction Currency'] = $val->transCurrencyCode;
                $data[$x]['Transaction Amount'] = $val->receivedAmount? number_format(abs($val->receivedAmount), $transDecimal, ".", "") : '';
                $data[$x]['Bank Currency'] = $val->bankCurrencyCode;
                $data[$x]['Bank Amount'] = $val->bankAmount? number_format(abs($val->bankAmount), $bankDecimal, ".", "") : '';

				$data[$x]['Local Currency'] = $val->localCurrencyID? ($val->localCurrency? $val->localCurrency->CurrencyCode : '') : '';
                $data[$x]['Local Amount'] = $val->localCurrency? number_format($val->localAmount,  $val->localCurrency->DecimalPlaces, ".", "") : '';
                $data[$x]['Reporting Currency'] = $val->companyRptCurrencyID? ($val->rptCurrency? $val->rptCurrency->CurrencyCode : '') : '';
                $data[$x]['Reporting Amount'] = $val->rptCurrency? number_format($val->companyRptAmount,  $val->rptCurrency->DecimalPlaces, ".", "") : '';

                $data[$x]['Treasury Cleared'] = $val->trsClearedYN == -1? 'Yes' : 'No';
                $data[$x]['Status'] = StatusService::getStatus($val->cancelYN, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
