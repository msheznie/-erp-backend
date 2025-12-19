<?php
/**
 * =============================================
 * -- File Name : EmployeeLeaveApplicationAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Employee Leave
 * -- Author : Mohamed Rilwan
 * -- Create date : 27 - August 2019
 * -- Description : This file contains the all functions for Employee Leave Application
 * -- REVISION HISTORY
 * -- Date: 27 - August 2019By: Rilwan Description: Added new functions named as getPeriodsForPayslip(),getEmployeePayslip(),getPayslipDetails(),getCompanyData(),getAdditionDetailsForPayslip(),getDeductionDetailsForPayslip(),getBankDetailsForPayslip()
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\QryPayslipadditons;
use App\Models\QryPayslipBankTransfer;
use App\Models\QryPayslipDeductions;
use App\Models\QryPayslipheader;
use App\Models\QrySalaryProcessedPeriods;
use App\Models\SalaryProcessDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeePayslipAPIController extends AppBaseController
{
    public function __construct()
    {

    }

    public function getPeriodsForPayslip()
    {
        $processPeriods =  QrySalaryProcessedPeriods::select('periodMonth','periodMasterID','periodYear')->get();
        $processPeriods = $processPeriods->map(function ($value, $key) {
            $value['periodMonth'] = (string)$value['periodMonth'];
            $value['periodMasterID'] = (int)$value['periodMasterID'];
            return $value;
        });

        $output = array('processPeriods' => $processPeriods->toArray());
        return $this->sendResponse($output, trans('custom.salary_process_periods_retrieved_successfully'));
    }

    public function getEmployeePayslip(Request $request)
    {
        $periodMasterID = $request['periodMasterID'];

        if($periodMasterID==null){
            return $this->sendError(trans('custom.salary_payslip_details_not_found'),200);
        }

        $employee = Helper::getEmployeeInfo();
        $empID = $employee->empID;

        $payslip_details = $this->getPayslipDetails($periodMasterID,$empID);
        $company_id = isset($payslip_details->companyID)?$payslip_details->companyID:null;
        $salaryProcessMasterID =  isset($payslip_details->salaryProcessMasterID)?$payslip_details->salaryProcessMasterID:null;
        if($salaryProcessMasterID==null){
            return $this->sendError(trans('custom.salary_payslip_details_not_found'),200);
        }
        $company_details = $this->getCompanyData($company_id);

        $company = [];
        if(collect($company_details)->count()){
            $company = array(
                'CompanyName' => isset($company_details->CompanyName)?$company_details->CompanyName:null,
                'CompanyAddress' => isset($company_details->CompanyAddress)?$company_details->CompanyAddress:null,
                'companyLogo' => isset($company_details->logo_url)? $company_details->logo_url : null,
            );
        }

        $currency = [];
        if(isset($company_details->localcurrency) && collect($company_details->localcurrency)->count()){
            $currency = collect($company_details->localcurrency)
                ->only(['currencyID', 'CurrencyName','CurrencyCode','DecimalPlaces'])
                ->toArray();
        }

        $employee = array(
            'id'=>$empID,
            'name'=>$employee->empFullName,
            'designation'=>isset($employee->details->designation->designation)?$employee->details->designation->designation:null,
        );

        $addition_details = $this->getAdditionDetailsForPayslip($empID,$salaryProcessMasterID);

        $deduction_details = $this->getDeductionDetailsForPayslip($empID,$salaryProcessMasterID);

        $bank_transfer_details = $this->getBankDetailsForPayslip($empID,$salaryProcessMasterID);

        $output = array(
            'id'=>$periodMasterID,
            'company'=>$company,
            'currency'=>$currency,
            'employee'=>$employee,
            'payslip_month'=> isset($payslip_details->startDate)?Carbon::parse($payslip_details->startDate)->format('F'):null,
            'payslip_year'=> isset($payslip_details->startDate)?Carbon::parse($payslip_details->startDate)->format('Y'):null,
            'payslip_start_date'=> isset($payslip_details->startDate)?$payslip_details->startDate:null,
            'payslip_end_date'=> isset($payslip_details->EndDate)?$payslip_details->EndDate:null,
            'addition'=>$addition_details['tableData'],
            'deduction'=>$deduction_details['tableData'],
            'addition_total'=>$addition_details['total'],
            'deduction_total'=>$deduction_details['total'],
            'net'=>($addition_details['total']-$deduction_details['total']),
            'bank_transfer'=>isset($bank_transfer_details['tableData'])?$bank_transfer_details['tableData']:[],
            'bank_transfer_total'=>isset($bank_transfer_details['total'])?$bank_transfer_details['total']:[],
            'payslip_details'=>$payslip_details

        );

        return $this->sendResponse($output, trans('custom.salary_payslip_details_retrieved_successfully'));
    }

    private function getPayslipDetails($periodMasterID,$empID)
    {
        $salary_process_detail = SalaryProcessDetail::where('processPeriod',$periodMasterID)->where('empID',$empID)->first();
        $salaryProcessMasterID = isset($salary_process_detail->salaryProcessMasterID)?$salary_process_detail->salaryProcessMasterID:null;
        if($salaryProcessMasterID!=null){
            $payslip = QryPayslipheader::select('payslipMasterID','salaryProcessMasterID','empID','Name',
                'CurrencyCode','DepartmentDescription','companyID','startDate','endDate')
                ->where('salaryProcessMasterID',$salaryProcessMasterID)
                ->where('empID',$empID)
                ->first();
            if(collect($payslip)->count()){
                return $payslip;
            }
        }
        return [];
    }

    private function getCompanyData($company_id)
    {
        if($company_id != null){
            $company = Company::with(['localcurrency'])
                ->where('companyID',$company_id)
                ->first();
            if(collect($company)->count()){
                return $company;
            }
        }
        return [];

    }

    private function getAdditionDetailsForPayslip($empID,$salaryProcessMasterID)
    {
        if($empID!=null && $salaryProcessMasterID!=null){

            $addition_array = array();
            $addition_total = 0;
            $additions_details = QryPayslipadditons::select('Naration','amount','paysheetgroup','Rate','dayPerHour')
                ->where('empID',$empID)
                ->where('salaryProcessMasterID',$salaryProcessMasterID)
                ->where('amount','<>',0)
                ->get();

            if($additions_details->count()){
                $addition= $additions_details->groupBy('paysheetgroup')->toArray();
                $addition_total = $additions_details->sum('amount');
                $temp_array = array();
                foreach ($addition as $key => $val){

                    foreach ($val as $data){
                        $temp_array[]=[
                            'Naration'=>$data['Naration'],
                            'amount'=>$data['amount'],
                            'Rate'=>$data['Rate'],
                            'dayPerHour'=>$data['dayPerHour']
                        ];

                    }
                    $addition_array[] = array(
                        'paysheetGroup'=>$key,
                        'items'=>$temp_array
                    );
                }
                return array(
                    'tableData'=>$addition_array,
                    'total'=>$addition_total
                );
            }
            return false;
        }
    }

    private function getDeductionDetailsForPayslip($empID,$salaryProcessMasterID)
    {
        if($empID!=null && $salaryProcessMasterID!=null){

            $deduction_array = array();
            $deduction_total = 0;
            $deductions_details = QryPayslipDeductions::select('Naration','amounts','paysheetgroup','Rate','dayPerHour')
                ->where('empID',$empID)
                ->where('salaryProcessMasterID',$salaryProcessMasterID)
                ->where('amounts','<>',0)
                ->get();

            if($deductions_details->count()){
                $deduction= $deductions_details->groupBy('paysheetgroup')->toArray();
                $deduction_total = $deductions_details->sum('amounts');

                foreach ($deduction as $key => $val){
                    $temp_array = array();
                    foreach ($val as $data){
                        $temp_array[]=[

                            'Naration'=>$data['Naration'],
                            'amount'=>$data['amounts'],
                            'Rate'=>$data['Rate'],
                            'dayPerHour'=>$data['dayPerHour']
                        ];
                    }
                    $deduction_array[] = array(
                        'paysheetGroup'=>$key,
                        'items'=>$temp_array
                    );
                }
                return array(
                    'tableData'=>$deduction_array,
                    'total'=>$deduction_total
                );
            }
            return false;
        }
    }

    private function getBankDetailsForPayslip($empID,$salaryProcessMasterID)
    {
        if($empID!=null && $salaryProcessMasterID!=null){
            $bank_transfer_details = QryPayslipBankTransfer::select('bankName','branch','swiftCode','accountNo','CurrencyCode','transferAmount')
                ->where('empID',$empID)
                ->where('salaryProcessMasterID',$salaryProcessMasterID)
                ->get();

            if($bank_transfer_details->count()){
                $bank_transfer_total = $bank_transfer_details->sum('transferAmount');
                $bank_transfer = $bank_transfer_details->toArray();

                return array(
                    'total'=>$bank_transfer_total,
                    'tableData'=>$bank_transfer,
                );
            }
        }
        return [];
    }

}
