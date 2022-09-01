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
                <td style="text-align:left;border: 1px solid black;">' . $val->documentCode . '</td> 
                <td style="text-align:left;border: 1px solid black;">' . $val->segment . '</td> 
                <td style="text-align:left;border: 1px solid black;">' . $val->currency . '</td> 
                <td style="text-align:right;border: 1px solid black;">' .$val->documentValue . '</td> 
                <td style="text-align:right;border: 1px solid black;">' .$val->remainingValue . '</td> 
                <td style="text-align:center;border: 1px solid black;">' . $val->cutOffDate . '</td> 
                </tr>';
            $x++;
        }
        $body .= '</tbody>
        </table>';
        return $body;
    }

    public static function getCutOffPurchaseOrders($dataBase, $partiallyRecivedPos, $type, $days, $emails, $companyIDFromScenarios)
    {

        $jobHeaderData['poCount'] = count($partiallyRecivedPos);
        $jobHeaderData['jobCount'] = 0;

        PoCutoffJob::create($jobHeaderData);

        foreach ($partiallyRecivedPos as $key => $value) {
           PurchaseOrderCutOffCheckJob::dispatch($dataBase, $type, $days, $value, $emails, $companyIDFromScenarios);
        }
    }
}
