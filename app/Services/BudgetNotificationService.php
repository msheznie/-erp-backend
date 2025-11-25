<?php

namespace App\Services;

use App\Models\BudgetNotification;
use App\Models\BudgetNotificationDetail;
use App\Models\BudgetNotificationRecipient;
use App\Models\CompanyDepartment;
use App\Models\DepartmentBudgetPlanning;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyDepartmentEmployee;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class BudgetNotificationService
{

    private $companySystemID;
    private $scenario;
    private $delegateID;
    
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
   public function sendNotification($departmentBudgetPlanningID, $scenario, $companySystemID, $delegateID = null)
   {

       $this->companySystemID = $companySystemID;
       $this->scenario = $scenario;
       $this->delegateID = $delegateID;


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
                $this->sendDelegationConfirmationEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
                break;
            case 'deadline-warning':
                $this->sendDeadlineWarningEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
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

        $placeholders = [
            'HODName' => $hod->empName.' ('.$hod->empID.')',
            'BudgetYear' => date('d/m/Y', strtotime($departmentBudgetYear->bigginingDate)).' - '.date('d/m/Y', strtotime($departmentBudgetYear->endingDate)),
            'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
            'link' => $departmentBudgetPlanning->link ?? 'N/A'
        ];

        $emails = array(
            'empEmail' => $hod->empEmail,
            'companySystemID' => $departmentBudgetPlanning->masterBudgetPlannings->companySystemID,
            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true) // Body needs line breaks
        );

        \Email::sendEmailErp($emails);

   }

   private function sendTaskDelegattionEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
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

        $emails = array(
            'empEmail' => $delegatee->employee->empEmail,
            'companySystemID' => $departmentBudgetPlanning->masterBudgetPlannings->companySystemID,
            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true) // Body needs line breaks
        );


        \Email::sendEmailErp($emails);

   }

   private function sendDelegationConfirmationEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company')->find($departmentBudgetPlanningID);
        $delegatee = Employee::find($this->delegateID);

        $placeholders = [
            'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
            'HODName' => $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
            'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
            'DelegateeName' => $delegatee->empName.' ('.$delegatee->empID.')',
        ];

        $subjectTemplate = $budgetNotifications->subject;
        $bodyTemplate = $budgetNotifications->body;

        $emails = array(
            'empEmail' => $delegatee->empEmail,
            'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true) // Body needs line breaks
        );


        \Email::sendEmailErp($emails);

   }

   private function sendDeadlineWarningEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $this->sendEmailToHOD($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
        $this->sendEmailToDelegatee($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
       

   }

   private function sendEmailToHOD($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company')->find($departmentBudgetPlanningID);
        $placeholders = [
            'RecipientName' =>  $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
            'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
        ];

        $subjectTemplate = $budgetNotifications->subject;
        $bodyTemplate = $budgetNotifications->body;

        $emails = array(
            'empEmail' => $departmentBudgetPlanning->department->hod->employee->empEmail,
            'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true) // Body needs line breaks
        );

        \Email::sendEmailErp($emails);
   }

   private function sendEmailToDelegatee($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('budgetPlanningDetails.budgetDelegateAccessDetails')->find($departmentBudgetPlanningID);

        $today = Carbon::today();
        $twoDaysFromNow = $today->copy()->addDays(2);

        $departmentBudgetPlanningDetails = $budgetPlanning->budgetPlanningDetails;
        foreach($departmentBudgetPlanningDetails as $departmentBudgetPlanningDetail) {

            if($departmentBudgetPlanningDetail->budgetDelegateAccessDetails->count() > 0) {
                $budgetDelegateAccessDetails = $departmentBudgetPlanningDetail->budgetDelegateAccessDetails->where('submission_time', '>', $today)->where('submission_time', '<=', $twoDaysFromNow);
                if($budgetDelegateAccessDetails->count() > 0) {
                    foreach($budgetDelegateAccessDetails as $budgetDelegateAccessDetail) {
                        $delegatee = $budgetDelegateAccessDetail->delegatee;
                       
                        $employee = Employee::find($delegatee->employeeSystemID);
                        $placeholders = [
                            'RecipientName' => $employee->empName.' ('.$employee->empID.')',
                            'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
                        ];

                        $subjectTemplate = $budgetNotifications->subject;
                        $bodyTemplate = $budgetNotifications->body;

                        $emails = array(
                            'empEmail' => $employee->empEmail,
                            'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
                            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
                            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true) // Body needs line breaks
                        );

                        \Email::sendEmailErp($emails);
                    }
                }
            }

        }


   }
}

