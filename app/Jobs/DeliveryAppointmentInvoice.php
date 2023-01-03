<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyFinancePeriod;
use App\Models\Appointment;
use App\Models\BookInvSuppMaster;
use App\Repositories\BookInvSuppMasterRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\SupplierAssigned;

class DeliveryAppointmentInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(BookInvSuppMasterRepository $bookInvSuppMasterRepository)
    {
        $mytime =  '2022-12-12 00:00:00';//new Carbon();

    

        $appoinment = Appointment::find($this->data['id']);

        $fromCompanyFinanceYear = CompanyFinanceYear::where('companySystemID', $this->data['companySystemID'])
        ->whereDate('bigginingDate', '<=', $mytime)
        ->whereDate('endingDate', '>=', $mytime)
        ->first();


        if (!empty($fromCompanyFinanceYear)) {

            
            $fromCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $this->data['companySystemID'])
            ->where('departmentSystemID', 10)
            ->where('companyFinanceYearID', $fromCompanyFinanceYear->companyFinanceYearID)
            ->whereDate('dateFrom', '<=', $mytime)
            ->whereDate('dateTo', '>=', $mytime)
            ->first();


            if(!empty($fromCompanyFinancePeriod)){

     
                $supplierCurrencies = DB::table('suppliercurrency')
                ->leftJoin('currencymaster', 'suppliercurrency.currencyID', '=', 'currencymaster.currencyID')
                ->where('supplierCodeSystem', '=', $appoinment->supplier_id)->first();

                $detail['companySystemID'] = $this->data['companySystemID'];
                $detail['bookingDate'] = $mytime;
                $detail['supplierInvoiceDate'] = $mytime;
                $detail['companyFinanceYearID'] = $fromCompanyFinancePeriod->companyFinanceYearID;
                $detail['companyFinancePeriodID'] = $fromCompanyFinancePeriod->companyFinancePeriodID;
                $detail['custInvoiceDirectAutoID'] = null;
                $detail['documentType'] = 0;
                $detail['projectID'] = null;
                $detail['secondaryRefNo'] = '';
                $detail['supplierInvoiceNo'] = $appoinment->primary_code;
                $detail['comments'] = 'Created from SRM Delivery Appointment '.$appoinment->primary_code;
                $detail['supplierID'] = $appoinment->supplier_id;
                $detail['supplierTransactionCurrencyID'] = $supplierCurrencies->currencyID;
                $detail['preCheck'] = true; 
                $detail['FYPeriodDateFrom'] = $fromCompanyFinancePeriod->dateFrom; 
                $detail['FYPeriodDateTo'] = $fromCompanyFinancePeriod->dateTo; 
                $detail['retentionPercentage'] = 0;
                $detail['createdPcID'] = 11;//gethostname();
                $detail['createdUserID'] =  11;//\Helper::getEmployeeID();
                $detail['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                $detail['documentSystemID'] =  11;
                $detail['documentID'] = "SI";

                $companyCurrencyConversion = \Helper::currencyConversion($this->data['companySystemID'], $supplierCurrencies->currencyID, $supplierCurrencies->currencyID, 0);

                $company = Company::find($this->data['companySystemID']);
                if ($company) {
                $detail['companyID'] = $company->CompanyID;
                $detail['vatRegisteredYN'] = $company->vatRegisteredYN;
                $detail['localCurrencyID'] = $company->localCurrencyID;
                $input['companyReportingCurrencyID'] = $company->reportingCurrency;
                $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                }

                $lastSerial = BookInvSuppMaster::where('companySystemID', $this->data['companySystemID'])
                ->where('companyFinanceYearID', $fromCompanyFinancePeriod->companyFinanceYearID)
                ->orderBy('serialNo', 'desc')
                ->first();
    
                $lastSerialNumber = 1;
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                }
                
                $detail['serialNo'] = $lastSerialNumber;
                $detail['supplierTransactionCurrencyER'] = 1;
                $detail['FYBiggin'] = $fromCompanyFinanceYear->bigginingDate;
                $detail['FYEnd'] = $fromCompanyFinanceYear->endingDate;

                $startYear = $fromCompanyFinanceYear['bigginingDate'];
                $finYearExp = explode('-', $startYear);
                $finYear = $finYearExp[0];

                $bookingInvCode = ($company->CompanyID . '\\' . $finYear . '\\' . 'BSI' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                $detail['bookingInvCode'] = $bookingInvCode;
                $detail['isLocalSupplier'] = \Helper::isLocalSupplier($appoinment->supplier_id, $this->data['companySystemID']);


                $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID',
                'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount','VATPercentage')
                ->where('supplierCodeSytem', $appoinment->supplier_id)
                ->where('companySystemID', $this->data['companySystemID'])
                ->first();


                if ($supplierAssignedDetail) {
                    $detail['supplierVATEligible'] = $supplierAssignedDetail->vatEligible;
                    $detail['supplierGLCodeSystemID'] = $supplierAssignedDetail->liabilityAccountSysemID;
                    $detail['supplierGLCode'] = $supplierAssignedDetail->liabilityAccount;
                    $detail['UnbilledGRVAccountSystemID'] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
                    $detail['UnbilledGRVAccount'] = $supplierAssignedDetail->UnbilledGRVAccount;
                    $detail['VATPercentage'] = $supplierAssignedDetail->VATPercentage;
                }

                
                $invoice = $bookInvSuppMasterRepository->create($detail);

                Log::info('successfully created');
            }
            else
            {
                Log::info('From Company Finance period not found, date : ');
            }

        }
        else{
            Log::info('From Company Finance Year not found, date : ');
        }

        Log::info($fromCompanyFinanceYear);
    }
}
