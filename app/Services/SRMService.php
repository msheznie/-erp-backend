<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentDetails;
use App\Models\ProcumentOrder;
use App\Models\SlotDetails;
use Illuminate\Http\Request;

class SRMService
{
    private $POService = null;
    private $supplierService = null;
    public function __construct(POService $POService, SupplierService $supplierService)
    {
        $this->POService = $POService;
        $this->supplierService = $supplierService;
    }

    /**
     * get currencies
     * @return array
     */
    public function getCurrencies(): array
    {
        $data = [
            'LKR',
            'USD',
            'ASD'
        ];

        return [
            'success'   => true,
            'message'   => 'currencies successfully get',
            'data'      => $data
        ];
    }
    public function getPoList(Request $request): array
    {
        $supplierID = $request->input('auth.id');
        $per_page = $request->input('extra.per_page');
        $page = $request->input('extra.page');
        $data = ProcumentOrder::where('approved', -1)
            ->where('supplierID', $supplierID)
            ->where('documentSystemID', 2)
            ->with(['currency', 'created_by'])
            ->paginate($per_page, ['*'], 'page', $page);
        return [
            'success'   => true,
            'message'   => 'Purchase order list successfully get',
            'data'      => $data
        ];
    }
    public function getPoPrintData(Request $request)
    {
        $purchaseOrderID = $request->input('extra.purchaseOrderID');
        $data =  $this->POService->getPoPrintData($purchaseOrderID);
        return [
            'success'   => true,
            'message'   => 'Purchase order print data successfully get',
            'data'      => $data
        ];
    }
    public function getPoAddons(Request $request)
    {
        $purchaseOrderID = $request->input('extra.purchaseOrderID');
        $data =  $this->POService->getPoAddons($purchaseOrderID);
        return [
            'success'   => true,
            'message'   => 'Purchase order addon successfully get',
            'data'      => $data
        ];
    }
    public function getAppointmentSlots(Request $request)
    {
        $tenantID = $request->input('tenantId');
        $comapnyID = $request->input('extra.companyId');
        $wareHouseID = $request->input('extra.wareHouseID');
        $supplierID =  $request->input('auth.id');
        $data =  $this->POService->getAppointmentSlots($comapnyID, $wareHouseID);
        return [
            'success'   => true,
            'message'   => 'Calander appointment slots successfully get',
            'data'      => $data
        ];
    }
    public function getPurchaseOrders(Request $request)
    {
        $tenantID = $request->input('tenantId');
        $comapnyID = $request->input('extra.companyID');
        $wareHouseID = $request->input('extra.wareHouseID');
        $supplierID =  $request->input('auth.id');
        $data =  $this->POService->getPurchaseOrders($comapnyID, $wareHouseID, $supplierID, $tenantID);
        return [
            'success'   => true,
            'message'   => 'Purchase Orders successfully get',
            'data'      => $data
        ];
    }
    public function SavePurchaseOrderList(Request $request)
    {
        $tenantID = $request->input('tenantId');
        $purchaseOrderDetailID = $request->input('extra.purchaseOrderDetailID');
        $item_id = $request->input('extra.item_id');
        $quantityRequested =  $request->input('auth.quantityRequested');
        $slot_detail_id =  $request->input('extra.slot_detail_id');
        $supplierID =  $request->input('auth.id');
        $purchaseOrderID =  $request->input('extra.purchaseOrderID');

        $data['supplier_id'] = $supplierID;
        $data['status'] = 0;
        $data['slot_detail_id'] = $slot_detail_id;
        $appointment = Appointment::create($data);
        if ($appointment) {
            $slotDetail['status'] = 2;
            SlotDetails::where('id', $slot_detail_id)
                ->update($slotDetail);
            $data_details['appointment_id'] = $appointment->id;
            $data_details['po_master_id'] = $purchaseOrderID;
            $data_details['item_id'] = $item_id;
            $data_details['qty'] = 0;
            $appointmentDetail = AppointmentDetails::create($data_details);
        }

        return [
            'success'   => true,
            'message'   => 'Purchase Orders Appointment get',
            'data'      => $appointment
        ];
    }
    public function getSupplierInvitationInfo(Request $request)
    {
        $invitationToken = $request->input('extra.token');
        $data =  $this->supplierService->getTokenData($invitationToken);

        if (!$data) {
            return [
                'success'   => false,
                'message'   => "Invalid Token",
                'data'      => null
            ];
        }

        return [
            'success'   => true,
            'message'   => 'Valid Invitation Link',
            'data'      => $data
        ];
    }

    public function updateSupplierInvitaion(Request $request){
        $invitationToken = $request->input('extra.token');
        $isUpdated =  $this->supplierService->updateTokenStatus($invitationToken);

        if(!$isUpdated){
            return [
                'success'   => false,
                'message'   => "Update Failed",
                'data'      => null
            ];
        }

        return [
            'success'   => true,
            'message'   => 'Updated Successfully',
            'data'      => $isUpdated
        ];
    }
}
