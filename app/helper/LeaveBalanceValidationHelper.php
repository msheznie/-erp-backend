<?php

namespace App\helper;


use App\helper\SME;
use Illuminate\Support\Facades\DB;

class LeaveBalanceValidationHelper
{

    public static function validate($companyId,$asOfDate=null)
    {   
        
        $date =$asOfDate == '' ? date('Y-m-d'): date('Y-m-d',strtotime($asOfDate));        
        $leaveBalanceBasedOn = SME::leaveBalanceBasedOn($companyId);
        
        if (empty($leaveBalanceBasedOn)) {
            return ['status' => false, 'message' => 'Leave computation policy is not set with any value'];
        }

        if( $leaveBalanceBasedOn == 3 ){/* Payroll_year_based_validation; */
            
            $data = DB::table('srp_erp_hrperiodmaster')
            ->selectRaw('hrPeriodID as id, DATE( startDate ) as  startDate, DATE(endDate) as endDate  ')
            ->whereRaw("'{$date}' BETWEEN DATE(startDate) AND  DATE(endDate)")->where('isActive', 1)->where('companyID', $companyId)
            ->first();
            if(empty($data)){
                $msg = 'Can not find the payroll period for current date!';
                return ['status'=> false, 'message'=> $msg];
            }
            $data=array(
                'id' => $data->id,
                'startDate' => date('Y-m-d', strtotime($data->startDate)),
                'endDate' =>  date('Y-m-d', strtotime($data->endDate)),
                'accrualPolicyValue' =>  $leaveBalanceBasedOn
                
                );
            return ['status'=> true, 'details'=> $data];
            

        }elseif($leaveBalanceBasedOn == 2  ){/* Standard_year_based_validation */
            $data=array(
                'id' => null,
                'startDate' => date('Y-01-01', strtotime($date)),
                'endDate' => date('Y-12-t',strtotime($date)),
                'accrualPolicyValue' =>  $leaveBalanceBasedOn
                
                );
            return ['status'=> true, 'details'=> $data];

        }else{/* Financial_year_based_validation */
            
            $filterDate = ( ($date)? date_format(date_create($date),"Y-m-d"): date("Y-m-d"));
                    

            $data = DB::table('companyfinanceyear')
                ->selectRaw('companyFinanceYearID as id,DATE( bigginingDate ) as  startDate, DATE(endingDate) as endDate')
                ->whereRaw(" '$filterDate' BETWEEN DATE(bigginingDate) AND  DATE(endingDate) ")->where('isActive', -1)->where('isDeleted', 0)->where('companySystemID', $companyId)
                ->first();
            if(empty($data)){

                $msg='Company finance year not found for the current year!';
                return ['status'=> false, 'message'=> $msg];
                
            }        
            $data=array(
                'id' => $data->id,
                'startDate' => date('Y-m-d', strtotime($data->startDate)),
                'endDate' =>  date('Y-m-d', strtotime($data->endDate)),
                'accrualPolicyValue' =>  $leaveBalanceBasedOn
                
                );
            return ['status'=> true, 'details'=> $data];
        }
    }  
    
    public static function validate_month($companyId, $asOfDate=null)
    {        
        $date = $asOfDate == '' ? date('Y-m-d'): date('Y-m-d',strtotime($asOfDate));
            
        $leaveBalanceBasedOn = SME::leaveBalanceBasedOn($companyId);
        if (empty($leaveBalanceBasedOn)) {
            $data = [];
            return ['status' => false, 'details' => $data];
        }

        if( $leaveBalanceBasedOn == 3 ){
            $data = DB::table('srp_erp_hrperiod')
                ->select('id', 'dateFrom', 'dateTo')
                ->whereRaw(" '{$date}' BETWEEN dateFrom AND dateTo ")->where('companyID', $companyId)
                ->first();

            $data = [
                'id' => $data->id,
                'dateFrom' => date('Y-m-d', strtotime($data->dateFrom)),
                'dateTo' =>  date('Y-m-d', strtotime($data->dateTo))
            ];

            return ['status'=> true, 'details'=> $data];

        }

        /* financial_month_based_validation */
        $data = [
            'id' => null,
            'dateFrom' => date('Y-m-01', strtotime($date)),
            'dateTo' => date('Y-m-t',strtotime($date))
        ];
    
        return ['status'=> true, 'details'=> $data];
    }

    
}
