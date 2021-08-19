<?php

namespace App\helper;

use App\helper\NotificationService;
use App\Models\ItemAssigned;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RolReachedNotification
{
    public static function sendRolReachedNotificationEmail($scenarioID)
    {
        $getROL = [];
        $userDetail = null;
        $companyAssignScenarion = NotificationService::getCompanyScenarioConfiguration($scenarioID);
        Log::useFiles(storage_path() . '/logs/notification_service.log');
        Log::info('------------ Successfully start Inventory stock reaches a minimum order level Service ' . date('H:i:s') .  ' ------------');
        if (count($companyAssignScenarion) > 0) {
            foreach ($companyAssignScenarion as $compAssignScenario) {
                Log::info('Company Name: ' . $compAssignScenario->company->CompanyName);
                if (count($compAssignScenario->notification_day_setup) > 0) {
                    foreach ($compAssignScenario->notification_day_setup as $notDaySetup) {
                        if ($notDaySetup->beforeAfter == 0) {
                            $getROL = self::getRolSameDay($compAssignScenario->companyID);
                        }else { 
                            $getROL = [];
                            Log::error('Day setup configuration not exist');
                        }
                        if (count($getROL) > 0) {
                            $notificationDayCheck = NotificationService::notificationUserSettings($notDaySetup->id);
                            if (count($notificationDayCheck) > 0) {
                                foreach ($notificationDayCheck as $notificationDay) {
                                    if ($notificationDay->emailNotification == 1) {
                                        switch ($notificationDay->notification_user->applicableCategoryID) {
                                            case 1: //Employee
                                                $userDetail =  NotificationService::userDetail($notificationDay->notification_user->empID);
                                                $userEmail = $userDetail->empEmail;
                                                break;
                                            default :
                                                Log::error('Applicable category configuration not exist');
                                            break;
                                        }
                                        if($userDetail!=null) { 
                                            $body = "Dear {$userDetail->empName},<br/>";
                                            $body .= "Following items has been reaches minimum order level <br/><br/>";
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
                                            foreach ($getROL as $val) {
                                                $body .= '<tr>
                                                    <td style="text-align:left;border: 1px solid black;">' . $x . '</td>  
                                                    <td style="text-align:left;border: 1px solid black;">' . $val->secondaryItemCode . '</td> 
                                                    <td style="text-align:left;border: 1px solid black;">' . $val->itemDescription . '</td> 
                                                    <td style="text-align:left;border: 1px solid black;">' . $val->rolQuantity . '</td> 
                                                    <td style="text-right:left;border: 1px solid black;">' . $val->INoutQty . '</td> 
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
        Log::info('------------ Successfully end Inventory stock reaches a minimum order level Service' . date('H:i:s') . ' ------------');
    }

    public static function getRolSameDay($companyID)
    {
        $records = DB::table("itemassigned")
            ->selectRaw("itemDescription, itemCodeSystem, rolQuantity, IFNULL(ledger.INoutQty,0) as INoutQty,itemPrimaryCode,secondaryItemCode")
            ->join(DB::raw('(SELECT itemSystemCode, SUM(inOutQty) as INoutQty FROM erp_itemledger WHERE companySystemID = ' . $companyID . ' GROUP BY itemSystemCode) as ledger'), function ($query) {
                $query->on('itemassigned.itemCodeSystem', '=', 'ledger.itemSystemCode');
            })
            ->where('companySystemID', '=', $companyID)
            ->where('financeCategoryMaster', '=', 1)
            ->whereRaw('ledger.INoutQty <= rolQuantity')->get();
        return $records;
    }
}
