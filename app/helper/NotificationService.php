<?php

namespace App\helper;

use App\Models\Employee;
use App\Models\NotificationCompanyScenario;
use App\Models\NotificationUserDayCheck;
use Illuminate\Support\Arr;

class NotificationService
{
    public static function getCompanyScenarioConfiguration($scenarioID)
    {
        $companyScenarioConfiguration = NotificationCompanyScenario::with(['notification_Scenario' => function ($query) {
            $query->where('isActive', '=', 1);
        }, 'notification_day_setup' => function ($query) {
            $query->where('isActive', '=', 1);
        }, 'company'])
            ->whereHas('notification_Scenario', function ($query) {
                $query->where('isActive', '=', 1);
            })
            ->whereHas('notification_day_setup', function ($query) {
                $query->where('isActive', '=', 1);
            })
            ->where('isActive', '=', 1)
            ->where('scenarioID', '=', $scenarioID)
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
}
