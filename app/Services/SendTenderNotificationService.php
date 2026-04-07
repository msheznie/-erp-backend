<?php

namespace App\Services;

use App\helper\CommonJobService;
use App\Models\NotificationCompanyScenario;
use App\Models\TenderMaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\helper\email as Email;
class SendTenderNotificationService
{
    public static function tenderNotificationScenarioBased()
    {
        $now = Carbon::now()->startOfMinute();
        $notificationScenarios = NotificationCompanyScenario::getCompanyScenario();
        foreach ($notificationScenarios as $scenario) {
            $companyID = $scenario['companyID'] ?? 0;
            $scenarioID = $scenario['scenarioID'] ?? 0;
            $scenarioDaySetups = $scenario['notification_day_setup'] ?? [];

            foreach ($scenarioDaySetups as $daySetup) {
                $beforeAfterType = $daySetup['beforeAfter'] ?? 0;
                $frequency = $daySetup['frequency'] ?? null;

                if ($beforeAfterType == 0 && $frequency !== null) {
                    continue;
                }
                if ($beforeAfterType != 0 && $frequency === null) {
                    continue;
                }

                $tenderList = TenderMaster::getTenderList($companyID, $scenarioID, $now, $beforeAfterType, $frequency);

                foreach ($tenderList as $tender) {
                    $dateField = self::getRelevantDateField($scenarioID, $tender->stage);
                    if (!$dateField || !isset($tender->$dateField) || !$tender->$dateField) {
                        continue;
                    }

                    $targetDate = Carbon::parse($tender->$dateField);
                    if (self::checkNotificationCondition($beforeAfterType, $frequency, $targetDate, $now)) {
                        if ($beforeAfterType == 0 && $tender->stage == 1 && $scenarioID == 46) {
                            continue;
                        }
                        self::sendReminder($tender, $scenarioID, $tender->stage, $beforeAfterType);
                    }
                }
            }
        }
    }

    private static function getRelevantDateField($scenarioID, $stage)
    {
        $dateFields = [
            45 => [
                1 => 'bid_opening_date',
                2 => 'technical_bid_opening_date'
            ],
            46 => [
                1 => 'bid_opening_date',
                2 => 'commerical_bid_opening_date',
            ],
        ];

        return $dateFields[$scenarioID][$stage] ?? null;
    }

    public static function checkNotificationCondition($beforeAfterType, $frequency, $tenderDate, $currentDate) {
        if ($beforeAfterType == 0 && $frequency === null) {
            return $tenderDate->isSameMinute($currentDate);
        }

        if ($beforeAfterType == 0 || $frequency === null) {
            return false;
        }

        $timeDifference = null;

        switch ($frequency) {
            case 1:
                $timeDifference = ['method' => 'subHours', 'value' => 1];
                break;
            case 2:
                $timeDifference = ['method' => 'subHours', 'value' => 3];
                break;
            case 3:
                $timeDifference = ['method' => 'subDays', 'value' => 1];
                break;
            case 4:
                $timeDifference = ['method' => 'subDays', 'value' => 3];
                break;
            case 5:
                $timeDifference = ['method' => 'subWeeks', 'value' => 1];
                break;
            case 6:
                $timeDifference = ['method' => 'subWeeks', 'value' => 2];
                break;
            case 7:
                $timeDifference = ['method' => 'subMonths', 'value' => 1];
                break;
            default:
                return false;
        }

        $notificationTime = $tenderDate->copy();
        if ($beforeAfterType == 1) {
            $notificationTime->{$timeDifference['method']}($timeDifference['value']);
        } else if ($beforeAfterType == 2) {
            $addMethod = str_replace('sub', 'add', $timeDifference['method']);
            $notificationTime->{$addMethod}($timeDifference['value']);
        } else {
            return false;
        }

        return $notificationTime->isSameMinute($currentDate);
    }

    public static function sendReminder($tender, $scenarioID, $stage, $beforeAfter = 0)
    {
        $reminderUsersList = $tender->tenderBidMinimumApproval ?? [];
        $emailSubject = self::getEmailSubject($scenarioID);
        if (!empty($reminderUsersList)) {
            foreach($reminderUsersList as $user){
                $dataEmail = [];
                $empName = $user->employee->empName ?? '-';
                $empEmail = $user->employee->empEmail ?? null;
                if($empEmail != null){
                    $emailBody = self::getEmailBody($scenarioID, $tender, $empName, $beforeAfter);
                    $dataEmail['empEmail'] = $empEmail;
                    $dataEmail['companySystemID'] = $tender->company_id;
                    $dataEmail['isEmailSend'] = 0;
                    $dataEmail['attachmentFileName'] = '';
                    $dataEmail['alertMessage'] = $emailSubject;
                    $dataEmail['emailAlertMessage'] = $emailBody;
                    $sendEmail = Email::sendEmailErp($dataEmail);
                    if (!$sendEmail["success"]) {
                        $errorMessage  = $sendEmail["message"];
                    }
                }
            }
        }
        if($scenarioID == 45){
            $techBidOpeningMembers = $tender->tenderUserAccess ?? [];
            if(!empty($techBidOpeningMembers)){
                foreach($techBidOpeningMembers as $user){
                    $dataEmail = [];
                    $empName = $user->employee->empName ?? '-';
                    $empEmail = $user->employee->empEmail ?? null;
                    if($empEmail != null){
                        $emailBody = self::getEmailBody($scenarioID, $tender, $empName, $beforeAfter);
                        $dataEmail['empEmail'] = $empEmail;
                        $dataEmail['companySystemID'] = $tender->company_id;
                        $dataEmail['isEmailSend'] = 0;
                        $dataEmail['attachmentFileName'] = '';
                        $dataEmail['alertMessage'] = $emailSubject;
                        $dataEmail['emailAlertMessage'] = $emailBody;
                        $sendEmail = Email::sendEmailErp($dataEmail);
                        if (!$sendEmail["success"]) {
                            $errorMessage  = $sendEmail["message"];
                        }
                    }
                }
            }
        }

        if($scenarioID == 46){
            $commercialBidOpeningMembers = $tender->tenderUserAccess ?? [];
            if(!empty($commercialBidOpeningMembers)){
                foreach($commercialBidOpeningMembers as $user){
                    $dataEmail = [];
                    $empName = $user->employee->empName ?? '-';
                    $empEmail = $user->employee->empEmail ?? null;
                    if($empEmail != null){
                        $emailBody = self::getEmailBody($scenarioID, $tender, $empName, $beforeAfter);
                        $dataEmail['empEmail'] = $empEmail;
                        $dataEmail['companySystemID'] = $tender->company_id;
                        $dataEmail['isEmailSend'] = 0;
                        $dataEmail['attachmentFileName'] = '';
                        $dataEmail['alertMessage'] = $emailSubject;
                        $dataEmail['emailAlertMessage'] = $emailBody;
                        $sendEmail = Email::sendEmailErp($dataEmail);
                        if (!$sendEmail["success"]) {
                            $errorMessage  = $sendEmail["message"];
                        }
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
    public static function getEmailBody($scenarioID, $tender, $empName, $beforeAfter = 0){
        $tenderDetails = [
            'code' => $tender->tender_code ?? '-',
            'title' => $tender->title ?? '-',
            'description' => $tender->description ?? '-',
        ];

        switch ($scenarioID){
            case 45:
                if($tender->stage == 1) {
                    $bidDate = $tender->bid_opening_date_time ? Carbon::parse($tender->bid_opening_date_time) : '-';
                    $bidEndDate = $tender->bid_opening_end_date_time ? Carbon::parse($tender->bid_opening_end_date_time) : '-';
                } else{
                    $bidDate = $tender->technical_bid_opening_date_time ? Carbon::parse($tender->technical_bid_opening_date_time) : '-';
                    $bidEndDate = $tender->technical_bid_closing_date_time ? Carbon::parse($tender->technical_bid_closing_date_time) : '-';
                }
                break;
            case 46:
                if($tender->stage == 1) {
                    $bidDate = $tender->bid_opening_date_time ? Carbon::parse($tender->bid_opening_date_time) : '-';
                    $bidEndDate = $tender->bid_opening_end_date_time ? Carbon::parse($tender->bid_opening_end_date_time) : '-';
                } else{
                    $bidDate = $tender->commerical_bid_opening_date_time ? Carbon::parse($tender->commerical_bid_opening_date_time) : '-';
                    $bidEndDate = $tender->commerical_bid_closing_date_time ? Carbon::parse($tender->commerical_bid_closing_date_time) : '-';
                }
                break;
            default:
                $bidDate = null;
                $bidEndDate = null;
                break;
        }
        $messages = [
            45 => 'Bid Opening Process',
            46 => 'Commercial Bid Opening Process',
        ];

        if (!isset($messages[$scenarioID])) {
            return '';
        }
        $bidEndDateLine = ($beforeAfter == 2) ? "<p><strong>Bid Opening End Date and Time:</strong> {$bidEndDate}</p>\n            " : '';
        return "
            <p>Dear {$empName},</p>
            <p>This is a reminder regarding the {$messages[$scenarioID]} for the following tender:</p>
            <p><strong>Tender Code:</strong> {$tenderDetails['code']}</p>
            <p><strong>Tender Title:</strong> {$tenderDetails['title']}</p>
            <p><strong>Tender Description:</strong> {$tenderDetails['description']}</p>
            <p><strong>Bid Opening Start Date and Time:</strong> {$bidDate}</p>
            {$bidEndDateLine}<p>Please ensure all necessary preparations are completed before the scheduled time.</p>
            <p>Thank you.</p>
        ";
    }
}
