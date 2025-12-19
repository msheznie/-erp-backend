<?php

namespace App\Services;

use App\helper\Helper;
use App\Models\ProcumentOrder;
use App\Models\CompanyPolicyMaster;
use App\Models\PoCutoffJob;
use App\Models\BudgetMaster;
use App\Models\GRVDetails;
use App\helper\BudgetConsumptionService;
use App\Jobs\CompanyWiseCutOffNotificationJob;
use App\Jobs\PurchaseOrderCutOffCheckJob;
use App\helper\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\helper\CommonJobService;

class BudgetCutOffNotificationService
{

    public static function sendBudgetCutOffNotification($dataBase = "")
    {
        Log::useFiles(storage_path() . '/logs/budget-cutoff-po.log');  
        $scenarios = NotificationService::getCompanyScenarioConfiguration(18);
        if (count($scenarios) == 0) {
            Log::info('Notification Company Scenario not exist in '.$dataBase);
        } else {
            $scenario_des = $scenarios[0]->notification_scenario->scenarioDescription;

            Log::info('------------ Successfully start ' . $scenario_des . ' Service ' . date('H:i:s') .  ' ------------');
            $scenarios = $scenarios->toArray();
            foreach ($scenarios as $compAssignScenario) {
                CompanyWiseCutOffNotificationJob::dispatch($dataBase, $compAssignScenario);
            }
        }

        return 'success';
    }

    public static function getEmailContent($details, $fullName)
    {
        $body = "Dear {$fullName},<br/>";
        $body .= "Please be informed following purchase order/s which are not received and budget cutoff period  are as follow. <br/><br/>";
        $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center;border: 1px solid black;">Document No</th> 
                        <th style="text-align: center;border: 1px solid black;">Segment</th>
                        <th style="text-align: center;border: 1px solid black;">Currency</th>
                        <th style="text-align: center;border: 1px solid black;">Document Value</th>
                        <th style="text-align: center;border: 1px solid black;">Remaining Value</th>
                        <th style="text-align: center;border: 1px solid black;">Cutoff Date</th>
                    </tr>
                </thead>';
        $body .= '<tbody>';
        $x = 1;
        foreach ($details as $val) {
            $body .= '<tr>
                <td style="text-align:left;border: 1px solid black;">' . $val['documentCode'] . '</td> 
                <td style="text-align:left;border: 1px solid black;">' . $val['segment'] . '</td> 
                <td style="text-align:left;border: 1px solid black;">' . $val['currency'] . '</td> 
                <td style="text-align:right;border: 1px solid black;">' .$val['documentValue'] . '</td> 
                <td style="text-align:right;border: 1px solid black;">' .$val['remainingValue'] . '</td> 
                <td style="text-align:center;border: 1px solid black;">' . $val['cutOffDate'] . '</td> 
                </tr>';
            $x++;
        }
        $body .= '</tbody>
        </table>';
        return $body;
    }

    public static function getCutOffPurchaseOrders($dataBase, $partiallyRecivedPos, $type, $days, $emails, $companyIDFromScenario)
    {

        // $jobHeaderData['poCount'] = count($partiallyRecivedPos);
        // $jobHeaderData['jobCount'] = 0;

        // PoCutoffJob::create($jobHeaderData);
        $poData = [];
        foreach ($partiallyRecivedPos as $key => $value) {
           // PurchaseOrderCutOffCheckJob::dispatch($dataBase, $type, $days, $value, $emails, $companyIDFromScenario);

            $now = Carbon::now();
            $checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
                                        ->where('companySystemID', $value['companySystemID'])
                                        ->first();

            if ($checkBudget && $checkBudget->isYesNO) {
                $budgetConsumedData = BudgetConsumptionService::getBudgetIdsByConsumption($value['documentSystemID'], $value['purchaseOrderID']);

                if (count($budgetConsumedData['budgetmasterIDs']) > 0) {
                    $budgetIds = array_unique($budgetConsumedData['budgetmasterIDs']);

                    foreach ($budgetIds as $key1 => $value1) {
                        $budgetMaster = BudgetMaster::with(['finance_year_by', 'segment_by'])->find($value1);

                        if ($budgetMaster && $budgetMaster->finance_year_by) {
                            $cutOffDate = Carbon::parse($budgetMaster->finance_year_by->endingDate)->addMonthsNoOverflow($budgetMaster->cutOffPeriod);

                            $diff = $now->diffInDays($cutOffDate);
                            $temp = [];
                            $temp['documentCode'] = $value['purchaseOrderCode'];
                            $temp['segment'] = ($budgetMaster && $budgetMaster->segment_by) ? $budgetMaster->segment_by->ServiceLineCode." - ".$budgetMaster->segment_by->ServiceLineDes : "";
                            $temp['currency'] = ($value['currency']) ? $value['currency']['CurrencyCode'] : "";
                            $temp['documentValue'] = ($value['currency']) ? number_format($value['poTotalSupplierTransactionCurrency'], $value['currency']['DecimalPlaces']) : number_format($value['poTotalSupplierTransactionCurrency'], 2);
                            $temp['cutOffDate'] = $cutOffDate->format('Y-m-d');

                            if (($cutOffDate > $now) && $diff > 0) {
                                if ($type == 1 && ($diff == $days)) { // Before 
                                   $recivedValue = ($value['grvRecieved'] == 1)  ? GRVDetails::selectRaw('SUM(erp_grvdetails.GRVcostPerUnitSupTransCur*erp_grvdetails.noQty) as total')->where('purchaseOrderMastertID', $value['purchaseOrderID'])->first()->total : 0;
                                   $temp['remainingValue'] = ($value['currency']) ? number_format(($value['poTotalSupplierTransactionCurrency'] - $recivedValue), $value['currency']['DecimalPlaces']) : number_format(($value['poTotalSupplierTransactionCurrency'] - $recivedValue), 2);

                                   // PoCutoffJobData::create($temp);
                                   $poData[] = $temp;
                                } 
                            } else if ($cutOffDate < $now && $diff > 0) {
                                if ($type == 2 && ($diff == $days)) { // After
                                   $recivedValue = ($value['grvRecieved'] == 1)  ? GRVDetails::selectRaw('SUM(erp_grvdetails.GRVcostPerUnitSupTransCur*erp_grvdetails.noQty) as total')->where('purchaseOrderMastertID', $value['purchaseOrderID'])->first()->total : 0;
                                   $temp['remainingValue'] = ($value['currency']) ? number_format(($value['poTotalSupplierTransactionCurrency'] - $recivedValue), $value['currency']['DecimalPlaces']) : number_format(($value['poTotalSupplierTransactionCurrency'] - $recivedValue), 2);
                                   // PoCutoffJobData::create($temp);
                                   $poData[] = $temp;
                                }
                            } else {
                                if ($type == 0 && ($diff == 0)) { // Same Day
                                   $recivedValue = ($value['grvRecieved'] == 1)  ? GRVDetails::selectRaw('SUM(erp_grvdetails.GRVcostPerUnitSupTransCur*erp_grvdetails.noQty) as total')->where('purchaseOrderMastertID', $value['purchaseOrderID'])->first()->total : 0;
                                   $temp['remainingValue'] = ($value['currency']) ? number_format(($value['poTotalSupplierTransactionCurrency'] - $recivedValue), $value['currency']['DecimalPlaces']) : number_format(($value['poTotalSupplierTransactionCurrency'] - $recivedValue), 2);
                                   // PoCutoffJobData::create($temp);
                                   $poData[] = $temp;
                                } 
                            }
                        }
                    }
                }
            }
        }


        if (!empty($poData)) {
            Log::info('type - '.$type);
            Log::info('days - '.$days);
            Log::info('poData - ');
            Log::info($poData);
            Log::info('emails - ');
            Log::info($emails);
            $subject = "Open Purchase Order/s Reaching budget cutoff period";
            foreach ($emails as $key => $notificationUserVal) {
                $emailContent = BudgetCutOffNotificationService::getEmailContent($poData, $notificationUserVal[$key]['empName']);

                $sendEmail = NotificationService::emailNotification($companyIDFromScenario, $subject, $notificationUserVal[$key]['empEmail'], $emailContent);

                if (!$sendEmail["success"]) {
                    Log::error($sendEmail["message"]);
                }
            }
        }
    }
}
