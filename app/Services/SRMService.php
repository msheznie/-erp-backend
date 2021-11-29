<?php

namespace App\Services;

use App\helper\Helper;
use App\Models\Appointment;
use App\Models\AppointmentDetails;
use App\Models\DocumentMaster;
use App\Models\ProcumentOrder;
use App\Models\SlotDetails;
use App\Models\SlotMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function getPurchaseOrders(Request $request)
    {
        $tenantID = $request->input('tenantId');
        $wareHouseID = $request->input('extra.wareHouseID');
        $supplierID =  $request->input('auth.id');
        $poData = [];
        $data =  $this->POService->getPurchaseOrders($wareHouseID, $supplierID, $tenantID);

        return [
            'success'   => true,
            'message'   => 'Purchase Orders successfully get',
            'data'      => $data
        ];
    }
    public function SavePurchaseOrderList(Request $request)
    {
        $tenantID = $request->input('tenantId');
        $data = $request->input('extra.purchaseOrders');
        $slotDetailID = $request->input('extra.slotDetailID');
        $slotCompanyId = $request->input('extra.slotCompanyId');
        $supplierID =  $request->input('auth.id');
        $appointmentID = $request->input('extra.appointmentID');;
        $document = DocumentMaster::select('documentID', 'documentSystemID')
            ->where('documentSystemID', 106)
            ->first();

        $lastSerial = Appointment::orderBy('serial_no', 'desc')
            ->first();
        DB::beginTransaction();
        try {

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serial_no) + 1;
            }
            $code =  ($document['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $dataMaster['serial_no'] = $lastSerialNumber;
            $dataMaster['primary_code'] = $code;
            $dataMaster['supplier_id'] = $supplierID;
            $dataMaster['status'] = 0;
            $dataMaster['slot_detail_id'] = $slotDetailID;
            $dataMaster['created_by'] = $supplierID;
            $dataMaster['document_id'] = $document->documentID;
            $dataMaster['document_system_id'] = $document->documentSystemID;
            $dataMaster['company_id'] = $slotCompanyId;
            $slotData['status'] = 1;  
            SlotDetails::where('id', $slotDetailID)->update($slotData);
            if ($appointmentID <= 0) { 
                $appointment = Appointment::create($dataMaster);
            }

            if (!empty($data) && $appointmentID > 0) {
                foreach ($data as $val) {
                    AppointmentDetails::where('appointment_id', $appointmentID)
                        ->delete();
                }
            }

            if (!empty($data)) {
                foreach ($data as $val) {
                    $data_details['appointment_id'] = (isset($appointment)) ? $appointment->id : $appointmentID;
                    $data_details['po_master_id'] = ($appointmentID > 0) ? $val['po_master_id'] : $val['purchaseOrderID'];
                    $data_details['po_detail_id'] = ($appointmentID > 0) ? $val['po_detail_id'] : $val['purchaseOrderDetailID'];
                    $data_details['item_id'] = ($appointmentID > 0) ? $val['item_id'] : $val['item_id'];
                    $data_details['qty'] = 0;;
                    AppointmentDetails::create($data_details);
                }
            }

            DB::commit();
            return [
                'success'   => true,
                'message'   => 'Purchase Orders Appointment save successfully',
                'data'      => $data
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'success'   => false,
                'message'   => 'Purchase Orders Appointment save failed',
                'data'      => $exception->getMessage()
            ];
        }
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

    public function updateSupplierInvitation(Request $request)
    {
        $invitationToken = $request->input('extra.token');
        $supplierUuid = $request->input('supplier_uuid');

        $isUpdated =  $this->supplierService->updateTokenStatus($invitationToken, $supplierUuid);

        if (!$isUpdated) {
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
    public function getAppointmentSlots(Request $request)
    {
        $tenantID = $request->input('tenantId');
        $data =  $this->POService->getAppointmentSlots($tenantID);
        $arr = [];
        $x = 0;
        if (isset($data) && $data != '') {
            foreach ($data as $row) {
                foreach ($row['slot_details'] as $slotDetail) {
                    $appointment = Appointment::select('id')->where('slot_detail_id', $slotDetail->id)->get();
                    $availableConcat = '';
                    if($row['limit_deliveries']==1){ 
                       $availableConcat = ' (' . sizeof($appointment) . '/' . $row['no_of_deliveries'] . ')';
                    }
                    $arr[$x]['id'] = $slotDetail->id;
                    $arr[$x]['slot_master_id'] = $row->id;
                    $arr[$x]['title'] =  $row->ware_house->wareHouseDescription .$availableConcat;
                    $arr[$x]['start'] = $slotDetail->start_date;
                    $arr[$x]['end'] = $slotDetail->end_date;
                    $arr[$x]['fullDay'] = 0;
                    $arr[$x]['color'] = '#ffc107';
                    $arr[$x]['status'] = $slotDetail->status;
                    $arr[$x]['slotCompanyId'] = $row['company_id'];
                    $arr[$x]['remaining_appointments'] = ($row['limit_deliveries'] == 0 ? 1: ($row['no_of_deliveries'] - sizeof($appointment)) );
                    $x++;
                }
            }
            return [
                'success'   => true,
                'message'   => 'Calander appointment slots successfully get',
                'data'      => $arr
            ];
        }
    }
    public function getAppointmentDeliveries(Request $request)
    {

        $slotDetailID = $request->input('extra.slotDetailID');
        $slotMasterID = $request->input('extra.slotMasterID');

        $data = Appointment::with(['detail' => function ($query) {
            $query->with(['getPoMaster', 'getPoDetails']);
        }, 'created_by'])
            ->where('slot_detail_id', $slotDetailID)
            ->get();

        return [
            'success'   => true,
            'message'   => 'Calander appointment deliveries get',
            'data'      => $data
        ];
    }
    public function getPoAppointments(Request $request)
    {
        $appointmentID = $request->input('extra.appointmentID');

        $data = Appointment::with(['detail' => function ($q) {
            $q->with(['getPoDetails' => function ($q1) {
                $q1->with(['productmentOrder', 'unit']);
            }]);
        }])
            ->where('id', $appointmentID)->first();

        return [
            'success'   => true,
            'message'   => 'Calander appointment get',
            'data'      => $data
        ];
    }
    public function deleteSupplierAppointment(Request $request)
    {
        $appointmentID = $request->input('extra.id');
        $slotMasterID = $request->input('extra.slotMasterID');
        $slotDetailID = $request->input('extra.slotDetailID');
        $appointment = Appointment::where('id', $appointmentID)->delete();
        if ($appointment) {
            $appointmentDetail =  AppointmentDetails::where('appointment_id', $appointmentID)->delete();
        }
        $slotMaster = SlotMaster::select('no_of_deliveries')->where('id', $slotMasterID)->first();
        $slotDetailAppointment = Appointment::select('id')->where('slot_detail_id', $slotDetailID)->get();

        $data['AvailabelDeliveries'] = $slotMaster['no_of_deliveries'] - sizeof($slotDetailAppointment);
        if (sizeof($slotDetailAppointment) == 0) {
            $slotData['status'] = 0;
            SlotDetails::where('id', $slotDetailID)->update($slotData);
        }


        return [
            'success'   => true,
            'message'   => 'Calander appointment deleted',
            'data'      => $data
        ];
    }
    public function confirmSupplierAppointment(Request $request)
    {
        $params = array('autoID' => $request->input('extra.data.id'), 'company' => $request->input('extra.data.company_id'), 'document' => $request->input('extra.data.document_system_id'));
        $confirm = \Helper::confirmDocument($params);
        return [
            'success'   => $confirm['success'],
            'message'   => $confirm['message'],
            'data'      => $params
        ];
    }
}
