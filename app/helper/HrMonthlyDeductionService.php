<?php


namespace App\helper;


use App\Models\CurrencyConversion;
use App\Models\CurrencyMaster;
use App\Models\DirectPaymentDetails;
use App\Models\HrMonthlyDeductionDetail;
use App\Models\HrMonthlyDeductionMaster;
use App\Models\HrPayrollHeaderDetails;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SrpEmployeeDetails;
use App\Models\ExpenseEmployeeAllocation;
use App\Models\BookInvSuppMaster;
use App\Models\Employee;
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

        $this->setup_document_date($this->pv_master->BPVdate);

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
                'transactionExchangeRate'=> 1, 'transactionAmount'=> (($row->DPAmount + $row->vatAmount) * $this->emp_currency->ExchangeRate),

                'companyLocalCurrencyID'=> $this->local_currency->currencyID,
                'companyLocalCurrency'=> $this->local_currency->CurrencyCode,
                'companyLocalCurrencyDecimalPlaces'=> $this->local_currency->DecimalPlaces,
                'companyLocalExchangeRate'=> $this->local_currency->ExchangeRate, 'companyLocalAmount'=> (($row->DPAmount + $row->vatAmount) * $this->emp_currency->ExchangeRate)/$this->local_currency->ExchangeRate,

                'companyReportingCurrencyID'=> $this->rpt_currency->currencyID,
                'companyReportingCurrency'=> $this->rpt_currency->CurrencyCode,
                'companyReportingCurrencyDecimalPlaces'=> $this->rpt_currency->DecimalPlaces,
                'companyReportingExchangeRate'=> $this->rpt_currency->ExchangeRate, 'companyReportingAmount'=> (($row->DPAmount + $row->vatAmount) * $this->emp_currency->ExchangeRate)/$this->rpt_currency->ExchangeRate,

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

    function setup_document_date($docDate){
        $document_date = Carbon::parse($docDate);


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
            ->selectRaw('DPAmount, vatAmount, deductionType, localAmount, comRptAmount, localCurrencyER, comRptCurrencyER')
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

    public static function createMonthlyDeductionForSupplierInvoice($supplierInvoice)
    {
        $supplierInvoiceData = BookInvSuppMaster::find($supplierInvoice['autoID']);

        if(!$supplierInvoiceData->createMonthlyDeduction){
            $msg = "No need to create the Monthly deduction document for this PV";
            return ['status'=> true, 'message'=> $msg];
        }


        $employeeDeductions = ExpenseEmployeeAllocation::where('documentSystemCode', $supplierInvoiceData->bookingSuppMasInvAutoID)
                                                       ->with(['invoice_detail' => function($query) {
                                                            $query->with(['monthly_deduction_det'])
                                                                  ->whereHas('monthly_deduction_det');                                                        
                                                       }, 'supplier_invoice'])
                                                       ->whereHas('invoice_detail', function($query) {
                                                            $query->whereHas('monthly_deduction_det');
                                                       })
                                                       ->whereHas('supplier_invoice')
                                                       ->get();

        $groupedData = collect($employeeDeductions)->groupBy(function ($item, $key) {
                                                        return Carbon::parse($item['dateOfDeduction'])->format('m-Y');
                                                    });



        $user_id = Helper::getEmployeeSystemID();
        $empDetails = SrpEmployeeDetails::with('currency')->find($user_id);
        $user_name = ($empDetails) ? $empDetails->Ename2 : null;

        $company_det = Helper::companyCurrency($supplierInvoiceData->companySystemID);

        if(empty($company_det->localcurrency)){
            return ['status'=> false, 'message'=> "Company local currency details not found"];
        }

        if(empty($company_det->reportingcurrency)){
            return ['status'=> false, 'message'=> "Company Reporting currency details not found"];
        }

        $local_currency = $company_det->localcurrency;

        $rpt_currency = $company_det->reportingcurrency;


        foreach ($groupedData as $key => $value) {
            if (count($value) > 0) {
                
                $serialNo = HrMonthlyDeductionMaster::where('companyID', $supplierInvoiceData->companySystemID)
                                                    ->max('serialNo');

                $serialNo += 1;

                $serial_no = $serialNo;

                $md_code = HrDocumentCodeService::generate($supplierInvoiceData->companySystemID,$supplierInvoiceData->companyID,'MD',$serialNo);


                $documentDate = self::setupDocumentDate($value[0]['dateOfDeduction'], $value[0]['employeeSystemID']);          


                $doc = HrMonthlyDeductionMaster::where('supplierInvoiceID', $supplierInvoiceData->bookingSuppMasInvAutoID)
                                               ->whereDate('dateMD', $documentDate)
                                               ->first();

                if ($doc) {
                    return ['status'=> false, 'message'=> "Monthly deduction already created"];
                }


                $header = new HrMonthlyDeductionMaster;

                $header->monthlyDeductionCode = $md_code;
                $header->serialNo = $serial_no;
                $header->documentID = 'MD';
                $header->description = "System generated document - ".$supplierInvoiceData->bookingInvCode;
                $header->currency = $local_currency->CurrencyCode;
                $header->dateMD = $documentDate;
                $header->isNonPayroll = 'N';
                $header->supplierInvoiceID = $supplierInvoice['autoID'];

                $header->confirmedYN = 1;
                $header->confirmedByEmpID = $user_id;
                $header->confirmedByName = $user_name;
                $header->confirmedDate = Carbon::now();

                $header->companyID = $supplierInvoiceData->companySystemID;
                $header->companyCode = $supplierInvoiceData->companyID;

                $header->createdPCID = gethostname();
                $header->createdUserID = $user_id;
                $header->createdUserName = $user_name;
                $header->createdDateTime = Carbon::now();
                $header->timestamp = Carbon::now();

                $header->save();
                
                $monthly_ded_id = $header->id;
                self::addDetailsForMonthlyDeduction($monthly_ded_id, $value, $empDetails, $local_currency, $rpt_currency, $supplierInvoiceData, $user_id, $user_name);
            }
        }
        
        return ['status'=> true, 'message'=> "Monthly deduction created successfully"];
    }


    public static function addDetailsForMonthlyDeduction($monthly_ded_id, $details, $empDetails, $local_currency, $rpt_currency, $supplierInvoiceData, $user_id, $user_name)
    {
        $data = [];
        $employeeCurrency = ($empDetails) ? $empDetails->currency : null;


        foreach ($details as $row){
            $ded_det = $row->invoice_detail->monthly_deduction_det;

            $sup_currency = $row->supplier_invoice->supplierTransactionCurrencyID;
            $emp_currency = ($employeeCurrency) ? $employeeCurrency->currencyID : null;




            if($emp_currency == $sup_currency){
                $employeeCurrency->ExchangeRate = 1;
                $local_currency->ExchangeRate = $row->invoice_detail->localCurrencyER;
                $rpt_currency->ExchangeRate = $row->invoice_detail->comRptCurrencyER;
            }

            if ($employeeCurrency) {
                $employeeCurrency->ExchangeRate = self::currency_conversion($emp_currency, $sup_currency);
            }

            $local_currency->ExchangeRate = self::currency_conversion($emp_currency, $local_currency->currencyID);
            $rpt_currency->ExchangeRate = self::currency_conversion($emp_currency, $rpt_currency->currencyID);

            $transCurrency = CurrencyMaster::where('currencyID', $sup_currency)->first();

            $data[] = [
                'monthlyDeductionMasterID'=> $monthly_ded_id, 'empID'=> $row->employeeSystemID,
                'accessGroupID'=> 0,
                'declarationID'=> $row->invoice_detail->deductionType, 'GLCode'=> $ded_det->expenseGLCode,
                'categoryID'=> $ded_det->salaryCategoryID,

                'transactionCurrencyID'=> ($sup_currency) ? $sup_currency : 0,
                'transactionCurrency'=> ($transCurrency) ? $transCurrency->CurrencyCode : null,
                'transactionCurrencyDecimalPlaces'=> ($transCurrency) ? $transCurrency->DecimalPlaces : null,
                'transactionExchangeRate'=> 1, 'transactionAmount'=> ($row->amount * (($employeeCurrency) ? $employeeCurrency->ExchangeRate : 1)),

                'companyLocalCurrencyID'=> $local_currency->currencyID,
                'companyLocalCurrency'=> $local_currency->CurrencyCode,
                'companyLocalCurrencyDecimalPlaces'=> $local_currency->DecimalPlaces,
                'companyLocalExchangeRate'=> $local_currency->ExchangeRate, 'companyLocalAmount'=> $row->amountLocal,

                'companyReportingCurrencyID'=> $rpt_currency->currencyID,
                'companyReportingCurrency'=> $rpt_currency->CurrencyCode,
                'companyReportingCurrencyDecimalPlaces'=> $rpt_currency->DecimalPlaces,
                'companyReportingExchangeRate'=> $rpt_currency->ExchangeRate, 'companyReportingAmount'=> $row->amountRpt,

                'companyID'=> $supplierInvoiceData->companySystemID, 'companyCode'=> $supplierInvoiceData->companyID,
                'createdPCID'=> gethostname(), 'createdUserID'=> $user_id,
                'createdDateTime'=> Carbon::now(), 'createdUserName'=> $user_name,
                'timestamp'=> Carbon::now()
            ];
        }

        HrMonthlyDeductionDetail::insert($data);

        return true;
    }

    public static function setupDocumentDate($docDate, $empID)
    {
        $document_date = Carbon::parse($docDate);

        $employee = SrpEmployeeDetails::with('currency')->find($empID);
        
        $is_processed = true;
        while( $is_processed ){
            $pv_date_arr = [
                'year'=> $document_date->format('Y'),
                'month'=> $document_date->format('m')
            ];

            //check payroll status of given month
            $is_processed = HrPayrollHeaderDetails::where('EmpID', $employee->EIdNo)
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

        return $document_date->format('Y-m-d');
    }
}
