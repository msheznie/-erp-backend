<?php

namespace App\helper;

use App\Models\ProcumentOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\CompanyFinancePeriod;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Repositories\FixedAssetDepreciationMasterRepository;
use App\Models\FixedAssetMaster;
use App\Models\CompanyFinanceYear;
use Carbon\CarbonPeriod;
use App\Models\FixedAssetDepreciationPeriod;
use App\helper\Helper;
class CreateAccumulatedDepreciation
{
    private $fixedAssetDepreciationMasterRepository;

    public function __construct(FixedAssetDepreciationMasterRepository $fixedAssetDepreciationMasterRepo)
    {
        $this->fixedAssetDepreciationMasterRepository = $fixedAssetDepreciationMasterRepo;
    }

    public static function process($input,$id)
    {

        $accumulated_date = $input['accumulated_depreciation_date'];
        $accumulated_year = date('Y', strtotime($accumulated_date));
        $accumulated_month= date('m', strtotime($accumulated_date));
        $companyFinanceYearID = '';
        $companyFinancePeriodID = '';
        $companyFinanceYear = Helper::companyFinanceYear($input['companySystemID'],1);
        foreach($companyFinanceYear as $companyFinance)
        {

            $finance_year = date('Y', strtotime($companyFinance->bigginingDate));
            if($accumulated_year == $finance_year)
            {
                $companyFinanceYearID = $companyFinance->companyFinanceYearID;
                break;
            }
         
        }
        if($companyFinanceYearID == '')
        {
            return ;
        }

        if(isset($input['departmentSystemID']))
        {
                      
            $output = CompanyFinancePeriod::select(DB::raw("companyFinancePeriodID,isCurrent,CONCAT(DATE_FORMAT(dateFrom, '%d/%m/%Y'), ' | ', DATE_FORMAT(dateTo, '%d/%m/%Y')) as financePeriod"))
            ->where('companySystemID', '=', $input['companySystemID'])
            ->where('companyFinanceYearID', $companyFinanceYearID)
            ->where('departmentSystemID', $input['departmentSystemID'])
            ->where('isActive', -1)
            ->get();

            $accumulated_month = $accumulated_month - 1;
            $companyFinancePeriodID = $output[$accumulated_month]->companyFinancePeriodID;
       
        }

        if($companyFinancePeriodID == '')
        {
            return ;
        }

        $finance_data['companyFinanceYearID'] = $companyFinanceYearID;
        $finance_data['companySystemID'] = $input['companySystemID'];
        $companyFinanceYear = Helper::companyFinanceYearCheck($finance_data);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        } else {
            $dep_data['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
            $dep_data['FYEnd'] = $companyFinanceYear["message"]->endingDate;
        }


        $inputParam = $finance_data;
        $inputParam["departmentSystemID"] = 9;
        $inputParam["companyFinancePeriodID"] = $companyFinancePeriodID;
        $companyFinancePeriod = Helper::companyFinancePeriodCheck($inputParam);

        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $dep_data['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
            $dep_data['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
        }



        $subMonth = new Carbon($dep_data['FYPeriodDateFrom']);
        $subMonthStart = $subMonth->subMonth()->startOfMonth()->format('Y-m-d');
        $subMonthStartCarbon = new Carbon($subMonthStart);
        $subMonthEnd = $subMonthStartCarbon->endOfMonth()->format('Y-m-d');

        $lastMonthRun = FixedAssetDepreciationMaster::where('companySystemID', $input['companySystemID'])->where('companyFinanceYearID', $companyFinanceYearID)->where('FYPeriodDateFrom', $subMonthStart)->where('FYPeriodDateTo', $subMonthEnd)->first();

        if (!empty($lastMonthRun)) {
            if ($lastMonthRun->approved == 0) {
                return $this->sendError('Last month depreciation is not approved. Please approve it before you run for this month', 500);
            }
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $dep_data['companyID'] = $company->CompanyID;
        }
    
        $documentMaster = DocumentMaster::find($input['documentSystemID']);
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

        $lastSerial = FixedAssetDepreciationMaster::where('companySystemID', $input['companySystemID'])
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
        $dep_data['companySystemID'] = $input['companySystemID'];
        $dep_data['documentSystemID'] = $input['documentSystemID'];

        $dep_data['depCode'] = $documentCode;
        $dep_data['serialNo'] = $lastSerialNumber;
        $dep_data['depDate'] = $dep_data['FYPeriodDateTo'];
        $dep_data['depMonthYear'] = $depDate->month . '/' . $depDate->year;
        $dep_data['depLocalCur'] = $company->localCurrencyID;
        $dep_data['depRptCur'] = $company->reportingCurrency;
        $dep_data['createdPCID'] = gethostname();
        $dep_data['createdUserID'] = Helper::getEmployeeID();
        $dep_data['createdUserSystemID'] = Helper::getEmployeeSystemID();
        $dep_data['approved'] = -1;
        
        $depMaster = FixedAssetDepreciationMaster::create($dep_data);

        // $amount_local = 0;

        $faDepMaster = FixedAssetDepreciationMaster::where('depMasterAutoID','=',$depMaster->depMasterAutoID)->first();

        $faMaster = FixedAssetMaster::where('faID',$id)->with(['depperiod_by' => function ($query) {
            $query->selectRaw('SUM(depAmountRpt) as depAmountRpt,SUM(depAmountLocal) as depAmountLocal,faID');
            // $query->whereHas('master_by', function ($query) {
            //     $query->where('approved', -1);
            // });
            $query->groupBy('faID');
        }])
        ->ofCompany([$input['companySystemID']])
        // ->isApproved()
        // ->assetType(2)
        ->orderBy('faID', 'desc')
        ->first();

        $depAmountRpt = count($faMaster->depperiod_by) > 0 ? $faMaster->depperiod_by[0]->depAmountRpt : 0;
        $depAmountLocal = count($faMaster->depperiod_by) > 0 ? $faMaster->depperiod_by[0]->depAmountLocal : 0;
        $nbvLocal = $faMaster->COSTUNIT - $depAmountLocal;
        $nbvRpt = $faMaster->costUnitRpt - $depAmountRpt;
        $monthlyLocal = (($faMaster->COSTUNIT - $faMaster->salvage_value) * ($faMaster->DEPpercentage / 100)) / 12;
        $monthlyRpt = (($faMaster->costUnitRpt - $faMaster->salvage_value_rpt) * ($faMaster->DEPpercentage / 100)) / 12;

        if (round($nbvLocal,2) > $faMaster->salvage_value || round($nbvRpt,2) > $faMaster->salvage_value_rpt) {


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
            $data['createdBy'] = $depMaster->createdUserID;
            $data['createdUserSystemID'] = $depMaster->createdUserSystemID;
            $data['depMonthYear'] = $depMaster->depMonthYear;
            $data['depMonth'] = $faMaster->depMonth;
            $data['depAmountLocalCurr'] = $depMaster->depLocalCur;
            $data['depAmountRptCurr'] = $depMaster->depRptCur;

            if ($nbvLocal < $monthlyLocal) {
                $data['depAmountLocal'] = $nbvLocal;
                $amount_local = $nbvLocal;
            } else {
                $data['depAmountLocal'] = $monthlyLocal;
                $amount_local = $monthlyLocal;
            }

            if ($nbvRpt < $monthlyRpt) {
                $data['depAmountRpt'] = $nbvRpt;
            } else {
                $data['depAmountRpt'] = $monthlyRpt;
            }


            
            if (round($depAmountRpt,2) == 0 && round($depAmountLocal,2) == 0) {

                               
                $dateDEP = Carbon::parse($faMaster->dateDEP);
                $dateDEP1 = Carbon::parse($faMaster->dateDEP);

          
                if ($dateDEP->lessThanOrEqualTo($depDate)) {


                    $life_time_month = ($faMaster->depMonth*12) - 1;
              
                    $life_time_period = $dateDEP->addMonths($life_time_month);

               
                    if($life_time_period < $depDate) // if deprecetion running month greater than deprecetion start month then different month is life time
                    {
                        
                        $differentMonths = CarbonPeriod::create($dateDEP1->format('Y-m-d'), '1 month', $life_time_period->format('Y-m-d'));
                     
                    }
                    else
                    {
                        $differentMonths = CarbonPeriod::create($dateDEP1->format('Y-m-d'), '1 month', $depDate->format('Y-m-d'));
                     
                    }
                    
                  
                    if ($differentMonths) {
                        foreach ($differentMonths as $dt) {
                           
                            $companyFinanceYearID = CompanyFinanceYear::ofCompany($depMaster->companySystemID)->where('bigginingDate', '<=', $dt)->where('endingDate', '>=', $dt->format('Y-m-d'))->first();
                            if ($companyFinanceYearID) {
                               
                               
                                $data['FYID'] = $companyFinanceYearID->companyFinanceYearID;
                                $data['depForFYStartDate'] = $companyFinanceYearID->bigginingDate;
                                $data['depForFYEndDate'] = $companyFinanceYearID->endingDate;
                                $companyFinancePeriodID1 = CompanyFinancePeriod::ofCompany($depMaster->companySystemID)->ofDepartment(9)->where('dateFrom', '<=', $dt)->where('dateTo', '>=', $dt->format('Y-m-d'))->first();

                                
                                $data['FYperiodID'] = $companyFinancePeriodID1->companyFinancePeriodID;
                                $data['depForFYperiodStartDate'] = $companyFinancePeriodID1->dateFrom;
                                $data['depForFYperiodEndDate'] = $companyFinancePeriodID1->dateTo;
                                $data['timestamp'] = NOW();
                                array_push($finalData, $data);
                            }
                        }

                        
                    
                    }

                }
            } else {
                if (round($nbvRpt,2) != 0 && round($nbvLocal,2) != 0) {
                    $data['FYID'] = $depMaster->companyFinanceYearID;
                    $data['depForFYStartDate'] = $depMaster->FYBiggin;
                    $data['depForFYEndDate'] = $depMaster->FYEnd;
                    $data['FYperiodID'] = $depMaster->companyFinancePeriodID;
                    $data['depForFYperiodStartDate'] = $depMaster->FYPeriodDateFrom;
                    $data['depForFYperiodEndDate'] = $depMaster->FYPeriodDateTo;
                    $data['timestamp'] = NOW();
                    array_push($finalData, $data);
                }
            }

        }


        FixedAssetDepreciationPeriod::insert($data);

        $depDetail = FixedAssetDepreciationPeriod::selectRaw('SUM(depAmountLocal) as depAmountLocal, SUM(depAmountRpt) as depAmountRpt')->OfDepreciation($depMaster->depMasterAutoID)->first();
        // Log::info('Depreciation processing');
        if($depDetail) {
            //$fixedAssetDepreciationMasters = $faDepMaster->update(['depAmountLocal' => $depDetail->depAmountLocal, 'depAmountRpt' => $depDetail->depAmountRpt, 'isDepProcessingYN' => 1], $depMaster->depMasterAutoID);

            $fixedAssetDepreciationMasters = $faDepMaster->where('depMasterAutoID', $depMaster->depMasterAutoID)->update(array('depAmountLocal' => $depDetail->depAmountLocal,'depAmountRpt' => $depDetail->depAmountRpt,'isDepProcessingYN' => 1));
        }


        return true;

    }



}
