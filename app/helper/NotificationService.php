<?php

namespace App\helper;

use App\Models\Employee;
use App\Models\NotificationCompanyScenario;
use App\Models\NotificationScenarios;
use App\Models\NotificationUserDayCheck;
use App\Models\NotificationHourSetup;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public static function log_file(){
        return storage_path() . '/logs/notification_service.log';
    }

    public static function db_switch( $db ){
        Config::set("database.connections.mysql.database", $db);
        DB::reconnect('mysql');

        return true;
    }

    public static function get_tenant_details(){
        return Tenant::get();
    }

    public static function hr_scenarios(){
        return [
            6, //HR document expiry
            7, //HR - Employee contract expiry
            8, //HR - Employee end of probation
            14, //HR - Employee end of shift period
            39 // HR - Department End date Expiry Notification
        ];
    }

    public static function all_active_scenarios(){
        return NotificationScenarios::where('isActive', 1)->get();
    }

    public static function process($scenarioID){
        $log_file = self::log_file();
        Log::useFiles($log_file);

        $com_assign_scenarios = NotificationService::getCompanyScenarioConfiguration($scenarioID);
        $emailContent = [];
        $subject = 'N/A';


        if (count($com_assign_scenarios) == 0) {
            Log::info('Notification Company Scenario not exist');
            return true;
        }

        $scenario_des = $com_assign_scenarios[0]->notification_scenario->scenarioDescription;

        Log::info('------------ Successfully start ' . $scenario_des . ' Service ' . date('H:i:s') .  ' ------------');

        foreach ($com_assign_scenarios as $compAssignScenario) {
            Log::info('Company Name: ' . $compAssignScenario->company->CompanyName);

            if (count($compAssignScenario->notification_day_setup) == 0) {
                Log::info('Notification day setup not exist');
                continue;
            }

            $companyID = $compAssignScenario->companyID;

            foreach ($compAssignScenario->notification_day_setup as $notDaySetup) {
                $beforeAfter = $notDaySetup->beforeAfter;
                $days = $notDaySetup->days;

                $details = [];

                switch ($scenarioID) {
                    case 1:
                        $details = RolReachedNotification::getRolReachedNotification($companyID, $beforeAfter);
                        $subject = 'Inventory stock reaches a minimum order level';
                        break;
                    case 2:
                        $details = PurchaseOrderPendingDeliveryNotificationService::getPurchaseOrderPendingDelivery($companyID, $beforeAfter, $days, 1);
                        $subject = 'Purchase order pending delivery notification';
                        break;
                    case 3:
                        $details = PurchaseOrderPendingDeliveryNotificationService::getPurchaseOrderPendingDelivery($companyID, $beforeAfter, $days, 5);
                        $subject = 'Work order expiry notification';
                        break;
                    case 4:
                        $details = AdvancePaymentNotification::getadvancePaymentDetails($companyID, $beforeAfter, $days);
                        $subject = 'Advance Payment Notification';
                        break;
                    case 5:
                        $details = BudgetLimitNotification::getBudgetLimitDetails($companyID, $beforeAfter);
                        $subject = 'Budget Limit Notification';
                        break;

                    case 6:
                        $hr_doc = new HRNotificationService($companyID, $notDaySetup);
                        $hr_doc->emp_expired_docs();
                        $details = [];
                        break;

                    case 7:
                        $contract = new HRContractNotificationService($companyID, $notDaySetup);
                        $contract->expired_doc();
                        $details = [];
                        break;

                    case 8:
                        $probation = new HRProbationNotificationService($companyID, $notDaySetup);
                        $probation->expired_doc();
                        $details = [];
                        break;

                    case 9:
                        $details = RolReachedNotification::getReOrderLevelReachedNotification($companyID, $beforeAfter);
                        $subject = 'Inventory stock reaches a re-order level';
                        break;

                    case 14:
                        $shift = new ShiftPeriodEndNotificationService($companyID, $notDaySetup);
                        $shift->ended_shift();
                        $details = [];
                        break;

                    case 39:
                        $department = new DepartmentExpiryNotificationService($companyID, $notDaySetup);
                        $department->proceed();
                        $details = [];
                        break;
                    case 47:
                        $supplier = new SupplierExpiryNotificationService($companyID, $notDaySetup);
                        $supplier->proceed();
                        $details = [];
                        break;

                    case 49:
                        $openPR = new OpenPurchaseRequestNotificationService($companyID);
                        $openPR->proceed();
                        $details = [];
                        break;

                    default:
                        Log::error("Applicable category configuration not exist for scenario {$scenario_des}");

                        break;
                }

                $hr_scenarios = self::hr_scenarios();
                if(in_array($scenarioID, $hr_scenarios)) {
                    continue;
                }

                if (count($details) == 0) {
                    Log::info("No records found for scenario {$scenario_des} ");
                    continue;
                }

                $notificationUserSettings = NotificationService::notificationUserSettings($notDaySetup->id);
                if (count($notificationUserSettings['email']) == 0) {
                    Log::info("User setup not found for scenario {$scenario_des}");
                    continue;
                }

                foreach ($notificationUserSettings['email'] as $key => $notificationUserVal) {

                    switch ($scenarioID) {
                        case 1:
                            $emailContent = RolReachedNotification::getRolReachedEmailContent($details, $notificationUserVal[$key]['empName']);
                            break;
                        case 2:
                            $emailContent = PurchaseOrderPendingDeliveryNotificationService::getPurchaseOrderEmailContent($details, $notificationUserVal[$key]['empName'], 1);
                            break;
                        case 3:
                            $emailContent = PurchaseOrderPendingDeliveryNotificationService::getPurchaseOrderEmailContent($details, $notificationUserVal[$key]['empName'], 5);
                            break;
                        case 4:
                            $emailContent = AdvancePaymentNotification::getAdvancePaymentEmailContent($details, $notificationUserVal[$key]['empName']);
                            break;
                        case 5:
                            $emailContent = BudgetLimitNotification::getEmailContent($details, $notificationUserVal[$key]['empName']);
                            break;
                        case 9:
                            $emailContent = RolReachedNotification::getReOrderLevelReachedEmailContent($details, $notificationUserVal[$key]['empName']);
                            break;
                        default:
                            Log::error("Email content configuration not done for scenario {$scenario_des}");
                            break;
                    }


                    $sendEmail = NotificationService::emailNotification($companyID, $subject, $notificationUserVal[$key]['empEmail'], $emailContent);

                    if (!$sendEmail["success"]) {
                        Log::error($sendEmail["message"]);
                    }
                }
            }

        }

        Log::info('------------ Successfully end ' . $scenario_des . ' Service ' . date('H:i:s') . ' ------------');

        return true;
    }

    public static function getCompanyScenarioConfiguration($scenarioID)
    {
        $companyScenarioConfiguration = NotificationCompanyScenario::where('isActive', '=', 1)
            ->where('scenarioID', '=', $scenarioID)
            ->has('company')
            ->with(['notification_Scenario' => function ($query) {
                $query->where('isActive', '=', 1);
            },
            'notification_day_setup' => function ($query) {
                $query->selectRaw('id,companyScenarionID,beforeAfter,days');
                $query->where('isActive', '=', 1);
            },
            'company'])
            ->whereHas('notification_Scenario', function ($query) {
                $query->where('isActive', '=', 1);
            })
            ->whereHas('notification_day_setup', function ($query) {
                $query->where('isActive', '=', 1);
            })
            ->get();

        return $companyScenarioConfiguration;
    }

    public static function notificationUserSettings($notificationDaySetupID)
    {
        $notificationUserSettingsArr = [
            'email' => array(),
            'push' => array(),
            'web' => array(),
        ];

        $emailNotificationArr = [];
        $pushNotificationArr = [];
        $webNotificationArr = [];

        $notificationUser = NotificationUserDayCheck::with(['notification_user'])
            ->where('notificationDaySetupID', '=', $notificationDaySetupID)
            ->get();

        foreach ($notificationUser as $notifiUserVal) {
            if ($notifiUserVal->emailNotification == 1) {
                switch ($notifiUserVal->notification_user->applicableCategoryID) {
                    case 1: //Employee 
                        $employee = Employee::where('employeeSystemID', $notifiUserVal->notification_user->empID)
                        ->first();
                        $dataEmail['empEmail'] = $employee->empEmail;
                        $dataEmail['empName'] = $employee->empFullName;
                        break;
                }
                array_push($emailNotificationArr, $dataEmail);
                array_push($notificationUserSettingsArr['email'], $emailNotificationArr);
            }
            if ($notifiUserVal->pushNotification == 1) {
                switch ($notifiUserVal->notification_user->applicableCategoryID) {
                    case 1: //Employee 
                        $employee = Employee::where('employeeSystemID', $notifiUserVal->notification_user->empID)
                        ->first();
                        $dataPush['token'] =  'asd';
                        break;
                } 
                array_push($pushNotificationArr, $dataPush);
                array_push($notificationUserSettingsArr['push'], $pushNotificationArr);
            }
            if ($notifiUserVal->webNotification == 1) {
                switch ($notifiUserVal->notification_user->applicableCategoryID) {
                    case 1: //Employee 
                        $employee = Employee::where('employeeSystemID', $notifiUserVal->notification_user->empID)
                        ->first();
                        $dataWeb['webToken'] = 'N/A';
                        break;
                }  
                array_push($webNotificationArr, $dataWeb);;
                array_push($notificationUserSettingsArr['web'], $webNotificationArr);
            }
        }
        return $notificationUserSettingsArr;
    }

    public static function emailNotification($companyID, $subject, $userEmail, $body)
    {
        $emails = [
            'companySystemID' => $companyID,
            'alertMessage' => $subject,
            'empEmail' => $userEmail,
            'emailAlertMessage' => $body
        ];
        $sendEmail = \Email::sendEmailErp($emails);
        return $sendEmail;

    }

    public static function get_filter_date($type, $days){
        // for same day $type will be 0 ( zero )
        $filter_date = Carbon::now();

        if($type == 1){ //Before
            $filter_date = $filter_date->addDays($days);
        }
        elseif ($type == 2 ){ // After
            $filter_date = $filter_date->subDays($days);
        }

        return $filter_date->format('Y-m-d');
    }

    public static function getCompanyScenarioConfigurationForCompany($scenarioID,$companyID)
    {
        $companyScenarioConfiguration = NotificationCompanyScenario::where('isActive', '=', 1)
            ->where('scenarioID', '=', $scenarioID)
            ->where('companyID',$companyID)
            ->has('company')
            ->with(['notification_Scenario' => function ($query) {
                $query->where('isActive', '=', 1);
            },
            'notification_day_setup' => function ($query) {
                $query->selectRaw('id,companyScenarionID,beforeAfter,days');
                $query->where('isActive', '=', 1);
            },
            'company'])
            ->whereHas('notification_Scenario', function ($query) {
                $query->where('isActive', '=', 1);
            })
            ->whereHas('notification_day_setup', function ($query) {
                $query->where('isActive', '=', 1);
            })
            ->first();

        return $companyScenarioConfiguration;
    }

    public static function getActiveCompanyByScenario($scenarioID)
    {
        return NotificationCompanyScenario::select('id', 'scenarioID', 'companyID')
            ->where('isActive', 1)
            ->where('scenarioID', $scenarioID)            
            ->with('company:companySystemID,CompanyID,CompanyName')
            ->with('user:empID,companyScenarionID,applicableCategoryID')
            ->has('company')
            ->whereHas('notification_Scenario', function ($query) {
                $query->where('isActive', 1);
            }) 
            ->whereHas('user', function ($query) {
                $query->where('isActive', 1);
            }) 
            ->get();        
    }

    public static function getHours($companyScenarioId)
    {
        $result = NotificationHourSetup::where('company_scenario_id', $companyScenarioId)
            ->where('before_after', 2)
            ->value('hours');

        return empty($result) ? '' : $result;
    }
}
