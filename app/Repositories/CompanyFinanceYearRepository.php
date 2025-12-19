<?php

namespace App\Repositories;

use App\Models\CompanyFinanceYear;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyPolicyMaster;
use InfyOm\Generator\Common\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



/**
 * Class CompanyFinanceYearRepository
 * @package App\Repositories
 * @version June 12, 2018, 6:44 am UTC
 *
 * @method CompanyFinanceYear findWithoutFail($id, $columns = ['*'])
 * @method CompanyFinanceYear find($id, $columns = ['*'])
 * @method CompanyFinanceYear first($columns = ['*'])
*/
class CompanyFinanceYearRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'bigginingDate',
        'endingDate',
        'isActive',
        'isCurrent',
        'isClosed',
        'closedByEmpSystemID',
        'closedByEmpID',
        'closedByEmpName',
        'closedDate',
        'comments',
        'createdUserGroup',
        'createdUserID',
        'createdPcID',
        'createdDateTime',
        'modifiedUser',
        'modifiedPc',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyFinanceYear::class;
    }

    public static function croneJobFinancialPeriodActivation(){
        DB::beginTransaction();
        try {
            $currentDate = Carbon::now()->format('Y-m-d');
        
            $financialYears = CompanyFinanceYear::whereDate('bigginingDate','<=', $currentDate)
                                                        ->whereDate('endingDate','>=', $currentDate)
                                                        ->get();

            foreach($financialYears as $financialYear){
                
                $policyCheck = CompanyPolicyMaster::where('companyPolicyCategoryID',63)
                                                    ->where('companySystemID', $financialYear->companySystemID)
                                                    ->first();
                if($policyCheck && $policyCheck->isYesNO==1){

                    if($financialYear->isActive == -1 && $financialYear->isCurrent == -1){
                        $companyFinanceYearID = $financialYear->companyFinanceYearID;
    
                        $companyFinancialPeriods = CompanyFinancePeriod::where('companyFinanceYearID', $companyFinanceYearID)
                                                                                ->whereDate('dateFrom', $currentDate)
                                                                                ->get();
    
                            foreach($companyFinancialPeriods as $companyFinancialPeriod){
                                $departmentSystemID = $companyFinancialPeriod->departmentSystemID;
    
                                if($companyFinancialPeriod->isActive == -1 && $companyFinancialPeriod->isCurrent == -1){
    
                                } else {
                                    $masterDataReverse = [ 'isCurrent' => 0];
                                    $companyFinancialPeriodsReverse = CompanyFinancePeriod::where('companyFinanceYearID', $companyFinanceYearID)
                                                                                            ->where('departmentSystemID', $departmentSystemID)
                                                                                            ->update($masterDataReverse);
                                    
                                    $masterDataUpdate = ['isActive' => -1, 'isCurrent' => -1];
                                    $companyFinancialPeriodUpdate = $companyFinancialPeriod->update($masterDataUpdate);
                                }
                            }
    
    
                    } else {
                        
                        $companySystemID = $financialYear->companySystemID;
                        $companyFinanceYearID = $financialYear->companyFinanceYearID;
    
                        $masterDataReverse = ['isCurrent' => 0];
                        $companyFinancialYearsReverse = CompanyFinanceYear::where('companySystemID' , $companySystemID)->update($masterDataReverse);
    
                        $masterDataUpdate = ['isActive' => -1, 'isCurrent' => -1];
                        $companyFinancialYearsUpdate = $financialYear->update($masterDataUpdate);
    
                        $companyFinancialPeriods = CompanyFinancePeriod::where('companyFinanceYearID', $companyFinanceYearID)
                                                                                ->whereDate('dateFrom', $currentDate)
                                                                                ->get();
    
                            foreach($companyFinancialPeriods as $companyFinancialPeriod){
    
                                if($companyFinancialPeriod->isActive == -1 && $companyFinancialPeriod->isCurrent == -1){
                                    // return 'Financial Periods Already Updated';
                                } else {
                                    $masterDataReverse = ['isCurrent' => 0];
                                    $companyFinancialPeriodsReverse = CompanyFinancePeriod::where('companyFinanceYearID', $companyFinanceYearID)
                                                                                ->update($masterDataReverse);
                                    
                                    $masterDataUpdate = ['isActive' => -1, 'isCurrent' => -1];
                                    $companyFinancialPeriodUpdate = $companyFinancialPeriod->update($masterDataUpdate);
                                }
                            }
                    }
                }
            }
            
            DB::commit();
            return ['status' => true];
        }
        catch(Exception $ex){
            DB::rollback();
            $ex_arr = Helper::exception_to_error($ex);
            return ['status' => false];
        }
    }


}
