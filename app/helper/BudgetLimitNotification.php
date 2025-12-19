<?php

namespace App\helper;

use Illuminate\Support\Facades\DB;
use App\helper\Helper;
use Carbon\Carbon;
use App\Models\Budjetdetails;
use App\Models\BudgetDetailHistory;
use App\Models\BudgetMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\BudgetConsumedData;
use App\Models\PurchaseRequest;
use App\Models\CompanyFinanceYear;
use App\Models\PurchaseRequestDetails;
use App\Models\PurchaseOrderDetails;
use App\Models\ReportTemplateDetails;
use App\Models\ProcumentOrder;
use App\Models\BookInvSuppMaster;
use App\Models\DirectInvoiceDetails;
use App\Models\SegmentAllocatedItem;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\DirectPaymentDetails;
use App\Models\ErpProjectMaster;
use App\Models\ChartOfAccount;
use App\Models\SegmentMaster;

class BudgetLimitNotification
{
    public static function getBudgetLimitDetails($companySystemID, $type)
    {
        $records = [];
        if ($type == 0) { // Same Day
           $checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
                                        ->where('companySystemID', $companySystemID)
                                        ->first();

            $checkBudgetBasedOnGL = CompanyPolicyMaster::where('companyPolicyCategoryID', 55)
                                            ->where('companySystemID', $companySystemID)
                                            ->first();

            $departmentWiseCheckBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 33)
                                        ->where('companySystemID', $companySystemID)
                                        ->first();

            $checkBudgetBasedOnGLPolicy = false;
            if ($checkBudgetBasedOnGL && $checkBudgetBasedOnGL->isYesNO == 1) {
                $checkBudgetBasedOnGLPolicy = true;
            }
            
            $departmentWiseCheckBudgetPolicy = false;
            if ($departmentWiseCheckBudget && $departmentWiseCheckBudget->isYesNO == 1) {
                $departmentWiseCheckBudgetPolicy = true;
            }

            $budgetFormData['departmentWiseCheckBudgetPolicy'] = $departmentWiseCheckBudgetPolicy;
            $budgetFormData['checkBudgetBasedOnGLPolicy'] = $checkBudgetBasedOnGLPolicy;
            $budgetFormData['companySystemID'] = $companySystemID;

            if ($checkBudget && $checkBudget->isYesNO == 1) {
                $records = self::budgetAmountAndConsumption($budgetFormData);
            }
        }
        return $records;
    }

    public static function budgetAmountAndConsumption($budgetFormData)
    {
        $budgetAmount = Budjetdetails::select(DB::raw("
                                       (SUM(budjetAmtLocal)) as totalLocal,
                                       (SUM(budjetAmtRpt)) as totalRpt,
                                       chartofaccounts.AccountCode,chartofaccounts.AccountDescription,
                                       erp_companyreporttemplatedetails.description as templateDetailDescription,
                                       erp_companyreporttemplatedetails.companyReportTemplateID as templatesMasterAutoID,
                                       erp_budjetdetails.*,
                                       ifnull(ca.consumed_amount,0) as consumed_amount,
                                       ((SUM(budjetAmtRpt)) - (ifnull(ca.consumed_amount,0))) AS balance
                                       "))
                                    ->where('erp_budjetdetails.companySystemID', $budgetFormData['companySystemID'])
                                    ->leftJoin('chartofaccounts', 'chartOfAccountID', '=', 'chartOfAccountSystemID')
                                    ->leftJoin('erp_budgetmaster', 'erp_budgetmaster.budgetmasterID', '=', 'erp_budjetdetails.budgetmasterID')
                                    ->leftJoin('erp_companyreporttemplatedetails', 'templateDetailID', '=', 'detID')
                                    ->leftJoin(DB::raw('(SELECT erp_budgetconsumeddata.companySystemID, erp_budgetconsumeddata.serviceLineSystemID,
                                                                        erp_budgetconsumeddata.chartOfAccountID, erp_budgetconsumeddata.Year, 
                                                                        Sum(erp_budgetconsumeddata.consumedRptAmount) AS consumed_amount FROM
                                                                        erp_budgetconsumeddata WHERE erp_budgetconsumeddata.consumeYN = -1 
                                                                        GROUP BY erp_budgetconsumeddata.companySystemID, erp_budgetconsumeddata.serviceLineSystemID, 
                                                                        erp_budgetconsumeddata.chartOfAccountID, erp_budgetconsumeddata.Year) as ca'),
                                        function ($join) {
                                            $join->on('erp_budjetdetails.companySystemID', '=', 'ca.companySystemID')
                                                ->on('erp_budjetdetails.serviceLineSystemID', '=', 'ca.serviceLineSystemID')
                                                ->on('erp_budjetdetails.Year', '=', 'ca.Year')
                                                ->on('erp_budjetdetails.chartOfAccountID', '=', 'ca.chartOfAccountID');
                                        })
                                    ->groupBy(['erp_budjetdetails.budjetDetailsID'])
                                    ->get();


        if ($budgetFormData['departmentWiseCheckBudgetPolicy']) {
            $groups = collect($budgetAmount)->groupBy(function ($item, $key) {
                                    return $item['budgetmasterID']."_".$item['serviceLineSystemID'];
                                });
            
        } else {
            $groups = collect($budgetAmount)->groupBy(function ($item, $key) {
                                    return $item['companyFinanceYearID']."_".$item['templatesMasterAutoID'];
                                });
        }


        $budgetAmount = $groups->map(function ($group) {

            $budgetLimit = $group->first()['sentNotificationAt'];

            $notfiy =  ((abs($group->sum('consumed_amount')) >= (abs($group->sum('totalRpt'))* ($budgetLimit/100))) && abs($group->sum('totalRpt')) > 0) ? 1 : 0;

            return [
                'budgetmasterID' => $group->first()['budgetmasterID'],
                'budgetLocalAmount' => abs($group->sum('totalLocal')),
                'budgetRptAmount' => abs($group->sum('totalRpt')),
                'limit' => (abs($group->sum('totalRpt')) * ($budgetLimit/100)),
                'notfiy' => $notfiy,
                'used' => abs($group->sum('consumed_amount'))
            ];
        });



        $notfifiedDetails = collect($budgetAmount)->where('notfiy', 1)->all();

        return (empty($notfifiedDetails)) ? [] : ['notfifiedDetails' => $notfifiedDetails, 'groups' => $groups, 'budgetFormData' => $budgetFormData];
    }

    public static function getEmailContent($details, $fullName)
    {
        $body = "Dear {$fullName},<br/>";
        $body .= "Following budgets has been reaches budget limit <br/><br/>";

        foreach ($details['notfifiedDetails'] as $key => $value) {
            foreach ($details['groups'] as $key1 => $value1) {
                if ($key == $key1) {
                    $budgetMasterData = BudgetMaster::with(['finance_year_by', 'segment_by'])->find($value['budgetmasterID']);
                    $serviceLineCode = '';
                    if($budgetMasterData->segment_by && $budgetMasterData->segment_by->ServiceLineCode)
                    {
                        $serviceLineCode = $budgetMasterData->segment_by->ServiceLineCode;
                    }
                    $serviceLine = ($details['budgetFormData']['departmentWiseCheckBudgetPolicy']) ? " of segment ".$serviceLineCode : "";

                    if ($details['budgetFormData']['checkBudgetBasedOnGLPolicy']) {
                        $templateWiseData = collect($value1)->groupBy('chartOfAccountID');

                        $groupedAmount = $templateWiseData->map(function ($group) {
                            return [
                                'budgetmasterID' => $group->first()['budgetmasterID'],
                                'chartOfAccountID' => $group->first()['chartOfAccountID'],
                                'budgetLocalAmount' => abs($group->sum('totalLocal')),
                                'consumed_amount' => abs($group->sum('consumed_amount')),
                                'budgetRptAmount' => abs($group->sum('totalRpt'))
                            ];
                        });

                        if (count($groupedAmount) > 0) {
                            $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                                    <thead>
                                        <tr>
                                            <th colspan="5" style="text-align: center;border: 1px solid black;">Budget for finance year '.\Helper::dateFormat($budgetMasterData->finance_year_by->bigginingDate).' - '.\Helper::dateFormat($budgetMasterData->finance_year_by->endingDate).$serviceLine.'</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align: center;border: 1px solid black;">#</th>
                                            <th style="text-align: center;border: 1px solid black;">Category</th> 
                                            <th style="text-align: center;border: 1px solid black;">Budget Amount</th>
                                            <th style="text-align: center;border: 1px solid black;">Consumed Amount</th>
                                            <th style="text-align: center;border: 1px solid black;">Balance Amount</th>
                                        </tr>
                                    </thead>';
                            $body .= '<tbody>';
                        }

                        $x = 1;
                        $budgetRptAmount = 0;
                        $totalBalanceAmount = 0;
                        $consumed_amount = 0;
                        foreach ($groupedAmount as $key2 => $value2) {
                             $chartOfAcData = ChartOfAccount::find($value2['chartOfAccountID']);
                             $budgetRptAmount += $value2['budgetRptAmount'];
                             $consumed_amount += $value2['consumed_amount'];
                             $balanceAmount = $value2['budgetRptAmount'] - $value2['consumed_amount'];
                             $totalBalanceAmount += $balanceAmount;
                             $body .= '<tr>
                                <td style="text-align:left;border: 1px solid black;">' . $x . '</td>  
                                <td style="text-align:left;border: 1px solid black;">' .  (($chartOfAcData) ? $chartOfAcData->AccountCode.' - '.$chartOfAcData->AccountDescription : "-") . '</td> 
                                <td style="text-align:right;border: 1px solid black;">' . $value2['budgetRptAmount'] . '</td> 
                                <td style="text-align:right;border: 1px solid black;">' . $value2['consumed_amount'] . '</td> 
                                <td style="text-align:right;border: 1px solid black;">' . $balanceAmount . '</td> 
                                </tr>';
                            $x++;
                        }
                        
                        if (count($groupedAmount) > 0) {
                            $body .= '<tr>
                                <td style="text-align:left;border: 1px solid black;" colspan="2">Total</td>  
                                <td style="text-align:right;border: 1px solid black;">' . $budgetRptAmount . '</td> 
                                <td style="text-align:right;border: 1px solid black;">' . $consumed_amount . '</td> 
                                <td style="text-align:right;border: 1px solid black;">' . $totalBalanceAmount . '</td> 
                                </tr>';
                            $body .= '</tbody>
                                    </table><br><hr><br>';
                        }
                   
                    } else {
                        $templateWiseData = collect($value1)->groupBy('templateDetailID');

                        $groupedAmount = $templateWiseData->map(function ($group) {
                            return [
                                'budgetmasterID' => $group->first()['budgetmasterID'],
                                'templateDetailID' => $group->first()['templateDetailID'],
                                'budgetLocalAmount' => abs($group->sum('totalLocal')),
                                'consumed_amount' => abs($group->sum('consumed_amount')),
                                'budgetRptAmount' => abs($group->sum('totalRpt'))
                            ];
                        });

                        if (count($groupedAmount) > 0) {
                            $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                                    <thead>
                                        <tr>
                                            <th colspan="5" style="text-align: center;border: 1px solid black;">Budget for finance year '.\Helper::dateFormat($budgetMasterData->finance_year_by->bigginingDate).' - '.\Helper::dateFormat($budgetMasterData->finance_year_by->endingDate).$serviceLine.'</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align: center;border: 1px solid black;">#</th>
                                            <th style="text-align: center;border: 1px solid black;">Category</th> 
                                            <th style="text-align: center;border: 1px solid black;">Budget Amount</th>
                                            <th style="text-align: center;border: 1px solid black;">Consumed Amount</th>
                                            <th style="text-align: center;border: 1px solid black;">Balance Amount</th>
                                        </tr>
                                    </thead>';
                            $body .= '<tbody>';
                        }

                        $x = 1;
                        $budgetRptAmount = 0;
                        $consumed_amount = 0;
                        $totalBalanceAmount = 0;
                        foreach ($groupedAmount as $key2 => $value2) {
                             $templateDetail = ReportTemplateDetails::find($value2['templateDetailID']);
                             $budgetRptAmount += $value2['budgetRptAmount'];
                             $consumed_amount += $value2['consumed_amount'];
                             $balanceAmount = $value2['budgetRptAmount'] - $value2['consumed_amount'];
                             $totalBalanceAmount += $balanceAmount;
                             $body .= '<tr>
                                <td style="text-align:left;border: 1px solid black;">' . $x . '</td>  
                                <td style="text-align:left;border: 1px solid black;">' . (isset($templateDetail->description) ? $templateDetail->description : "-") . '</td> 
                                <td style="text-align:right;border: 1px solid black;">' . $value2['budgetRptAmount'] . '</td> 
                                <td style="text-align:right;border: 1px solid black;">' . $value2['consumed_amount'] . '</td> 
                                <td style="text-align:right;border: 1px solid black;">' . $balanceAmount . '</td> 
                                </tr>';
                            $x++;
                        }
                        
                        if (count($groupedAmount) > 0) {
                             $body .= '<tr>
                                <td style="text-align:left;border: 1px solid black;" colspan="2">Total</td>  
                                <td style="text-align:right;border: 1px solid black;">' . $budgetRptAmount . '</td> 
                                <td style="text-align:right;border: 1px solid black;">' . $consumed_amount . '</td> 
                                <td style="text-align:right;border: 1px solid black;">' . $totalBalanceAmount . '</td> 
                                </tr>';
                            $body .= '</tbody>
                                    </table><br><hr><br>';
                        }
                    }
                }
            }
        }

        return $body;
    }
}
