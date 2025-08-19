<?php

namespace App\Services;

use App\helper\CommonJobService;
use App\Models\NotificationCompanyScenario;
use App\Models\TenderMaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendTenderNotificationService
{
    public static function tenderNotificationScenarioBased()
    {
        Log::useFiles(CommonJobService::get_specific_log_file('tender-bid'));

        $now = Carbon::now();
        $notificationScenarios = NotificationCompanyScenario::getCompanyScenario();
        foreach ($notificationScenarios as $scenario) {
            $companyID = $scenario['companyID'] ?? 0;
            $scenarioID = $scenario['scenarioID'] ?? 0;
            $scenarioDaySetups = $scenario['notification_day_setup'] ?? [];

            foreach ($scenarioDaySetups as $daySetup) {
                $beforeAfterType = $daySetup['beforeAfter'] ?? 0;
                $frequency = $daySetup['frequency'] ?? 0;

                $tenderList = TenderMaster::getTenderList($companyID, $scenarioID);

                foreach ($tenderList as $tender) {
                    $dateField = self::getRelevantDateField($scenarioID, $tender->stage, $beforeAfterType);

                    if (!$dateField || !isset($tender->$dateField)) {
                        continue;
                    }

                    $targetDate = Carbon::parse($tender->$dateField);
                    if (self::checkNotificationCondition($beforeAfterType, $frequency, $targetDate, $now)) {
                        self::sendReminder($tender, $scenarioID, $tender->stage);
                    }
                }

            }
        }
    }

    private static function getRelevantDateField($scenarioID, $stage, $beforeAfterType)
    {
        $dateFields = [
            45 => [
                1 => [1 => 'bid_opening_date'],
                2 => [1 => 'technical_bid_opening_date'],
            ],
            46 => [
                1 => [2 => 'bid_opening_date'],
                2 => [2 => 'commerical_bid_opening_date'],
            ],
        ];

        return $dateFields[$scenarioID][$stage][$beforeAfterType] ?? null;
    }

    public static function checkNotificationCondition($beforeAfterType, $frequency, $tenderDate, $currentDate) {
        $timeDifference = null;

        switch ($frequency) {
            case 1:
                $timeDifference = ['method' => 'subHours', 'value' => 3];
                break;
            case 2:
                $timeDifference = ['method' => 'subDays', 'value' => 1];
                break;
            case 3:
                $timeDifference = ['method' => 'subDays', 'value' => 3];
                break;
            case 4:
                $timeDifference = ['method' => 'subWeeks', 'value' => 1];
                break;
            case 5:
                $timeDifference = ['method' => 'subWeeks', 'value' => 2];
                break;
            case 6:
                $timeDifference = ['method' => 'subMonths', 'value' => 1];
                break;
            default:
                return false;
        }

        $notificationTime = $tenderDate->copy();
        if ($beforeAfterType == 1) {
            $notificationTime->{$timeDifference['method']}($timeDifference['value']);
        } else {
            $addMethod = str_replace('sub', 'add', $timeDifference['method']);
            $notificationTime->{$addMethod}($timeDifference['value']);
        }
        return $beforeAfterType != 0 ? $notificationTime->equalTo($currentDate) : $tenderDate->equalTo($currentDate);
    }

    public static function sendReminder($tender, $scenarioID, $stage)
    {
        $reminderUsersList = $tender->tenderBidMinimumApproval ?? [];
        $emailSubject = self::getEmailSubject($scenarioID);
        if (!empty($reminderUsersList)) {
            foreach($reminderUsersList as $user){
                $dataEmail = [];
                $empName = $user->employee->empName ?? '-';
                $empEmail = $user->employee->empEmail ?? null;
                if($empEmail != null){
                    $emailBody = self::getEmailBody($scenarioID, $tender, $empName);
                    $dataEmail['empEmail'] = $empEmail;
                    $dataEmail['companySystemID'] = $tender->company_id;
                    $dataEmail['isEmailSend'] = 0;
                    $dataEmail['attachmentFileName'] = '';
                    $dataEmail['alertMessage'] = $emailSubject;
                    $dataEmail['emailAlertMessage'] = $emailBody;
                    $sendEmail = \Email::sendEmailErp($dataEmail);
                    if (!$sendEmail["success"]) {
                        $errorMessage  = $sendEmail["message"];
                        Log::info("Error: $errorMessage");
                    }
                }
            }
        }

        return true;
    }

    public static function getEmailSubject($scenarioID)
    {
        $subjects = [
            45 => 'Tender Bid Opening Reminder',
            46 => 'Tender Commercial Bid Opening Reminder',
        ];

        return $subjects[$scenarioID] ?? '';
    }
    public static function getEmailBody($scenarioID, $tender, $empName){
        $tenderDetails = [
            'code' => $tender->tender_code ?? '-',
            'title' => $tender->title ?? '-',
            'description' => $tender->description ?? '-',
        ];

        switch ($scenarioID){
            case 45:
                if($tender->stage == 1) {
                    $bidDate = $tender->bid_opening_date_time ? Carbon::parse($tender->bid_opening_date_time) : '-';
                } else{
                    $bidDate = $tender->technical_bid_opening_date_time ? Carbon::parse($tender->technical_bid_opening_date_time) : '-';
                }
                break;
            case 46:
                if($tender->stage == 1) {
                    $bidDate = $tender->bid_opening_date_time ? Carbon::parse($tender->bid_opening_date_time) : '-';
                } else{
                    $bidDate = $tender->commerical_bid_opening_date_time ? Carbon::parse($tender->commerical_bid_opening_date_time) : '-';
                }
                break;
            default:
                $bidDate = null;
                break;
        }
        $messages = [
            45 => 'Bid Opening Process',
            46 => 'Commercial Bid Opening Process',
        ];

        if (!isset($messages[$scenarioID])) {
            return '';
        }
        return "
            <p>Dear {$empName},</p>
            <p>This is a reminder regarding the {$messages[$scenarioID]} for the following tender:</p>
            <p><strong>Tender Code:</strong> {$tenderDetails['code']}</p>
            <p><strong>Tender Title:</strong> {$tenderDetails['title']}</p>
            <p><strong>Tender Description:</strong> {$tenderDetails['description']}</p>
            <p><strong>Bid Opening Start Date and Time:</strong> {$bidDate}</p>
            <p>Please ensure all necessary preparations are completed before the scheduled time.</p>
            <p>Thank you.</p>
        ";
    }
}
