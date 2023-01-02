<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\Appointment;
use Carbon\Carbon;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyFinancePeriod;
use App\Models\SegmentMaster;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\SupplierMaster;
use App\Models\GRVMaster;
use App\Models\SupplierCurrency;
use App\Models\CurrencyMaster;
use App\Models\SupplierAssigned;
use App\Repositories\GRVMasterRepository;

class DeliveryAppoinmentGRV implements ShouldQueue
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
    public function handle(GRVMasterRepository $grvMasterRepo)
    {
        $mytime =  new Carbon();

        $appoinment = Appointment::find($this->data['documentSystemCode']);

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

               $serviceLine = SegmentMaster::where('serviceLineSystemID',$this->data['segment'])->first();


                $detail['companySystemID'] = $this->data['companySystemID'];
                $detail['stampDate'] = $mytime;
                $detail['grvDate'] = $mytime;
                $detail['companyFinanceYearID'] = $fromCompanyFinancePeriod->companyFinanceYearID;
                $detail['companyFinancePeriodID'] = $fromCompanyFinancePeriod->companyFinancePeriodID;
                $detail['grvTypeID'] = 2;
                $detail['serviceLineSystemID'] = $this->data['segment'];
                $detail['grvDoRefNo'] = $appoinment->primary_code;
                $detail['grvNarration'] = 'Created from SRM Delivery Appointment '.$appoinment->primary_code;
                $detail['grvLocation'] = $this->data['location'];
                $detail['supplierID'] = $appoinment->supplier_id;
                $detail['supplierTransactionCurrencyID'] = $supplierCurrencies->currencyID;
                $detail['FYBiggin'] = $fromCompanyFinancePeriod->dateFrom;
                $detail['FYEnd'] = $fromCompanyFinancePeriod->dateTo;              
                $detail['createdPcID'] = gethostname();
                $detail['createdUserID'] =  \Helper::getEmployeeID();
                $detail['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                $detail['documentSystemID'] =  3;
                $detail['documentID'] = "GRV";
                $detail["grvType"] = 'POG';
                $detail["serviceLineCode"] = $serviceLine->ServiceLineCode;

                $company = Company::find($this->data['companySystemID']);
                if ($company) {
                $detail['companyID'] = $company->CompanyID;
                $detail['localCurrencyID'] = $company->localCurrencyID;
                $detail['companyReportingCurrencyID'] = $company->reportingCurrency;
                }
                
                $detail['vatRegisteredYN'] = 1;
                $companyCurrencyConversion = \Helper::currencyConversion($this->data['companySystemID'], $supplierCurrencies->currencyID, $supplierCurrencies->currencyID, 0);


                $detail['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                $detail['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $detail['supplierTransactionER'] = 1;

                $supplier = SupplierMaster::where('supplierCodeSystem', $appoinment->supplier_id)->first();
                if ($supplier) {
                    $detail['supplierPrimaryCode'] = $supplier->primarySupplierCode;
                    $detail['supplierName'] = $supplier->supplierName;
                    $detail['supplierAddress'] = $supplier->address;
                    $detail['supplierTelephone'] = $supplier->telephone;
                    $detail['supplierFax'] = $supplier->fax;
                    $detail['supplierEmail'] = $supplier->supEmail;
                }

                $lastSerial = GRVMaster::where('companySystemID', $this->data['companySystemID'])
                ->where('companyFinanceYearID', $fromCompanyFinancePeriod->companyFinanceYearID)
                ->orderBy('grvSerialNo', 'desc')
                ->first();
    
                $lastSerialNumber = 1;
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->grvSerialNo) + 1;
                }
                $detail['grvSerialNo'] = $lastSerialNumber;

                if ($fromCompanyFinancePeriod) {
                    $grvStartYear = $fromCompanyFinanceYear->bigginingDate;
                    $grvFinYearExp = explode('-', $grvStartYear);
                    $grvFinYear = $grvFinYearExp[0];
                } else {
                    $grvFinYear = date("Y");
                }
                $document_id = "GRV";
                $grvCode = ($company->CompanyID . '\\' . $grvFinYear . '\\' . $document_id . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                $detail['grvPrimaryCode'] = $grvCode;

                $supplierCurrency = SupplierCurrency::where('supplierCodeSystem', $appoinment->supplier_id)
                ->where('isDefault', -1)
                ->first();
    
                if ($supplierCurrency) {
        
                    $erCurrency = CurrencyMaster::where('currencyID', $supplierCurrency->currencyID)->first();
        
                    $detail['supplierDefaultCurrencyID'] = $supplierCurrency->currencyID;
        
                    if ($erCurrency) {
                        $detail['supplierDefaultER'] = $erCurrency->ExchangeRate;
                    }
                }

                $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $appoinment->supplier_id)
                ->where('companySystemID', $this->data['companySystemID'])
                ->first();
    
                if ($supplierAssignedDetail) {
                    $detail['liabilityAccountSysemID'] = $supplierAssignedDetail->liabilityAccountSysemID;
                    $detail['liabilityAccount'] = $supplierAssignedDetail->liabilityAccount;
                    $detail['UnbilledGRVAccountSystemID'] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
                    $detail['UnbilledGRVAccount'] = $supplierAssignedDetail->UnbilledGRVAccount;
                }
                $detail['deliveryAppoinmentID'] = $this->data['documentSystemCode'];
                
                $grvMaster = $grvMasterRepo->create($detail);

                Log::info('delivert appoinment grv completed... : ');
            }
            else
            {
                Log::info('From Company Finance period not found, date : ');
            }

        }
        else{
            Log::info('From Company Finance Year not found, date : ');
        }

    }
}
