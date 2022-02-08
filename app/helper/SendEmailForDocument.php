<?php


namespace App\helper;


use App\Models\ProcumentOrder;
use App\Models\NotificationUser;
use App\Models\ApprovalLevel;
use App\Models\Employee;
use App\helper\NotificationService;

class SendEmailForDocument
{
    public static function approvedDocument($input)
    {
        switch ($input["documentSystemID"]) { 
            case 2: 
                // procument order document
                $approvalLevel = ApprovalLevel::find($input["approvalLevelID"]);
                // check final level approval
                if ($approvalLevel->noOfLevels == $input["rollLevelOrder"]) {
                    $procument_order_master = ProcumentOrder::find($input['purchaseOrderID']);

                    if(isset($procument_order_master->companySystemID) && $procument_order_master->logisticsAvailable == -1) {
                        $notificationScenario = NotificationService::getCompanyScenarioConfigurationForCompany(13,$procument_order_master->companySystemID);
                        if($notificationScenario->isActive) {
                            $notification_users = NotificationUser::where('companyScenarionID',$procument_order_master->companySystemID)->get();
                            
                            foreach($notification_users as $notification_user) {
                                switch($notification_user->applicableCategoryID) {
                                    case 1 : self::sendEmailToEmployeeCategory($procument_order_master);
                                    break;
                                }
                            }
                            return ['success' => true, 'message' => 'Email Send'];
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

    public static function sendEmailToEmployeeCategory($purchaseOrder) {
        $employee = Employee::find($purchaseOrder->approvedByUserSystemID);
        $body = $purchaseOrder->purchaseOrderCode . " is marked as logistics available. Details as follows. <br> PO Code : " . $purchaseOrder->purchaseOrderCode ."<br> Total Amount : " . $purchaseOrder->poTotalLocalCurrency ."<br> Supplier Name : ".$purchaseOrder->supplierName."<br> Supplier Address : ".$purchaseOrder->supplierAddress."<br> Thanks,";
        $temp['employeeSystemID'] = $employee->employeeSystemID;
		$temp['documentSystemCode'] = $purchaseOrder->documentSystemID;
		$temp['documentCode'] = $purchaseOrder->purchaseOrderCode;
        $dataEmail['docSystemID'] = $purchaseOrder->documentSystemID;
        $dataEmail['empSystemID'] = $employee->employeeSystemID;
        $dataEmail['empEmail'] = $employee->empEmail;
        $dataEmail['companySystemID'] = $purchaseOrder->companySystemID;
        $dataEmail['alertMessage']  = $purchaseOrder->purchaseOrderCode . " is marked as logistics available ";

        $dataEmail['emailAlertMessage'] = $body;
        $sendEmail = \Email::sendEmailErp($dataEmail);
    }
}
