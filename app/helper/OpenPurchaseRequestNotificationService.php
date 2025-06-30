<?php

namespace App\helper;

use App\Models\Employee;
use App\Models\PurchaseRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OpenPurchaseRequestNotificationService
{
    private $companyID;
    private $notificationDaySetup;

    public function __construct($companyID, $notificationDaySetup)
    {
        $this->companyID = $companyID;
        $this->notificationDaySetup = $notificationDaySetup;
    }

    public function proceed()
    {
        $log_file = NotificationService::log_file();
        Log::useFiles($log_file);

        // Check if today is the last day of the month
        if (!$this->isLastDayOfMonth()) {
            // Log::info("Today is not the last day of the month. Skipping Open PR notification for company ID: {$this->companyID}");
            return;
        }

        // Log::info("Processing Open Purchase Request notification for company ID: {$this->companyID}");

        // Get open purchase requests
        $openPRs = $this->getOpenPurchaseRequests();

        if (count($openPRs) == 0) {
            // Log::info("No open Purchase Requests found for company ID: {$this->companyID}");
            return;
        }

        // Log::info("Found " . count($openPRs) . " open Purchase Requests for company ID: {$this->companyID}");

        // Get notification users
        $notificationUserSettings = NotificationService::notificationUserSettings($this->notificationDaySetup->id);
        
        if (count($notificationUserSettings['email']) == 0) {
            // Log::info("No email notification users configured for Open PR notification");
            return;
        }

                // Send emails to configured users
        foreach ($notificationUserSettings['email'] as $notificationUserVal) {
            foreach ($notificationUserVal as $userInfo) {
                // Get employee email and name from the processed notification settings
                $empEmail = $userInfo['empEmail'] ?? null;
                $empName = $userInfo['empName'] ?? null;

                if (empty($empEmail)) {
                    Log::warning("Email is missing for employee {$empName}");
                    continue;
                }

                // Additional validation - fetch employee to ensure they are still active
                $employee = Employee::selectRaw('empEmail,empName,empFullName,employeeSystemID')
                    ->where('isEmailVerified', 1)
                    ->where('empActive', 1)
                    ->where('discharegedYN', 0)
                    ->where('empEmail', $empEmail)
                    ->first();

                if (empty($employee)) {
                    Log::error("Employee not found or not valid for Open PR notification. Employee email: {$empEmail}");
                    continue;
                }

                $emailContent = $this->getEmailContent($openPRs, $empName);
                $subject = 'Open Purchase Requests - Month End Report';
                
                $sendEmail = NotificationService::emailNotification(
                    $this->companyID, 
                    $subject, 
                    $empEmail, 
                    $emailContent
                );

                if (!$sendEmail["success"]) {
                    Log::error("Failed to send Open PR notification email: " . $sendEmail["message"]);
                } 
            }
        }
    }

    private function isLastDayOfMonth()
    {
        $today = Carbon::now();
        $lastDayOfMonth = $today->copy()->endOfMonth();
        
        return $today->isSameDay($lastDayOfMonth);
    }

    private function getOpenPurchaseRequests()
    {
        // Get open PRs using the same logic as the ReportOpenRequestsComponent
        $openPRs = PurchaseRequest::where('companySystemID', $this->companyID)
            ->where('approved', -1) // Approved
            ->where('manuallyClosed', 0) // Not manually closed
            ->where('cancelledYN', 0) // Not cancelled
            ->where('prClosedYN', 0) // PR not closed
            ->with([
                'created_by', 
                'priority_pdf', 
                'location', 
                'segment',
                'details' => function ($query) {
                    $query->with(['podetail' => function ($q) {
                        $q->with(['order']);
                    }]);
                }
            ])
            ->orderBy('purchaseRequestID', 'desc')
            ->get();

        // Filter out PRs that have no remaining balance
        $filteredPRs = $openPRs->filter(function ($pr) {
            $hasOpenBalance = false;
            
            foreach ($pr->details as $detail) {
                $poQtySum = 0;
                
                if (!empty($detail->podetail)) {
                    foreach ($detail->podetail as $poDetail) {
                        if (
                            !empty($poDetail->order) &&
                            isset($poDetail->order->approved) &&
                            $poDetail->order->approved == -1
                        ) {
                            $poQtySum += floatval($poDetail->noQty);
                        }
                    }
                }
                
                $balance = floatval($detail->quantityRequested) - $poQtySum;
                if ($balance > 0) {
                    $hasOpenBalance = true;
                    break;
                }
            }
            
            return $hasOpenBalance;
        });

        return $filteredPRs->values();
    }

    private function getEmailContent($openPRs, $recipientName)
    {
        $currentMonth = Carbon::now()->format('F Y');
        
        $emailContent = "<p>Hi {$recipientName},</p>";
        $emailContent .= "<p>Please be informed and find below the list of open Purchase Requests (PRs) as of this month-end.</p>";
        $emailContent .= "<p><strong>Purchase Requests:</strong></p>";
        
        $emailContent .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
        $emailContent .= '<thead>';
        $emailContent .= '<tr style="background-color: #f2f2f2;">';
        $emailContent .= '<th>PR Number</th>';
        $emailContent .= '<th>PR Requested Date</th>';
        $emailContent .= '<th>Priority</th>';
        $emailContent .= '<th>Created By</th>';
        $emailContent .= '</tr>';
        $emailContent .= '</thead>';
        $emailContent .= '<tbody>';
        
        foreach ($openPRs as $pr) {
            $prDate = $pr->createdDateTime ? Carbon::parse($pr->createdDateTime)->format('Y-m-d') : 'N/A';
            $priority = $pr->priority_pdf ? $pr->priority_pdf->priorityDescription : 'N/A';
            $createdBy = $pr->created_by ? $pr->created_by->empName : 'N/A';
            
            $emailContent .= '<tr>';
            $emailContent .= '<td>' . $pr->purchaseRequestCode . '</td>';
            $emailContent .= '<td>' . $prDate . '</td>';
            $emailContent .= '<td>' . $priority . '</td>';
            $emailContent .= '<td>' . $createdBy . '</td>';
            $emailContent .= '</tr>';
        }
        
        $emailContent .= '</tbody>';
        $emailContent .= '</table>';
        
        $emailContent .= "<br/><p>Best regards,<br/>";
        $emailContent .= "System Administrator,<br/>";
        $emailContent .= "OSOS Training.</p>";
        
        return $emailContent;
    }
} 
