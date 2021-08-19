<?php

namespace App\helper;

use App\Models\Employee;
use App\Models\NotificationCompanyScenario;
use App\Models\NotificationUserDayCheck;

class NotificationService
{
    public static function getCompanyScenarioConfiguration($scenarioID)
    {
        $companyScenarioConfiguration = NotificationCompanyScenario::with(['notification_Scenario', 'notification_day_setup','company'])
            ->whereHas('notification_Scenario', function ($query) {
                $query->where('isActive', '=', 1);
            })
            ->where('isActive', '=', 1)
            ->where('scenarioID', '=', $scenarioID)
            ->get();
        return $companyScenarioConfiguration;
    }
    public static function notificationUserSettings($notificationDaySetupID)
    {
        $notificationUser = NotificationUserDayCheck::with(['notification_user'])
            ->where('notificationDaySetupID', '=', $notificationDaySetupID)
            ->whereHas('notification_user', function ($q) {
                $q->whereNotNull('id');
            })
            ->get();
        return $notificationUser;
    }
    public static function userDetail($empID)
    {
        return Employee::where('employeeSystemID', $empID)
            ->first();
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
