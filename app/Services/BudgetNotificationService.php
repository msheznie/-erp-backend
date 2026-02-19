<?php

namespace App\Services;

use App\Models\BudgetNotification;
use App\Models\BudgetNotificationDetail;
use App\Models\BudgetNotificationRecipient;
use App\Models\CompanyDepartment;
use App\Models\DepartmentBudgetPlanning;
use App\Models\DepartmentBudgetPlanningsDelegateAccess;
use App\Models\CompanyFinanceYear;
use App\Models\BudgetDelegateAccessRecord;
use App\Models\CompanyDepartmentEmployee;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class BudgetNotificationService
{

    private $companySystemID;
    private $scenario;
    private $delegateID;
    private $baseurl;
    private $reminderTime;
    /**
     * Replace placeholders in template string with actual values
     *
     * @param string $template
     * @param array $replacements
     * @param bool $convertNewlines Whether to convert \n to <br> for HTML emails
     * @return string
     */
    public function replacePlaceholders($template, $replacements = [], $convertNewlines = true)
    {
        if (empty($template)) {
            return $template;
        }

        // Build search and replace arrays
        $search = [];
        $replace = [];

        foreach ($replacements as $key => $value) {
            $search[] = '{{' . $key . '}}';
            $replace[] = $value;
            // Also replace literal placeholder text when key is bracket-style e.g. [Budget Review Dashboard]
            if (strpos($key, '[') !== false && strpos($key, ']') !== false) {
                $search[] = $key;
                $replace[] = $value;
            }
        }

        $result = str_replace($search, $replace, $template);

        // Convert newlines to HTML line breaks for email
        if ($convertNewlines) {
            // Convert \n\n to double line breaks, \n to single line break
            $result = str_replace(["\n\n", "\n"], ['<br><br>', '<br>'], $result);
            // Also handle escaped newlines in stored strings
            $result = str_replace(['\\n\\n', '\\n'], ['<br><br>', '<br>'], $result);
        }

        return $result;
    }

   /**
    * Send notification based on scenario
    *
    * @param string $scenario
    * @param int $companySystemID
    * @param array $placeholders Optional array of placeholder values to replace
    * @return array
    */
   public function sendNotification($departmentBudgetPlanningID, $scenario, $companySystemID, $delegateID = null,$baseurl = null, $reminderTime = null)
   {

       $this->companySystemID = $companySystemID;
       $this->scenario = $scenario;
       $this->delegateID = $delegateID;
       $this->baseurl = $baseurl;
       $this->reminderTime = $reminderTime;
       try {
           $budgetNotifications = BudgetNotification::where('slug', $scenario)->first();
           if (!$budgetNotifications) {
               return [
                   'success' => false,
                   'message' => 'Notification template not found for scenario: ' . $scenario
               ];
           }

           $budgetNotificationDetails = BudgetNotificationDetail::where('notification_id', $budgetNotifications->id)
                                       ->where('isActive', 1)
                                       ->where('companySystemID', $companySystemID)
                                       ->first();

           if (!$budgetNotificationDetails) {
               return;
           }

           $this->sendEmail($departmentBudgetPlanningID,$budgetNotifications);

       } catch (Exception $e) {
           return [
               'success' => false,
               'message' => 'Error sending notification: ' . $e->getMessage()
           ];
       }
   }

   /**
    * Get recipients for a notification
    *
    * @param \App\Models\BudgetNotification $notification
    * @return array
    */
   public function getNotificationRecipients($notification)
   {
       $recipients = [];
       
       if ($notification && $notification->recipient) {
           $recipientIds = is_array($notification->recipient) 
               ? $notification->recipient 
               : json_decode($notification->recipient, true);
               
           if (is_array($recipientIds) && !empty($recipientIds)) {
               $recipientRecords = BudgetNotificationRecipient::whereIn('id', $recipientIds)->get();
               $recipients = $recipientRecords->pluck('title')->toArray();
           }
       }

       return $recipients;
   }

   public function sendEmail($departmentBudgetPlanningID, $budgetNotifications) 
   {
        $departmentBudgetPlanning = DepartmentBudgetPlanning::with(['department.hod.employee','masterBudgetPlannings.company'])->find($departmentBudgetPlanningID);

        $slug = $budgetNotifications->slug;
        switch($slug)
        {
            case 'kick-off':
                $this->sendKickOffEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
                break;
            case 'task-delegation':
                $this->sendTaskDelegattionEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
                break;
            case 'delegation-confirmation':
                // $this->sendDelegationConfirmationEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
                break;
            case 'deadline-warning':
                $this->sendDeadlineWarningEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
                break;
            case 'submission-deadline-reached':
                $this->sendSubmissionDeadlineReachedEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
                break;
            case 'delegatee-submission':
                $this->sendDelegateeSubmissionEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
                break;
            case 'final-submission-to-finance':
                $this->sendFinalSubmissionToFinanceEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
                break;
            case 'finance-rejects-for-revision':
                $this->sendFinanceRejectsForRevisionEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
                break;
            case 'extension-request-submitted':
                $this->sendTimeExtensionRequestSubmittedEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
                break;
            case 'extension-request-approved':
                $this->sendTimeExtentionApprovedEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
                break;
            case 'extension-request-rejected':
                $this->sendTimeExtensionRequestCancelledEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
                break;
        }

   }

   public function sendKickOffEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::find($departmentBudgetPlanningID);
        $department = CompanyDepartment::find($budgetPlanning->departmentID);

        $departmentBudgetYear = CompanyFinanceYear::find($departmentBudgetPlanning->yearID);

        // Get template body and subject
        $bodyTemplate = $budgetNotifications->body;
        $subjectTemplate = $budgetNotifications->subject;

        $hod = $department->hod->employee;


        $linkUrl = str_replace('approval/erp', 'budget-planning/planning', $this->baseurl);
        $placeholders = [
            'HODName' => $hod->empName.' ('.$hod->empID.')',
            'BudgetYear' => date('d/m/Y', strtotime($departmentBudgetYear->bigginingDate)).' - '.date('d/m/Y', strtotime($departmentBudgetYear->endingDate)),
            'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
            'link' => '<a href="' . $linkUrl . '" style="color: #007bff; text-decoration: underline;">Click here to view the budget planning</a>'
        ];

        $emails[] = array(
            'empEmail' => $hod->empEmail,
            'companySystemID' => $departmentBudgetPlanning->masterBudgetPlannings->companySystemID,
            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true), // Body needs line breaks
            'empSystemID' => $hod->employeeSystemID,
            'docSystemID' => 133,
            'docSystemCode' => $departmentBudgetPlanningID
        );

        \Email::sendEmail($emails);

   }

   private function sendTaskDelegattionEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
            $budgetPlanning = DepartmentBudgetPlanning::with(['timeExtensionRequests' => function ($query) {
                $query->where('status', 2);
            }],'department.hod.employee','masterBudgetPlannings.company','revisions','delegateAccess.employee')->find($departmentBudgetPlanningID);

        $revision = $budgetPlanning->revisions->where('revisionStatus', 1)->first();

        $delegatee = CompanyDepartmentEmployee::with('employee')->find($this->delegateID);

        $budgetDelegateAccess = BudgetDelegateAccessRecord::where('delegatee_id', $this->delegateID)->latest()->first();

        if(empty($revision)) {
            $placeholders = [
                'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
                'HODName' => $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
                'DeadlineDate' => ($budgetDelegateAccess) ? date('d/m/Y', strtotime($budgetDelegateAccess->submission_time)) : (date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A'),
                'DelegateeName' => $delegatee->employee->empName.' ('.$delegatee->employee->empID.')',
            ];   
        }else {
            $placeholders = [
                'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
                'HODName' => $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
                'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
                'DelegateeName' => $delegatee->employee->empName.' ('.$delegatee->employee->empID.')',
                'RevisionDeadline' => date('d/m/Y', strtotime($revision->newSubmissionDate)) ?? 'N/A',
            ];
            $budgetNotifications = BudgetNotification::where('slug', 'hod-re-delegates-revision')->first();

        }

            
        $subjectTemplate = $budgetNotifications->subject;
        $bodyTemplate = $budgetNotifications->body;
        

        $emails[] = array(
            'empEmail' => $delegatee->employee->empEmail,
            'companySystemID' => $departmentBudgetPlanning->masterBudgetPlannings->companySystemID,
            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true), // Body needs line breaks
            'empSystemID' => $delegatee->employee->employeeSystemID,
            'docSystemID' => 133,
            'docSystemCode' => $departmentBudgetPlanningID
        );


        \Email::sendEmail($emails);

   }

   private function sendDelegationConfirmationEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company')->find($departmentBudgetPlanningID);
        $delegatee = CompanyDepartmentEmployee::with('employee')->find($this->delegateID);

        $placeholders = [
            'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
            'HODName' => $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
            'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
            'DelegateeName' => $delegatee->employee->empName.' ('.$delegatee->employee->empID.')',
        ];

        $subjectTemplate = $budgetNotifications->subject;
        $bodyTemplate = $budgetNotifications->body;

        $emails[] = array(
            'empEmail' => $budgetPlanning->department->hod->employee->empEmail,
            'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true), // Body needs line breaks,
            'empSystemID' => $budgetPlanning->department->hod->employee->employeeSystemID,
            'docSystemID' => 133,
            'docSystemCode' => $departmentBudgetPlanningID
        );


        \Email::sendEmail($emails);

   }

   private function sendDeadlineWarningEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $this->sendEmailToHOD($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
        $this->sendEmailToDelegatee($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
       

   }

   private function sendSubmissionDeadlineReachedEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $this->sendEmailToHOD($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);

        $budgetNotificationDetails = BudgetNotificationDetail::where('notification_id', 6)->where('isActive', 1)->where('companySystemID', $this->companySystemID)->exists();
        if(!$budgetNotificationDetails) {
            return;
        }
        $budgetPlanning = DepartmentBudgetPlanning::with([
            'department.hod.employee',
            'masterBudgetPlannings.company',
            'revisions',
            'budgetPlanningDetails' => function ($q) {
                $q->whereHas('budgetDelegateAccessDetails');
            },
            'budgetPlanningDetails.budgetDelegateAccessDetails.delegatee.employee',
        ])->find($departmentBudgetPlanningID);
        $revision = $budgetPlanning->revisions->where('revisionStatus', 1)->first();
        $departmentBudgetYear = CompanyFinanceYear::find($departmentBudgetPlanning->yearID);

        $departmentBudgetPlanningDetails = $budgetPlanning->budgetPlanningDetails;
        $today = Carbon::today();
        $todayStr = $today->toDateString();
        $emails = [];

        foreach ($departmentBudgetPlanningDetails as $departmentBudgetPlanningDetail) {
            $budgetDelegateAccessDetails = $departmentBudgetPlanningDetail->budgetDelegateAccessDetails->filter(function ($record) use ($todayStr) {
                $submissionDate = $record->submission_time ? \Carbon\Carbon::parse($record->submission_time)->toDateString() : null;
                return $submissionDate === $todayStr;
            });
            if ($budgetDelegateAccessDetails->count() > 0) {
                foreach ($budgetDelegateAccessDetails as $budgetDelegateAccessDetail) {
                    $delegatee = $budgetDelegateAccessDetail->delegatee;
                    if (!$delegatee || !$delegatee->employee) {
                        continue;
                    }
                    $employee = $delegatee->employee;
                    $placeholders = [
                        'RecipientName' => $employee->empName . ' (' . $employee->empID . ')',
                        'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
                        'BudgetYear' => date('d/m/Y', strtotime($departmentBudgetYear->bigginingDate)) . ' - ' . date('d/m/Y', strtotime($departmentBudgetYear->endingDate)),
                    ];
                    $notificationToUse = $budgetNotifications;
                    if ($revision) {
                        $placeholders['RevisionDeadline'] = date('d/m/Y', strtotime($revision->newSubmissionDate)) ?? 'N/A';
                        $notificationToUse = BudgetNotification::where('slug', 'revision-deadline-warning')->first() ?: $budgetNotifications;
                    }
                    $subjectTemplate = $notificationToUse->subject;
                    $bodyTemplate = $notificationToUse->body;
                    $emails[] = [
                        'empEmail' => $employee->empEmail,
                        'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
                        'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false),
                        'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true),
                        'empSystemID' => $employee->employeeSystemID,
                        'docSystemID' => 133,
                        'docSystemCode' => $departmentBudgetPlanningID,
                    ];
                }
            }
        }


        if (!empty($emails)) {
            \Email::sendEmail($emails);
        }
   }

   private function sendEmailToHOD($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company','revisions')->find($departmentBudgetPlanningID);
        $revision = $budgetPlanning->revisions->where('revisionStatus', 1)->first();
        $departmentBudgetYear = CompanyFinanceYear::find($departmentBudgetPlanning->yearID);

        $reminderTimeHours = $this->reminderTime !== null ? (int) $this->reminderTime : 48;
        if(empty($revision)) {
            $placeholders = [
                'RecipientName' =>  $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
                'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
                'BudgetYear' => date('d/m/Y', strtotime($departmentBudgetYear->bigginingDate)).' - '.date('d/m/Y', strtotime($departmentBudgetYear->endingDate)),
                'ReminderTime' => $reminderTimeHours,
                'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
            ];
        } else {
            $placeholders = [
                'RecipientName' =>  $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
                'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
                'RevisionDeadline' => date('d/m/Y', strtotime($revision->newSubmissionDate)) ?? 'N/A',
                'BudgetYear' => date('d/m/Y', strtotime($departmentBudgetYear->bigginingDate)).' - '.date('d/m/Y', strtotime($departmentBudgetYear->endingDate)),
                'ReminderTime' => $reminderTimeHours,
                'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
            ];

            $budgetNotifications = BudgetNotification::where('slug', 'revision-deadline-warning')->first();
        }


        $subjectTemplate = $budgetNotifications->subject;
        $bodyTemplate = $budgetNotifications->body;
        // Support both {{ReminderTime}} placeholder and legacy "48 hours" text
        $subjectTemplate = str_replace('48 Hours', $reminderTimeHours . ' Hours', $subjectTemplate);
        $bodyTemplate = str_replace('48 hours', $reminderTimeHours . ' hours', $bodyTemplate);

        $emails[] = array(
            'empEmail' => $departmentBudgetPlanning->department->hod->employee->empEmail,
            'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true), // Body needs line breaks
            'empSystemID' => $departmentBudgetPlanning->department->hod->employee->employeeSystemID,
            'docSystemID' => 133,
            'docSystemCode' => $departmentBudgetPlanningID,
        );


        \Email::sendEmail($emails);
   }

   private function sendEmailToDelegatee($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('budgetPlanningDetails.budgetDelegateAccessDetails','revisions')->find($departmentBudgetPlanningID);
        $revision = $budgetPlanning->revisions->where('revisionStatus', 1)->first();

        $today = Carbon::today();
        $twoDaysFromNow = $today->copy()->addDays(2);

        $departmentBudgetPlanningDetails = $budgetPlanning->budgetPlanningDetails;
        $departmentBudgetYear = CompanyFinanceYear::find($departmentBudgetPlanning->yearID);

        foreach($departmentBudgetPlanningDetails as $departmentBudgetPlanningDetail) {
            if($departmentBudgetPlanningDetail->budgetDelegateAccessDetails->count() > 0) {
                $budgetDelegateAccessDetails = $departmentBudgetPlanningDetail->budgetDelegateAccessDetails->where('submission_time', '<=', $twoDaysFromNow);
                if($budgetDelegateAccessDetails->count() > 0) {
                    foreach($budgetDelegateAccessDetails as $budgetDelegateAccessDetail) {
                        $delegatee = $budgetDelegateAccessDetail->delegatee;
                       
                        $employee = Employee::find($delegatee->employeeSystemID);

                        $reminderTimeHours = $this->reminderTime !== null ? (int) $this->reminderTime : 48;
                        if(empty($revision)) {
                            $placeholders = [
                                'RecipientName' => $employee->empName.' ('.$employee->empID.')',
                                'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
                                'BudgetYear' => date('d/m/Y', strtotime($departmentBudgetYear->bigginingDate)).' - '.date('d/m/Y', strtotime($departmentBudgetYear->endingDate)),
                                'ReminderTime' => $reminderTimeHours,
                                'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
                            ];
                        }else {
                            $placeholders = [
                                'RecipientName' => $employee->empName.' ('.$employee->empID.')',
                                'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
                                'BudgetYear' => date('d/m/Y', strtotime($departmentBudgetYear->bigginingDate)).' - '.date('d/m/Y', strtotime($departmentBudgetYear->endingDate)),
                                'RevisionDeadline' => date('d/m/Y', strtotime($revision->newSubmissionDate)) ?? 'N/A',
                                'ReminderTime' => $reminderTimeHours,
                                'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
                            ];
                            $budgetNotifications = BudgetNotification::where('slug', 'revision-deadline-warning')->first();
                        }

                        $subjectTemplate = $budgetNotifications->subject;
                        $bodyTemplate = $budgetNotifications->body;
                        $bodyTemplate = str_replace('48 hours', $reminderTimeHours . ' hours', $bodyTemplate);

                        $emails[] = array(
                            'empEmail' => $employee->empEmail,
                            'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
                            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
                            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true), // Body needs line breaks
                            'empSystemID' => $employee->employeeSystemID,
                            'docSystemID' => 133,
                            'docSystemCode' => $departmentBudgetPlanningID
                        );

                    }

                    \Email::sendEmail($emails);
                }
            }

        }
   }

   private function sendDelegateeSubmissionEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company','revisions')->find($departmentBudgetPlanningID);
        $revision = $budgetPlanning->revisions->where('revisionStatus', 1)->first();
        $delegatee = Employee::find($this->delegateID);

        $linkUrl = str_replace('approval/erp', 'budget-planning/planning', $this->baseurl);
        if(empty($revision)) {
            $placeholders = [
            'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
            'HODName' => $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
            'DelegateeName' => $delegatee->empName.' ('.$delegatee->empID.')',
            '[Budget Review Dashboard]' => '<a href="' . $linkUrl . '" style="color: #007bff; text-decoration: underline;">Click here to view the budget planning</a>'
        ];
        }else {
            $placeholders = [
                'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
                'HODName' => $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
                'DelegateeName' => $delegatee->empName.' ('.$delegatee->empID.')',
                'ResubmissionDate' => date('d/m/Y', strtotime($revision->newSubmissionDate)) ?? 'N/A',
                '[Budget Review Dashboard]' => '<a href="' . $linkUrl . '" style="color: #007bff; text-decoration: underline;">Click here to view the budget planning</a>'
            ];

            $budgetNotifications = BudgetNotification::where('slug', 'revision-resubmission')->first();
        }

        $subjectTemplate = $budgetNotifications->subject;
        $bodyTemplate = $budgetNotifications->body;

        $emails[] = array(
            'empEmail' => $budgetPlanning->department->hod->employee->empEmail,
            'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true), // Body needs line breaks
            'empSystemID' => $budgetPlanning->department->hod->employee->employeeSystemID,
            'docSystemID' => 133,
            'docSystemCode' => $departmentBudgetPlanningID
        );

        \Email::sendEmail($emails);
   }

   private function sendFinalSubmissionToFinanceEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company')->find($departmentBudgetPlanningID);
        
        $placeholders = [
            'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
            'HODName' => $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
            'SubmissionDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A'
        ];

        $subjectTemplate = $budgetNotifications->subject;
        $bodyTemplate = $budgetNotifications->body;

        // Get all finance users with their employee details eager loaded
        $financeUsers = CompanyDepartmentEmployee::with('employee')
                        ->whereHas('department', function ($query) use ($budgetPlanning) {
                            $query->where('companySystemID', $budgetPlanning->masterBudgetPlannings->companySystemID)
                                    ->where('isFinance', 1)->where('isActive', 1);
                        })
                        ->where('isActive', 1)
                        ->get();

        foreach($financeUsers as $financeUser) {
            // Check if employee exists and has an email
            if (!$financeUser->employee || !$financeUser->employee->empEmail) {
                continue;
            }

            $emails[] = array(
                'empEmail' => $financeUser->employee->empEmail,
                'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
                'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
                'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true), // Body needs line breaks
                'empSystemID' => $financeUser->employee->employeeSystemID,
                'docSystemID' => 133,
                'docSystemCode' => $departmentBudgetPlanningID
            );
        }

        \Email::sendEmail($emails);
   }

   private function sendFinanceRejectsForRevisionEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company','revisions')->find($departmentBudgetPlanningID);
        
        $placeholders = [
            'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
            'HODName' => $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
            'RevisionDeadline' => $budgetPlanning->revisions->where('revisionStatus', 1)->first()->newSubmissionDate,
            'FinanceComments' => $budgetPlanning->revisions->where('revisionStatus', 1)->first()->reviewComments
        ];


        $subjectTemplate = $budgetNotifications->subject;
        $bodyTemplate = $budgetNotifications->body;

        $emails[] = array(
            'empEmail' => $departmentBudgetPlanning->department->hod->employee->empEmail,
            'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true), // Body needs line breaks
            'empSystemID' => $departmentBudgetPlanning->department->hod->employee->employeeSystemID,
            'docSystemID' => 133,
            'docSystemCode' => $departmentBudgetPlanningID
        );


        \Email::sendEmail($emails);
   }


   private function sendTimeExtensionRequestSubmittedEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company','timeExtensionRequests')->find($departmentBudgetPlanningID);
        $timeExtenionRequest = $budgetPlanning->timeExtensionRequests->where('status', 1)->first();
        // Get all department users with their employee details eager loaded
        $financeUsers = CompanyDepartmentEmployee::with('employee')
                        ->whereHas('department', function ($query) use ($budgetPlanning) {
                        $query->where('isFinance', 1)->where('isActive', 1)->where('companySystemID', $budgetPlanning->masterBudgetPlannings->companySystemID);
                        })
                        ->where('isActive', 1)
                        ->get();

        $placeholders = [
            'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
            'RequestedDeadline' => date('d/m/Y', strtotime($timeExtenionRequest->date_of_request)) ?? 'N/A',
            'ExtensionReason' => $timeExtenionRequest->reason_for_extension,
        ];

        $subjectTemplate = $budgetNotifications->subject;
        $bodyTemplate = $budgetNotifications->body;

        foreach($financeUsers as $financeUser) {
            // Check if employee exists and has an email
            if (!$financeUser->employee || !$financeUser->employee->empEmail) {
                continue;
            }

            $emails[] = array(
                'empEmail' => $financeUser->employee->empEmail,
                'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
                'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
                'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true), // Body needs line breaks
                'empSystemID' => $financeUser->employee->employeeSystemID,
                'docSystemID' => 133,
                'docSystemCode' => $departmentBudgetPlanningID
            );
    
        }

        \Email::sendEmail($emails);
   }


   public function sendTimeExtentionApprovedEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company','timeExtensionRequests')->find($departmentBudgetPlanningID);

        $timeExtenionRequest = $budgetPlanning->timeExtensionRequests->where('status', 2)->first();


        $placeholders = [
            'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
            'ApprovedDeadline' => date('d/m/Y', strtotime($timeExtenionRequest->new_time)) ?? date('d/m/Y', strtotime($timeExtenionRequest->current_submission_date)),
        ];

        
        $subjectTemplate = $budgetNotifications->subject;
        $bodyTemplate = $budgetNotifications->body;

        $emails[] = array(
            'empEmail' => $departmentBudgetPlanning->department->hod->employee->empEmail,
            'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true), // Body needs line breaks
            'empSystemID' => $departmentBudgetPlanning->department->hod->employee->employeeSystemID,
            'docSystemID' => 133,
            'docSystemCode' => $departmentBudgetPlanningID
        );



        $delegateAccessList = BudgetDelegateAccessRecord::whereHas('budgetPlanningDetail', function ($query) use ($budgetPlanning) {
            $query->where('department_planning_id', $budgetPlanning->id);
        })
            ->with(['budgetPlanningDetail', 'delegatee.employee'])
            ->get();

        foreach ($delegateAccessList as $delegateAccess) {
            if (!$delegateAccess->delegatee || !$delegateAccess->delegatee->employee || !$delegateAccess->delegatee->employee->empEmail) {
                continue;
            }
            $emails[] = array(
                'empEmail' => $delegateAccess->delegatee->employee->empEmail,
                'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
                'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false),
                'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true),
                'empSystemID' => $delegateAccess->delegatee->employee->employeeSystemID,
                'docSystemID' => 133,
                'docSystemCode' => $departmentBudgetPlanningID
            );
        }


        \Email::sendEmail($emails);
   }

   private function sendTimeExtensionRequestCancelledEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company','timeExtensionRequests')->find($departmentBudgetPlanningID);
        $timeExtenionRequest = $budgetPlanning->timeExtensionRequests->whereIn('status', [3, 4])->first();
        $placeholders = [
            'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
            'OriginalDeadline' => date('d/m/Y', strtotime($timeExtenionRequest->current_submission_date)),
            'FinanceComments' => $timeExtenionRequest->review_comments,
            'HODName' => $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
        ];

        $subjectTemplate = $budgetNotifications->subject;
        $bodyTemplate = $budgetNotifications->body;
        $emails[] = array(
            'empEmail' => $departmentBudgetPlanning->department->hod->employee->empEmail,
            'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true), // Body needs line breaks
            'empSystemID' => $departmentBudgetPlanning->department->hod->employee->employeeSystemID,
            'docSystemID' => 133,
            'docSystemCode' => $departmentBudgetPlanningID
        );

        \Email::sendEmail($emails);
        
   }

}