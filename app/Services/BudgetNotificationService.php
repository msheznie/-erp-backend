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
use App\helper\Helper;

use App\helper\email as Email;
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
            case 'time-extension-request-submitted':
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

        $baseurl = Helper::checkDomai();
        $parsedUrl = parse_url($baseurl);
        $domain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        $linkUrl = $domain . '/#/budget-planning/planning';
        
        $placeholders = [
            'HODName' => $hod->empName.' ('.$hod->empID.')',
            'BudgetYear' => date('d/m/Y', strtotime($departmentBudgetYear->bigginingDate)).' - '.date('d/m/Y', strtotime($departmentBudgetYear->endingDate)),
            'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
            'link' => '<a href="' . $linkUrl . '" style="color: #007bff; text-decoration: underline;">' . $linkUrl . '</a>'
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

        Email::sendEmail($emails);

   }

   private function sendTaskDelegattionEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company','revisions')->find($departmentBudgetPlanningID);

        $revision = $budgetPlanning->revisions->where('revisionStatus', 1)->first();

        $delegatee = CompanyDepartmentEmployee::with('employee')->find($this->delegateID);

        if(empty($revision)) {
            $placeholders = [
                'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
                'HODName' => $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
                'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
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


        Email::sendEmail($emails);

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


        Email::sendEmail($emails);

   }

   private function sendDeadlineWarningEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $this->sendEmailToHOD($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
        $this->sendEmailToDelegatee($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
       

   }

   private function sendSubmissionDeadlineReachedEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $this->sendEmailToHOD($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
        $this->sendEmailToDelegatee($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID);
   }

   private function sendEmailToHOD($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company','revisions')->find($departmentBudgetPlanningID);
        $revision = $budgetPlanning->revisions->where('revisionStatus', 1)->first();
        $departmentBudgetYear = CompanyFinanceYear::find($departmentBudgetPlanning->yearID);

        if(empty($revision)) {
            $placeholders = [
                'RecipientName' =>  $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
                'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
                'BudgetYear' => date('d/m/Y', strtotime($departmentBudgetYear->bigginingDate)).' - '.date('d/m/Y', strtotime($departmentBudgetYear->endingDate)),
            ];
        } else {
            $placeholders = [
                'RecipientName' =>  $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
                'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
                'RevisionDeadline' => date('d/m/Y', strtotime($revision->newSubmissionDate)) ?? 'N/A',
                'BudgetYear' => date('d/m/Y', strtotime($departmentBudgetYear->bigginingDate)).' - '.date('d/m/Y', strtotime($departmentBudgetYear->endingDate)),
            ];

            $budgetNotifications = BudgetNotification::where('slug', 'revision-deadline-warning')->first();
        }


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


        Email::sendEmail($emails);
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
                $budgetDelegateAccessDetails = $departmentBudgetPlanningDetail->budgetDelegateAccessDetails->where('submission_time', '>', $today)->where('submission_time', '<=', $twoDaysFromNow);
                if($budgetDelegateAccessDetails->count() > 0) {
                    foreach($budgetDelegateAccessDetails as $budgetDelegateAccessDetail) {
                        $delegatee = $budgetDelegateAccessDetail->delegatee;
                       
                        $employee = Employee::find($delegatee->employeeSystemID);

                        if(empty($revision)) {
                            $placeholders = [
                                'RecipientName' => $employee->empName.' ('.$employee->empID.')',
                                'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
                                'BudgetYear' => date('d/m/Y', strtotime($departmentBudgetYear->bigginingDate)).' - '.date('d/m/Y', strtotime($departmentBudgetYear->endingDate)),
                            ];
                        }else {
                            $placeholders = [
                                'RecipientName' => $employee->empName.' ('.$employee->empID.')',
                                'DeadlineDate' => date('d/m/Y', strtotime($departmentBudgetPlanning->submissionDate)) ?? 'N/A',
                                'BudgetYear' => date('d/m/Y', strtotime($departmentBudgetYear->bigginingDate)).' - '.date('d/m/Y', strtotime($departmentBudgetYear->endingDate)),
                                'RevisionDeadline' => date('d/m/Y', strtotime($revision->newSubmissionDate)) ?? 'N/A',
                            ];
                            $budgetNotifications = BudgetNotification::where('slug', 'revision-deadline-warning')->first();
                        }

                        $subjectTemplate = $budgetNotifications->subject;
                        $bodyTemplate = $budgetNotifications->body;

                        $emails[] = array(
                            'empEmail' => $employee->empEmail,
                            'companySystemID' => $budgetPlanning->masterBudgetPlannings->companySystemID,
                            'alertMessage' => $this->replacePlaceholders($subjectTemplate, $placeholders, false), // Subject doesn't need line breaks
                            'emailAlertMessage' => $this->replacePlaceholders($bodyTemplate, $placeholders, true), // Body needs line breaks
                            'empSystemID' => $employee->employeeSystemID,
                            'docSystemID' => 133,
                            'docSystemCode' => $departmentBudgetPlanningID
                        );

                        Email::sendEmail($emails);
                    }
                }
            }

        }
   }

   private function sendDelegateeSubmissionEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company','revisions')->find($departmentBudgetPlanningID);
        $revision = $budgetPlanning->revisions->where('revisionStatus', 1)->first();
        $delegatee = Employee::find($this->delegateID);

        if(empty($revision)) {
            $placeholders = [
            'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
            'HODName' => $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
            'DelegateeName' => $delegatee->empName.' ('.$delegatee->empID.')',
            'Budget Review Dashboard' => 'N/A'
        ];
        }else {
            $placeholders = [
                'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
                'HODName' => $departmentBudgetPlanning->department->hod->employee->empName.' ('.$departmentBudgetPlanning->department->hod->employee->empID.')',
                'DelegateeName' => $delegatee->empName.' ('.$delegatee->empID.')',
                'ResubmissionDate' => date('d/m/Y', strtotime($revision->newSubmissionDate)) ?? 'N/A',
                'Budget Review Dashboard' => 'N/A'
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

        Email::sendEmail($emails);
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
                        ->whereHas('department', function ($query) {
                            $query->where('isFinance', 1)->where('isActive', 1);
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
    
            Email::sendEmail($emails);
        }

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


        Email::sendEmail($emails);
   }


   private function sendTimeExtensionRequestSubmittedEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company','timeExtensionRequests')->find($departmentBudgetPlanningID);
        
        $timeExtenionRequest = $budgetPlanning->timeExtensionRequests->where('status', 1)->first();

        // Get all department users with their employee details eager loaded
        $financeUsers = CompanyDepartmentEmployee::with('employee')
                        ->whereHas('department', function ($query) {
                        $query->where('isFinance', 1)->where('isActive', 1);
                        })
                        ->where('isActive', 1)
                        ->get();

        $placeholders = [
            'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
            'RequestedDeadline' => date('d/m/Y', strtotime($timeExtenionRequest->date_of_request)) ?? 'N/A',
            'ExtensionReason' => $timeExtenionRequest->reason_for_extension,
        ];

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
    
            Email::sendEmail($emails);
        }
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


        Email::sendEmail($emails);
   }

   private function sendTimeExtensionRequestCancelledEmail($budgetNotifications,$departmentBudgetPlanning,$departmentBudgetPlanningID)
   {
        $budgetPlanning = DepartmentBudgetPlanning::with('department.hod.employee','masterBudgetPlannings.company','timeExtensionRequests')->find($departmentBudgetPlanningID);
        $timeExtenionRequest = $budgetPlanning->timeExtensionRequests->whereIn('status', [3, 4])->first();
        
        $placeholders = [
            'DepartmentName' => $departmentBudgetPlanning->department->departmentCode.' - '.$departmentBudgetPlanning->department->departmentDescription,
            'OriginalDeadline' => date('d/m/Y', strtotime($timeExtenionRequest->current_submission_date)),
            'FinanceComments' => $timeExtenionRequest->review_comments,
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

        Email::sendEmail($emails);
        
   }

}