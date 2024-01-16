<?php


namespace App\helper;


use App\Models\ProcumentOrder;
use App\Models\NotificationUser;
use App\Models\ApprovalLevel;
use App\Models\Employee;
use App\helper\NotificationService;
use Illuminate\Support\Facades\Log;

class SendEmailForDocument
{
    public static function approvedDocument($input)
    {
        switch ($input["documentSystemID"]) { 
            case 2: 
                // procument order document
                $approvalLevel = ApprovalLevel::find($input["approvalLevelID"]);
                // check final level approval
                if ($approvalLevel && ($approvalLevel->noOfLevels == $input["rollLevelOrder"])) {
                    $procument_order_master = ProcumentOrder::find($input['purchaseOrderID']);
                    if($procument_order_master && isset($procument_order_master->companySystemID) && $procument_order_master->logisticsAvailable == -1) {
                        $notificationScenario = NotificationService::getCompanyScenarioConfigurationForCompany(13,$procument_order_master->companySystemID);
                        if($notificationScenario && $notificationScenario->isActive) {
                            $notification_users = NotificationUser::where('companyScenarionID',$notificationScenario->id)->get();
                            if($notification_users) {
                                foreach($notification_users as $notification_user) {
                                    switch($notification_user->applicableCategoryID) {
                                        case 1 :
                                            // check notification user id is equal to approved user id
                                                Log::info('Notified user id is equal to approved user id log'.$notification_user->empID.'approved user'. $procument_order_master->approvedByUserSystemID);
                                                self::sendEmailToEmployeeCategory($procument_order_master,$notification_user);
                                        break;
                                    }
                                }
                                return ['success' => true, 'message' => 'Email Send'];
                            }else {
                                return ['success' => false, 'message' => 'Notification Users not found'];
                            }
             
                        }else {
                            return ['success' => false, 'message' => 'Notification Scenario is not active'];
                        }
                    }else {
                        return ['success' => false, 'message' => 'Company not found for this purchase order'];
                    }
                }
                break;
        }
        
    }

    public static function sendEmailToEmployeeCategory($purchaseOrder,$notification_user) {
        
            $employee = Employee::find($notification_user->empID);
            if($employee && ($employee->discharegedYN == 0) && ($employee->ActivationFlag == -1) && ($employee->empLoginActive == 1) && ($employee->empActive == 1)) {
                $body = $purchaseOrder->purchaseOrderCode . " is marked as logistics available. Details as follows. <br> PO Code : " . $purchaseOrder->purchaseOrderCode ."<br> Total Amount : " . number_format($purchaseOrder->poTotalLocalCurrency,$purchaseOrder->currency->DecimalPlaces) .' '.$purchaseOrder->currency->CurrencyCode."<br> Supplier Name : ".$purchaseOrder->supplierName."<br> Supplier Address : ".$purchaseOrder->supplierAddress."<br> Thanks,";
                $temp['employeeSystemID'] = $employee->employeeSystemID;
                $temp['documentSystemCode'] = $purchaseOrder->documentSystemID;
                $dataEmail['docSystemCode'] = $purchaseOrder->documentSystemID;
                $temp['documentCode'] = $purchaseOrder->purchaseOrderCode;
                $dataEmail['docSystemID'] = $purchaseOrder->documentSystemID;
                $dataEmail['empSystemID'] = $employee->employeeSystemID;
                $dataEmail['empEmail'] = $employee->empEmail;
                $dataEmail['companySystemID'] = $purchaseOrder->companySystemID;
                $dataEmail['alertMessage']  = $purchaseOrder->purchaseOrderCode . " is marked as logistics available ";
                $dataEmail['emailAlertMessage'] = $body;
                Log::info('Email stared to send to PO',$dataEmail);
                $sendEmail = \Email::sendEmailErp($dataEmail);
                Log::info('Email end here');
            }

            Log::info('sendEmailToEmployeeCategory function called - end');
    }
}
