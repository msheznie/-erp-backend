<?php

namespace App\Jobs;

use App\Models\DocumentApproved;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\CompanyFinancePeriod;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Repositories\FixedAssetDepreciationMasterRepository;
use App\Models\FixedAssetMaster;
use App\Models\CompanyFinanceYear;
use Carbon\CarbonPeriod;
use App\Models\FixedAssetDepreciationPeriod;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChartOfAccount;
use App\Models\GeneralLedger;
class CreateAccumulatedDepreciation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $assetAutoID;
    protected $database;
    protected $isDocumentUpload;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($assetAutoID, $database, $isDocumentUpload = null)
    {
        $this->assetAutoID = $assetAutoID;
        $this->database = $database;
        $this->isDocumentUpload = $isDocumentUpload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
     
        Log::useFiles(storage_path() . '/logs/accumulated_dep_job.log');

        try {

            $faMaster = FixedAssetMaster::where('faID',$this->assetAutoID)->first();

            $accumulated_date = $faMaster->accumulated_depreciation_date;
            $accumulated_amount = $faMaster->accumulated_depreciation_amount_rpt;

            $unit_salvage_val = doubleval($faMaster->costUnitRpt) -doubleval($faMaster->salvage_value_rpt);


            if($accumulated_amount > 0 && $accumulated_amount != null && $faMaster->assetType == 1) {
                if(doubleval($accumulated_amount) <=  $unit_salvage_val) {
                    $accumulated_year = date('Y', strtotime($accumulated_date));
                    $accumulated_month= date('m', strtotime($accumulated_date));
                    $companyFinanceYearID = '';
                    $companyFinancePeriodID = '';
                    $companyFinanceYear = \Helper::companyFinanceYear($faMaster->companySystemID,1);
                    $doc_id = 23;    
                    $documentMaster = DocumentMaster::find($doc_id);

                    if($documentMaster) {
                        
                        $output = CompanyFinancePeriod::where('dateFrom', '<=', $accumulated_date)
                                                    ->where('dateTo', '>=', $accumulated_date)
                                                    ->where('departmentSystemID', '=', $documentMaster->departmentSystemID)
                                                    ->where('companySystemID', '=', $faMaster->companySystemID)
                                                    ->first();     



                        if(isset($output)) {
                            $companyFinanceYearID = $output->companyFinanceYearID;
                            $companyFinancePeriodID = $output->companyFinancePeriodID;

                            $finance_data['companyFinanceYearID'] = $companyFinanceYearID;
                            $finance_data['companySystemID'] = $faMaster->companySystemID;
                            
                            $companyFinanceYear = \Helper::companyFinanceYearCheck($finance_data);
                            if (!$companyFinanceYear["success"]) {
                                Log::error($companyFinanceYear["message"]);
                            } else {
                                $dep_data['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
                                $dep_data['FYEnd'] = $companyFinanceYear["message"]->endingDate;
                            }

                            $inputParam = $finance_data;
                            $inputParam["departmentSystemID"] = 9;
                            $inputParam["companyFinancePeriodID"] = $companyFinancePeriodID;
                            
                            $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);

                            if (!$companyFinancePeriod["success"]) {
                                Log::error('company finance period not found');
                            } else {
                                $dep_data['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
                                $dep_data['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
                            }

                        
                            $subMonth = new Carbon($dep_data['FYPeriodDateFrom']);
                            $subMonthStart = $subMonth->subMonth()->startOfMonth()->format('Y-m-d');
                            $subMonthStartCarbon = new Carbon($subMonthStart);
                            $subMonthEnd = $subMonthStartCarbon->endOfMonth()->format('Y-m-d');

                            // $lastMonthRun = FixedAssetDepreciationMaster::where('companySystemID', $faMaster->companySystemID)->where('companyFinanceYearID', $companyFinanceYearID)->where('FYPeriodDateFrom', $subMonthStart)->where('FYPeriodDateTo', $subMonthEnd)->first();

                            // if (!empty($lastMonthRun)) {
                            //     if ($lastMonthRun->approved == 0) {
                            //     }
                            // }

                            $company = Company::find($faMaster->companySystemID);
                            if ($company) {
                                $dep_data['companyID'] = $company->CompanyID;
                            }
                            
                            $doc_id = 23;
                            $documentMaster = DocumentMaster::find($doc_id);
                            if ($documentMaster) {
                                $dep_data['documentID'] = $documentMaster->documentID;
                            }

                            if ($companyFinanceYear["message"]) {
                                $startYear = $companyFinanceYear["message"]['bigginingDate'];
                                $finYearExp = explode('-', $startYear);
                                $finYear = $finYearExp[0];
                            } else {
                                $finYear = date("Y");
                            }

                            $lastSerial = FixedAssetDepreciationMaster::where('companySystemID', $faMaster->companySystemID)
                                                                ->where('companyFinanceYearID', $companyFinanceYearID)
                                                                ->orderBy('depMasterAutoID', 'desc')
                                                                ->first();

                            $lastSerialNumber = 1;
                            if ($lastSerial) {
                                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                            }

                            $documentCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

                            $depDate = Carbon::parse($dep_data['FYPeriodDateTo']);

                            $dep_data['companyFinanceYearID'] = $companyFinanceYearID;
                            $dep_data['companyFinancePeriodID'] = $companyFinancePeriodID;
                            $dep_data['companySystemID'] = $faMaster->companySystemID;
                            $dep_data['documentSystemID'] =23;

                            $dep_data['depCode'] = $documentCode;
                            $dep_data['serialNo'] = $lastSerialNumber;
                            $dep_data['depDate'] = $dep_data['FYPeriodDateTo'];
                            $dep_data['depMonthYear'] = $depDate->month . '/' . $depDate->year;
                            $dep_data['depLocalCur'] = $company->localCurrencyID;
                            $dep_data['depRptCur'] = $company->reportingCurrency;
                            $dep_data['createdPCID'] = gethostname();
                            $dep_data['createdUserID'] =  \Helper::getEmployeeID();
                            $dep_data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                            //$dep_data['approved'] = -1;
                            $dep_data['is_acc_dep'] = true;


                            $depMaster = FixedAssetDepreciationMaster::create($dep_data);


                            // $amount_local = 0;

                            $faDepMaster = FixedAssetDepreciationMaster::where('depMasterAutoID','=',$depMaster->depMasterAutoID)->first();

                            $faMaster = FixedAssetMaster::where('faID',$this->assetAutoID)->with(['depperiod_by' => function ($query) {
                                                            $query->selectRaw('SUM(depAmountRpt) as depAmountRpt,SUM(depAmountLocal) as depAmountLocal,faID');
                                                            $query->whereHas('master_by', function ($query) {
                                                            //$query->where('approved', -1);
                                                            });
                                                            $query->groupBy('faID');
                                                        }])
                                                        ->ofCompany([$faMaster->companySystemID])
                                                        //->isApproved()
                                                        ->assetType(1)
                                                        ->orderBy('faID', 'desc')
                                                        ->first();

                            if(isset($faMaster)) {
                                $depAmountRpt = $faMaster->accumulated_depreciation_amount_rpt;
                                $depAmountLocal = $faMaster->accumulated_depreciation_amount_lcl;
                                $nbvLocal = $faMaster->COSTUNIT - $depAmountLocal;
                                $nbvRpt = $faMaster->costUnitRpt - $depAmountRpt;
                                $monthlyLocal = (($faMaster->COSTUNIT - $faMaster->salvage_value) * ($faMaster->DEPpercentage / 100)) / 12;
                                $monthlyRpt = (($faMaster->costUnitRpt - $faMaster->salvage_value_rpt) * ($faMaster->DEPpercentage / 100)) / 12;

                                if (round($nbvLocal,2) >= $faMaster->salvage_value || round($nbvRpt,2) >= $faMaster->salvage_value_rpt) {
                                    $data['depMasterAutoID'] = $depMaster->depMasterAutoID;
                                    $data['companySystemID'] = $depMaster->companySystemID;
                                    $data['companyID'] = $depMaster->companyID;
                                    $data['serviceLineSystemID'] = $faMaster->serviceLineSystemID;
                                    $data['serviceLineCode'] = $faMaster->serviceLineCode;
                                    $data['faFinanceCatID'] = $faMaster->AUDITCATOGARY;
                                    $data['faMainCategory'] = $faMaster->faCatID;
                                    $data['faSubCategory'] = $faMaster->faSubCatID;
                                    $data['faID'] = $faMaster->faID;
                                    $data['faCode'] = $faMaster->faCode;
                                    $data['assetDescription'] = $faMaster->assetDescription;
                                    $data['depPercent'] = $faMaster->DEPpercentage;
                                    $data['COSTUNIT'] = $faMaster->COSTUNIT;
                                    $data['costUnitRpt'] = $faMaster->costUnitRpt;
                                    $data['depDoneYN'] = -1;
                                    $data['createdPCid'] = gethostname();
                                    $data['createdBy'] = \Helper::getEmployeeID();
                                    $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                                    $data['depMonthYear'] = $depMaster->depMonthYear;
                                    $data['depMonth'] = $faMaster->depMonth;
                                    $data['depAmountLocalCurr'] = $depMaster->depLocalCur;
                                    $data['depAmountRptCurr'] = $depMaster->depRptCur;
                                    $data['depAmountLocal'] = $depAmountLocal;
                                    $data['depAmountRpt'] = $depAmountRpt;


                                    if (round($nbvRpt,2) != 0 && round($nbvLocal,2) != 0) {
                                        $data['FYID'] = $depMaster->companyFinanceYearID;
                                        $data['depForFYStartDate'] = $depMaster->FYBiggin;
                                        $data['depForFYEndDate'] = $depMaster->FYEnd;
                                        $data['FYperiodID'] = $depMaster->companyFinancePeriodID;
                                        $data['depForFYperiodStartDate'] = $depMaster->FYPeriodDateFrom;
                                        $data['depForFYperiodEndDate'] = $depMaster->FYPeriodDateTo;
                                        $data['timestamp'] = NOW();
                                    }

                                    $dep_per = FixedAssetDepreciationPeriod::create($data);

                                    $depDetail = FixedAssetDepreciationPeriod::selectRaw('SUM(depAmountLocal) as depAmountLocal, SUM(depAmountRpt) as depAmountRpt')->OfDepreciation($depMaster->depMasterAutoID)->first();
                                    if($depDetail) {
                                        //$fixedAssetDepreciationMasters = $faDepMaster->update(['depAmountLocal' => $depDetail->depAmountLocal, 'depAmountRpt' => $depDetail->depAmountRpt, 'isDepProcessingYN' => 1], $depMaster->depMasterAutoID);
                                        $fixedAssetDepreciationMasters = $faDepMaster->where('depMasterAutoID', $depMaster->depMasterAutoID)->update(array('depAmountLocal' => $depDetail->depAmountLocal,'depAmountRpt' => $depDetail->depAmountRpt,'isDepProcessingYN' => 1));


                                        //cost
                                    }

                                }

                            } else {
                                Log::error('asset not found');
                            }

                            //Asset Dep auto approval for uploaded documents
                            if($faMaster->assetCostingUploadID != null){
                                $params = array('autoID' => $faDepMaster->depMasterAutoID,
                                    'company' => $faDepMaster->companySystemID,
                                    'document' => 23,
                                    'segment' => '',
                                    'category' => '',
                                    'amount' => '',
                                    'isAutoCreateDocument' => true
                                );

                                Log::info("on confirm depreciation");



                                $confirm = \Helper::confirmDocument($params);
                                if (!$confirm["success"]) {
                                    Log::error($confirm['message']);
                                }
                                $documentApproveds = DocumentApproved::where('documentSystemCode', $faDepMaster->depMasterAutoID)->where('documentSystemID', 23)->get();
                                foreach ($documentApproveds as $documentApproved) {
                                    $documentApproved["approvedComments"] = "Approved by System User";
                                    $documentApproved["db"] = $this->database;
                                    $documentApproved["isAutoCreateDocument"] = true;
                                    if($this->isDocumentUpload == true){
                                        $documentApproved["isDocumentUpload"] = true;
                                    }
                                    $approve = \Helper::approveDocument($documentApproved);
                                    if (!$approve["success"]) {
                                        Log::error($approve['message']);
                                    }
                                }
                            }



                        } else {
                            Log::error('companyFinanceYear not found');
                        }

                    } else {
                        Log::error('Document system not found');
                    }
                } else {
                    Log::error('Accumulated Amount is  cannot exceed the net of Total Asset Cost - Salvage/Residual Value');
                }
            } else {
                Log::error('Accumulated Amount is less than zero');
            }


        } catch (\Exception $e) {
            Log::error($this->failed($e));
        }  
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
