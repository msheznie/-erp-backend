<?php


namespace App\helper;


use App\Models\CurrencyConversion;
use App\Models\DirectPaymentDetails;
use App\Models\HrMonthlyDeductionDetail;
use App\Models\HrMonthlyDeductionMaster;
use App\Models\HrPayrollHeaderDetails;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SrpEmployeeDetails;
use Carbon\Carbon;

class HrMonthlyDeductionService
{
    private $pv_id;
    private $pv_master;
    private $pv_details = [];
    private $current_user = [];
    private $md_code;
    private $serial_no;
    private $date_time;
    private $companyID;
    private $employee;
    private $local_currency;
    private $rpt_currency;
    private $emp_currency;
    private $document_date;
    private $monthly_ded_id;

    public function __construct($id)
    {
        $this->pv_id = $id;
        $this->date_time = Carbon::now();
    }

    public function create_monthly_deduction(){
        $this->pv_master = PaySupplierInvoiceMaster::find($this->pv_id);

        if( empty($this->pv_master) ){
            throw new \Exception("Payment voucher master data not found", 404);
        }

        if( empty($this->pv_master->createMonthlyDeduction) ){
            $msg = "No need to create the Monthly deduction document for this PV";
            return ['status'=> true, 'message'=> $msg];
        }

        $this->is_document_created();

        $this->companyID = $this->pv_master->companySystemID;

        $this->set_user_details();

        $this->setup_currency();

        $this->setup_emp_det();

        $this->create_header();

        $msg = "Payment voucher is approved. <br>";
        $msg .= "Monthly Deduction <strong class='text-dark'>[ $this->md_code ]</strong> is successfully created";

        $pv_date = Carbon::parse( $this->pv_master->BPVdate )->format('Y-m-d');

        if($pv_date != $this->document_date ){
            $msg .= " for ". Carbon::parse($this->document_date )->format('Y - F');
            $msg .= ",<br>Since the payroll processed on selected month";
        }

        return $msg;
    }

    function create_header(){
        $this->generator_code();

        $this->setup_document_date();

        $header = new HrMonthlyDeductionMaster;

        $header->monthlyDeductionCode = $this->md_code;
        $header->serialNo = $this->serial_no;
        $header->documentID = 'MD';
        $header->description = "System generated document - ".$this->pv_master->BPVcode;
        $header->currency = $this->local_currency->CurrencyCode;
        $header->dateMD = $this->document_date;
        $header->isNonPayroll = 'N';
        $header->pv_id = $this->pv_id;

        $header->confirmedYN = 1;
        $header->confirmedByEmpID = $this->current_user['user_id'];
        $header->confirmedByName = $this->current_user['user_name'];
        $header->confirmedDate = $this->date_time;

        $header->companyID = $this->companyID;
        $header->companyCode = $this->pv_master->companyID;

        $header->createdPCID = gethostname();
        $header->createdUserID = $this->current_user['user_id'];
        $header->createdUserName = $this->current_user['user_name'];
        $header->createdDateTime = $this->date_time;
        $header->timestamp = $this->date_time;

        $header->save();
        $this->monthly_ded_id = $header->id;

        $this->load_pv_details();

        $this->add_details();
    }

    function add_details(){
        $data = [];

        $this->setup_currency_conversion();

        foreach ($this->pv_details as $row){
            $ded_det = $row->monthly_deduction_det;

            $data[] = [
                'monthlyDeductionMasterID'=> $this->monthly_ded_id, 'empID'=> $this->employee->EIdNo,
                'accessGroupID'=> 0,
                'declarationID'=> $row->deductionType, 'GLCode'=> $ded_det->expenseGLCode,
                'categoryID'=> $ded_det->salaryCategoryID,

                'transactionCurrencyID'=> $this->emp_currency->currencyID,
                'transactionCurrency'=> $this->emp_currency->CurrencyCode,
                'transactionCurrencyDecimalPlaces'=> $this->emp_currency->DecimalPlaces,
                'transactionExchangeRate'=> 1, 'transactionAmount'=> ($row->DPAmount * $this->emp_currency->ExchangeRate),

                'companyLocalCurrencyID'=> $this->local_currency->currencyID,
                'companyLocalCurrency'=> $this->local_currency->CurrencyCode,
                'companyLocalCurrencyDecimalPlaces'=> $this->local_currency->DecimalPlaces,
                'companyLocalExchangeRate'=> $this->local_currency->ExchangeRate, 'companyLocalAmount'=> $row->localAmount,

                'companyReportingCurrencyID'=> $this->rpt_currency->currencyID,
                'companyReportingCurrency'=> $this->rpt_currency->CurrencyCode,
                'companyReportingCurrencyDecimalPlaces'=> $this->rpt_currency->DecimalPlaces,
                'companyReportingExchangeRate'=> $this->rpt_currency->ExchangeRate, 'companyReportingAmount'=> $row->comRptAmount,

                'companyID'=> $this->companyID, 'companyCode'=> $this->pv_master->companyID,
                'createdPCID'=> gethostname(), 'createdUserID'=> $this->current_user['user_id'],
                'createdDateTime'=> $this->date_time, 'createdUserName'=> $this->current_user['user_name'],
                'timestamp'=> $this->date_time
            ];
        }

        HrMonthlyDeductionDetail::insert($data);

        return true;
    }

    function generator_code(){
        $serialNo = HrMonthlyDeductionMaster::where('companyID', $this->companyID)
            ->max('serialNo');

        $serialNo += 1;

        $this->serial_no = $serialNo;

        $this->md_code = HrDocumentCodeService::generate(
            $this->companyID,
            $this->pv_master->companyID,
            'MD',
            $serialNo
        );
    }

    function set_user_details(){
        $user_id = Helper::getEmployeeSystemID();

        $user_name = SrpEmployeeDetails::find($user_id)->Ename2;

        $this->current_user = [
            'user_id' => $user_id,
            'user_name' => $user_name,
        ];
    }

    function setup_currency(){
        $company_det = Helper::companyCurrency( $this->companyID );

        if( empty($company_det->localcurrency) ){
            throw new \Exception("Company local currency details not found", 404);
        }

        if( empty($company_det->reportingcurrency) ){
            throw new \Exception("Company Reporting currency details not found", 404);
        }

        $this->local_currency = $company_det->localcurrency;

        $this->rpt_currency = $company_det->reportingcurrency;
    }

    function setup_emp_det(){
        $this->employee = SrpEmployeeDetails::with('currency')
            ->find( $this->pv_master->directPaymentPayeeEmpID );

        if( empty($this->employee) ){
            throw new \Exception("Employee details not found", 404);
        }


        $this->emp_currency = $this->employee->currency;

        if( empty($this->emp_currency) ){
            throw new \Exception("Employee currency details not found", 404);
        }
    }

    function setup_document_date(){
        $document_date = Carbon::parse( $this->pv_master->BPVdate );


        $is_processed = true;
        while( $is_processed ){
            $pv_date_arr = [
                'year'=> $document_date->format('Y'),
                'month'=> $document_date->format('m')
            ];

            //check payroll status of given month
            $is_processed = HrPayrollHeaderDetails::where('EmpID', $this->employee->EIdNo)
                ->whereHas('master', function ($q) use ($pv_date_arr){
                    $q->where('payrollYear', $pv_date_arr['year'])
                        ->where('payrollMonth', $pv_date_arr['month']);
                })
                ->with('master')
                ->first();

            if($is_processed){
                //if payroll processed check next month payroll
                $document_date = $document_date->firstOfMonth();
                $document_date = $document_date->addMonth();
            }
        }

        $this->document_date = $document_date->format('Y-m-d');
    }

    function load_pv_details(){
        $this->pv_details = DirectPaymentDetails::where('directPaymentAutoID', $this->pv_id)
            ->selectRaw('DPAmount, deductionType, localAmount, comRptAmount, localCurrencyER, comRptCurrencyER')
            ->whereHas('monthly_deduction_det')
            ->with('monthly_deduction_det:monthlyDeclarationID,salaryCategoryID,expenseGLCode')
            ->get();

        if( empty($this->pv_details) ){
            throw new \Exception("PV details not found", 404);
        }
    }

    function setup_currency_conversion()
    {
        $pv_currency = $this->pv_master->directPayeeCurrency;
        $emp_currency = $this->emp_currency->currencyID;

        if( $emp_currency == $pv_currency ){
            $this->emp_currency->ExchangeRate = 1;
            $this->local_currency->ExchangeRate = $this->pv_details[0]->localCurrencyER;
            $this->rpt_currency->ExchangeRate = $this->pv_details[0]->comRptCurrencyER;

            return true;
        }

        $this->emp_currency->ExchangeRate = self::currency_conversion($emp_currency, $pv_currency);
        $this->local_currency->ExchangeRate = self::currency_conversion($emp_currency, $this->local_currency->currencyID);
        $this->rpt_currency->ExchangeRate = self::currency_conversion($emp_currency, $this->rpt_currency->currencyID);

        return true;
    }

    public static function currency_conversion($master, $sub){
        if($master == $sub){
            return 1;
        }

        return CurrencyConversion::where('masterCurrencyID', $master)
                    ->where('subCurrencyID', $sub)
                    ->value('conversion');
    }

    function is_document_created(){
        $doc = HrMonthlyDeductionMaster::where('pv_id', $this->pv_id)->first();

        if($doc){
            $msg = "Monthly deduction document already created.  <br> [ {$doc->monthlyDeductionCode} ]";
            throw new \Exception($msg, 500);
        }

        return false;
    }
}
