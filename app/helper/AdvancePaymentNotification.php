<?php

namespace App\helper;

use App\helper\NotificationService;
use App\Models\PoPaymentTerms;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdvancePaymentNotification
{
    public static function sendAdvancePaymentNotificationEmail($scenarioID)
    {
        $getROL = [];
        $advancePayment = null;
        $companyAssignScenarion = NotificationService::getCompanyScenarioConfiguration($scenarioID);
        Log::useFiles(storage_path() . '/logs/notification_service.log');
        Log::info('------------ Successfully start Advance payment notification Service ' . date('H:i:s') .  ' ------------'); 
        if (count($companyAssignScenarion) > 0) {
            foreach ($companyAssignScenarion as $compAssignScenario) {
                Log::info('Company Name: ' . $compAssignScenario->company->CompanyName); 
                if (count($compAssignScenario->notification_day_setup) > 0) {
                    foreach ($compAssignScenario->notification_day_setup as $notDaySetup) {
                        if ($notDaySetup->beforeAfter == 0) { // Same Day
                            $advancePayment = self::getadvancePaymentSameDay($compAssignScenario->companyID);
                        } else if ($notDaySetup->beforeAfter == 1) { // Before
                            $advancePayment = self::getadvancePaymentBefore($compAssignScenario->companyID, $notDaySetup->days);
                        } else if ($notDaySetup->beforeAfter == 2) { // Afer
                            $advancePayment = self::getadvancePaymentAfer($compAssignScenario->companyID, $notDaySetup->days);
                        } else {
                            $advancePayment = [];
                            Log::error('Day setup configuration not exist');
                        }
                        if (count($advancePayment) > 0) {
                            $notificationDayCheck = NotificationService::notificationUserSettings($notDaySetup->id);
                            if (count($notificationDayCheck) > 0) {
                                foreach ($notificationDayCheck as $notificationDay) {
                                    if ($notificationDay->emailNotification == 1) {
                                        switch ($notificationDay->notification_user->applicableCategoryID) {
                                            case 1: //Employee
                                                $userDetail =  NotificationService::userDetail($notificationDay->notification_user->empID);
                                                $userEmail = $userDetail->empEmail;
                                                break;
                                            default:
                                                Log::error('Applicable category configuration not exist');
                                                break;
                                        }
                                        if ($userDetail != null) {
                                            $body = "Dear {$userDetail->empName},<br/>";
                                            $body .= "Following items has been reaches minimum order level  $notDaySetup->beforeAfter<br/><br/>";
                                            $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                                                        <thead>
                                                            <tr>
                                                                <th style="text-align: center;border: 1px solid black;">#</th>
                                                                <th style="text-align: center;border: 1px solid black;">Item Code</th> 
                                                                <th style="text-align: center;border: 1px solid black;">Item Description</th>
                                                                <th style="text-align: center;border: 1px solid black;">ROL</th>
                                                                <th style="text-align: center;border: 1px solid black;">Available Stock</th>
                                                            </tr>
                                                        </thead>';
                                            $body .= '<tbody>';
                                            $x = 1;
                                            foreach ($advancePayment as $val) {
                                                $body .= '<tr>
                                                    <td style="text-align:left;border: 1px solid black;">' . $x . '</td>  
                                                    <td style="text-align:left;border: 1px solid black;">' . $val->purchase_order_master->purchaseOrderCode . '</td>  
                                                </tr>';
                                                $x++;
                                            }
                                            $body .= '</tbody>
                                                    </table>';
                                            $sendEmail = NotificationService::emailNotification($compAssignScenario->companyID, 'Inventory stock reaches a minimum order level', $userEmail, $body);
                                            if (!$sendEmail["success"]) {
                                                Log::error($sendEmail["message"]);
                                            }
                                        }
                                    }
                                }
                            } else {
                                Log::info('Notification day check not exist');
                            }
                        } else {
                            Log::info('Reorder level minimum records empty');
                        }
                    }
                } else {
                    Log::info('Notification day setup not exist');
                }
            }
        } else {
            Log::info('Notification Company Scenario not exist');
        }
        Log::info('------------ Successfully end Advance payment notification Service' . date('H:i:s') . ' ------------');
    }

    public static function getadvancePaymentSameDay($companyID)
    {
        $today = Carbon::today()->toDateString();
        $records = PoPaymentTerms::with(['purchase_order_master' => function ($q) use ($companyID) {
            $q->where('companySystemID', $companyID);
        }])
            ->where('LCPaymentYN', '=', 2)
            ->where('isRequested', '=', 1)
            ->WhereHas('purchase_order_master', function ($q) use ($companyID) {
                $q->where('companySystemID', $companyID);
            })
            ->whereRaw('DATE(comDate) = "' . $today . '"')
            ->get();
        return $records;
    }
    public static function getadvancePaymentBefore($companyID, $days)
    {
        $today = Carbon::today()->toDateString();
        $days = $days * -1;
        $records = PoPaymentTerms::with(['purchase_order_master' => function ($q) use ($companyID) {
            $q->where('companySystemID', $companyID);
        }])
            ->where('LCPaymentYN', '=', 2)
            ->where('isRequested', '=', 1)
            ->WhereHas('purchase_order_master', function ($q) use ($companyID) {
                $q->where('companySystemID', $companyID);
            })
            ->whereRaw('DATE_ADD(DATE(comDate), INTERVAL ' . $days . ' DAY) = "' . $today . '"')
            ->get();
        return $records;
    }
    public static function getadvancePaymentAfer($companyID, $days)
    {
        $today = Carbon::today()->toDateString();
        $records = PoPaymentTerms::with(['purchase_order_master' => function ($q) use ($companyID) {
            $q->where('companySystemID', $companyID);
        }])
            ->where('LCPaymentYN', '=', 2)
            ->where('isRequested', '=', 1)
            ->WhereHas('purchase_order_master', function ($q) use ($companyID) {
                $q->where('companySystemID', $companyID);
            })
            ->whereRaw('DATE_ADD(DATE(comDate), INTERVAL ' . $days . ' DAY) = "' . $today . '"')
            ->get();
        return $records;
    }
}
