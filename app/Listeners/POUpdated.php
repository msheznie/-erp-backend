<?php

namespace App\Listeners;

use App\Models\Alert;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class POUpdated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $order = $event->order;
        Log::useFiles(storage_path() . '/logs/po_updated.log');
        Log::info('Successfully start  po updated' . date('H:i:s'));
        if (!empty($order)) {
            $original = $order->getOriginal();
            Log::info($order->serviceLineSystemID . ' to ' . $original['serviceLineSystemID']);
            if ( ($order->serviceLineSystemID != $original['serviceLineSystemID']) && ($order->poConfirmedYN == 1 && $original['poConfirmedYN'] == 1)) {
                $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!" . "<br>This is an auto generated email. Please do not reply to this email because we are not" . "monitoring this inbox.</font>";
                $email_id = 'm.zahlan@pbs-int.net';
                $empName = 'Admin';
                $employeeSystemID = 11;
                $empID = '8888';
                $dataEmail = array();
                $dataEmail['empName'] = $empName;
                $dataEmail['empEmail'] = $email_id;
                $dataEmail['empSystemID'] = $employeeSystemID;
                $dataEmail['empID'] = $empID;
                $dataEmail['companySystemID'] = $order['companySystemID'];
                $dataEmail['companyID'] = $order['companyID'];
                $dataEmail['docID'] = $order->documentID;
                $dataEmail['docSystemID'] = $order["documentSystemID"];
                $dataEmail['docSystemCode'] = $order['purchaseOrderID'];
                $dataEmail['docApprovedYN'] = 0;
                $dataEmail['docCode'] = $order['purchaseOrderCode'];
                $dataEmail['ccEmailID'] = $email_id;
                $temp = "Segment changed for " . $order['purchaseOrderCode'] . "<p> from ". $original['serviceLine'] ." to ". $order->serviceLine .  $footer;
                $dataEmail['isEmailSend'] = 0;
                $dataEmail['attachmentFileName'] = null;
                $dataEmail['alertMessage'] = trans('email.segment_changed_for', ['purchaseOrderCode' => $order['purchaseOrderCode']]);
                $dataEmail['emailAlertMessage'] = $temp;
                $sendEmail = \Email::sendEmailErp($dataEmail);
                Log::info('Email array:');
                Log::info($dataEmail);
            }
        }
        Log::info('Successfully end  po updated ' . date('H:i:s'));
    }
}
