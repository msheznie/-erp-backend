<?php
namespace App\Services;
use App\Models\DocumentApproved;
use App\Models\GeneralLedger;
use App\Traits\AuditTrial;
use App\Models\DeliveryOrder;
use App\Models\CustomerInvoiceDirect;
use App\Models\ItemIssueMaster;
use App\Models\StockTransfer;
use App\Models\StockAdjustment;
use App\Models\StockCount;
use App\Models\ErpItemLedger;

class DeliveryOrderServices
{
    public function __construct()
    {
        
    }
    
    public function amendDeliveryOrder($orderId,$masterData,$input,$employee)
    {
        $emails = array();
        if(DeliveryOrder::where('deliveryOrderID',$orderId)->where('selectedForCustomerInvoice',-1)->exists()){
            return ['status' => false,'message'=>'You cannot return back to amend this document. It is added to a customer invoice'];
        }

        if(DeliveryOrder::where('deliveryOrderID',$orderId)->where('selectedForSalesReturn',-1)->exists()){
            return ['status' => false,'message'=>'You cannot return back to amend this document. It is added to a sales return'];
        }

        $masterDataDetails = DeliveryOrder::with('detail')
                                            ->whereHas('detail', function ($query) {
                                                $query->where('itemFinanceCategoryID', 1);
                                            })
                                            ->find($orderId);
        $isExist = false;                                    
        if ($masterDataDetails)
        {
            foreach ($masterDataDetails->detail as $detail) {
                $item = $detail->itemCodeSystem;

                $modelsToCheck = [
                    [CustomerInvoiceDirect::class, 'issue_item_details', ['isPerforma' => 2]],
                    [ItemIssueMaster::class, 'details'],
                    [StockTransfer::class, 'details'],
                    [StockAdjustment::class, 'details'],
                    [StockCount::class, 'details'],
                    [DeliveryOrder::class, 'detail'],
                ];

                foreach ($modelsToCheck as $modelInfo) {
                    [$model, $relation] = $modelInfo;
                    $additionalWhere = $modelInfo[2] ?? [];

                    $query = $model::with($relation)
                        ->whereHas($relation, function ($q) use ($item) {
                            $q->where('itemCodeSystem', $item);
                        });

                    foreach ($additionalWhere as $column => $value) {
                        $query->where($column, $value);
                    }

                    if ($model === DeliveryOrder::class && isset($orderId)) {
                        $query->where('deliveryOrderID', '!=', $orderId);
                    }

                    if ($query->exists()) {
                        $isExist = true;
                        break 2;
                    }
                }
            }
        }

        if($isExist)
        {
            return ['status' => false,'message'=>'You cannot return  back to amend the Delivery Order  because a stock-out document already exists for one or more related items.
                                        Allowing amendments at this stage may impact the existing stock-out document and affect the Weighted Average Cost (WAC) calculation'];
        }

        $emailBody = '<p>' . $masterData->deliveryOrderCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $masterData->deliveryOrderCode . ' has been return back to amend';


        if ($masterData->confirmedYN == 1) {
            $emails[] = array('empSystemID' => $masterData->confirmedByEmpSystemID,
                'companySystemID' => $masterData->companySystemID,
                'docSystemID' => $masterData->documentSystemID,
                'alertMessage' => $emailSubject,
                'emailAlertMessage' => $emailBody,
                'docSystemCode' => $orderId,
                'docCode' => $masterData->quotationCode
            );
        }

        $documentApproval = DocumentApproved::where('companySystemID', $masterData->companySystemID)
            ->where('documentSystemCode', $orderId)
            ->where('documentSystemID', $masterData->documentSystemID)
            ->get();

        foreach ($documentApproval as $da) {
            if ($da->approvedYN == -1) {
                $emails[] = array('empSystemID' => $da->employeeSystemID,
                    'companySystemID' => $masterData->companySystemID,
                    'docSystemID' => $masterData->documentSystemID,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docSystemCode' => $orderId,
                    'docCode' => $masterData->deliveryOrderCode
                );
            }
        }

        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return ['status' => false,'message'=>$sendEmail["message"]];
        }

        DocumentApproved::where('documentSystemCode', $orderId)
                        ->where('companySystemID', $masterData->companySystemID)
                        ->where('documentSystemID', $masterData->documentSystemID)
                        ->delete();


        GeneralLedger::where('documentSystemCode', $orderId)
                        ->where('companySystemID', $masterData->companySystemID)
                        ->where('documentSystemID', $masterData->documentSystemID)
                        ->delete();

        ErpItemLedger::where('documentSystemCode', $orderId)
                        ->where('companySystemID', $masterData->companySystemID)
                        ->where('documentSystemID', $masterData->documentSystemID)
                        ->delete();

        $masterData->confirmedYN = 0;
        $masterData->confirmedByEmpSystemID = null;
        $masterData->confirmedByEmpID = null;
        $masterData->confirmedByName = null;
        $masterData->confirmedDate = null;
        $masterData->RollLevForApp_curr = 1;

        $masterData->approvedYN = 0;
        $masterData->approvedEmpSystemID = null;
        $masterData->approvedbyEmpID = null;
        $masterData->approvedbyEmpName = null;
        $masterData->approvedDate = null;
        $masterData->save();

        AuditTrial::createAuditTrial($masterData->documentSystemID,$orderId,$input['returnComment'],'returned back to amend');
        return true;
    }
}