<?php

namespace App\Services;

use App\helper\Helper;
use App\Models\Appointment;
use App\Models\AppointmentDetails;
use App\Models\AppointmentDetailsRefferedBack;
use App\Models\AppointmentRefferedBack;
use App\Models\CompanyDocumentAttachment;
use App\Models\CountryMaster;
use App\Models\CurrencyMaster;
use App\Models\DirectInvoiceDetails;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\ProcumentOrder;
use App\Models\SlotDetails;
use App\Models\SlotMaster;
use App\Models\PurchaseOrderDetails;
use App\Models\SupplierCategoryMaster;
use App\Models\SupplierCategorySub;
use App\Models\SupplierMaster;
use App\Models\SupplierRegistrationLink;
use App\Models\WarehouseMaster;
use App\Repositories\SupplierInvoiceItemDetailRepository;
use App\Services\Shared\SharedService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use function Clue\StreamFilter\fun;

class SRMService
{
    private $POService = null;
    private $supplierService = null;
    private $sharedService = null;
    private $invoiceService = null;
    private $supplierInvoiceItemDetailRepository;

    public function __construct(
        POService $POService,
        SupplierService $supplierService,
        SharedService $sharedService,
        InvoiceService $invoiceService,
        SupplierInvoiceItemDetailRepository $supplierInvoiceItemDetailRepo
    ) {
        $this->POService        = $POService;
        $this->supplierService  = $supplierService;
        $this->sharedService    = $sharedService;
        $this->invoiceService   = $invoiceService;
        $this->supplierInvoiceItemDetailRepository = $supplierInvoiceItemDetailRepo;
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
        $input = $request->all();
        $supplierID = self::getSupplierIdByUUID($request->input('supplier_uuid'));
        $per_page = $request->input('extra.per_page');
        $page = $request->input('extra.page');
        $search = $request->input('search.value');
        /*return [
        'success' => true,
        'message' => 'Purchase order list successfully get',
        'data' => $input
        ];*/

        /*$data = ProcumentOrder::where('approved', -1)
        ->where('supplierID', $supplierID)
        ->where('poType_N', '!=', 5)
        ->with(['currency', 'created_by', 'segment', 'supplier'])
        ->orderBy('createdDateTime', 'desc')
        ->paginate($per_page, ['*'], 'page', $page);*/

        $query = ProcumentOrder::where('approved', -1)
            ->where('supplierID', $supplierID)
            ->where('poType_N', '!=', 5)
            ->with(['currency', 'created_by', 'segment', 'supplier'])
            ->orderBy('createdDateTime', 'desc');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->orWhere('purchaseOrderCode', 'LIKE', "%{$search}%");
                $query->orWhere('referenceNumber', 'LIKE', "%{$search}%");
                $query->orWhere('supplierName', 'LIKE', "%{$search}%");
                $query->orWhere('poTotalSupplierTransactionCurrency', 'LIKE', "%{$search}%");
                $query->orWhereHas('segment', function ($query1) use ($search) {
                    $query1->where('ServiceLineDes', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('supplier', function ($query1) use ($search) {
                    $query1->where('primarySupplierCode', 'LIKE', "%{$search}%");
                });
            });
        }

        $data = DataTables::eloquent($query)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('purchaseOrderID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->make(true);

        return [
            'success' => true,
            'message' => 'Purchase order list successfully get',
            'data' => $data
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
        $supplierID =  self::getSupplierIdByUUID($request->input('supplier_uuid'));
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
        $supplierID =  self::getSupplierIdByUUID($request->input('supplier_uuid'));
        $appointmentID = $request->input('extra.appointmentID');
        $amend = $request->input('extra.amend');
        $document = DocumentMaster::select('documentID', 'documentSystemID')
            ->where('documentSystemID', 106)
            ->first();

        $lastSerial = Appointment::orderBy('serial_no', 'desc')
            ->first();

        // Amend Appointment
        if ($amend) {
            return self::amendPoAppointment($appointmentID, $slotCompanyId);
        }

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
            if ($appointmentID <= 0 && !$amend) {
                $appointment = Appointment::create($dataMaster);
            }

            if (!empty($data) && $appointmentID > 0 && !$amend) {
                foreach ($data as $val) {
                    AppointmentDetails::where('appointment_id', $appointmentID)
                        ->delete();
                }
            }

            if (!empty($data) && !$amend) {
                foreach ($data as $val) {
                    $data_details['appointment_id'] = (isset($appointment)) ? $appointment->id : $appointmentID;
                    $data_details['po_master_id'] = $val['purchaseOrderID'];
                    $data_details['po_detail_id'] = $val['purchaseOrderDetailID'];
                    $data_details['item_id'] = $val['item_id'];
                    $data_details['qty'] = $val['qty'];
                    AppointmentDetails::create($data_details);
                }
            }

            DB::commit();
            return [
                'success'   => true,
                'message'   => 'Appointment saved successfully',
                'data'      => $data
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'success'   => false,
                'message'   => 'Appointment save failed',
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
    public function  getAppointmentSlots(Request $request)
    {
        $tenantID = $request->input('tenantId');
        $data =  $this->POService->getAppointmentSlots($tenantID);
        $supplierID =  self::getSupplierIdByUUID($request->input('supplier_uuid'));
        $arr = [];
        $x = 0;
        if (isset($data) && $data != '') {
            foreach ($data as $row) {
                foreach ($row['slot_details'] as $slotDetail) {
                    $appointment = Appointment::select('id')
                        ->where('slot_detail_id', $slotDetail->id)
                        ->where('confirmed_yn', 1)
                        ->Where(function ($query) {
                            $query->where('approved_yn', 0)
                                ->orWhere('approved_yn', 1);
                        })
                        ->where('refferedBackYN', 0)
                        ->where('created_by', $supplierID)
                        ->get();

                    $appointmentApproved = Appointment::select('id')
                        ->where('slot_detail_id', $slotDetail->id)
                        ->where('confirmed_yn', 1)
                        ->orWhere(function ($query) {
                            $query->where('approved_yn', 0)
                                ->where('approved_yn', 1);
                        })
                        ->where('refferedBackYN', 0)
                        ->get();

                    $availableConcat = '';
                    if ($row['limit_deliveries'] == 1) {
                        $availableConcat = ' (' . sizeof($appointment) . '/' . $row['no_of_deliveries'] . ')';
                    }
                    $arr[$x]['id'] = $slotDetail->id;
                    $arr[$x]['slot_master_id'] = $row->id;
                    $arr[$x]['title'] = date("h:i A",strtotime($slotDetail->start_date)). '-'.date("h:i A",strtotime($slotDetail->end_date)). ' '.$row->ware_house->wareHouseDescription;
                    $arr[$x]['warehouse'] = $row->ware_house->wareHouseDescription;
                    $arr[$x]['start'] = $slotDetail->start_date;
                    $arr[$x]['end'] = $slotDetail->end_date;
                    $arr[$x]['fullDay'] = 0;
                    $arr[$x]['color'] = '#ffc107';
                    $arr[$x]['status'] = $slotDetail->status;
                    $arr[$x]['slotCompanyId'] = $row['company_id'];
                    $arr[$x]['remaining_appointments'] = ($row['limit_deliveries'] == 0 ? 1 : ($row['no_of_deliveries'] - sizeof($appointment)));
                    $arr[$x]['remaining_approved_pending_appointments_count'] = ($row['limit_deliveries'] == 0 ? 1 : ($row['no_of_deliveries'] - sizeof($appointmentApproved)));
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
        $supplierID =  self::getSupplierIdByUUID($request->input('supplier_uuid'));
        $arr = [];
        $appointment = Appointment::select('id')
            ->where('slot_detail_id', $slotDetailID)
            ->where('confirmed_yn', 1)
            ->where('cancelYN', 0)
            ->Where(function ($query) {
                $query->where('approved_yn', 0)
                    ->orWhere('approved_yn', -1);
            })
            ->where('refferedBackYN', 0)
            /*->where('created_by', $supplierID)*/
            ->get();

        $slotMaster = SlotMaster::where('id', $slotMasterID)->first();

        $arr['remaining_appointments'] = ($slotMaster['limit_deliveries'] == 0 ? 1 : ($slotMaster['no_of_deliveries'] - sizeof($appointment)));

        $data = Appointment::with(['detail' => function ($query) {
            $query->with(['getPoMaster', 'getPoDetails' =>function($query){
                $query->with(['unit','appointmentDetails' => function($q){
                    $q->whereHas('appointment', function ($q){
                        $q->where('refferedBackYN', '!=', -1);
                        /*$q->where('confirmed_yn', 1);*/
                    })->groupBy('po_detail_id')
                        ->select('id', 'appointment_id','qty','po_detail_id')
                        ->selectRaw('sum(qty) as qty');
                }]);
            }]);
        }, 'created_by'])
            ->where('slot_detail_id', $slotDetailID)
            ->where('created_by', $supplierID)
            ->get();
        $arr['data'] = $data;
        return [
            'success'   => true,
            'message'   => 'Calander appointment deliveries get',
            'data'      => $arr
        ];
    }
    public function getPoAppointments(Request $request)
    {
        $appointmentID = $request->input('extra.appointmentID');

        $data = Appointment::with(['detail' => function ($q) {
            $q->with(['getPoDetails' => function ($q1) {
                $q1->with(['order', 'unit']);
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
            'message'   => 'Appointment deleted successfully',
            'data'      => $data
        ];
    }
    public function confirmSupplierAppointment(Request $request)
    {
        $params = array('autoID' => $request->input('extra.data.id'), 'company' => $request->input('extra.data.company_id'), 'document' => $request->input('extra.data.document_system_id'), 'email' => $request->input('extra.email'),);
        $confirm = \Helper::confirmDocument($params);

        return [
            'success'   => $confirm['success'],
            'message'   => $confirm['message'],
            'data'      => $params
        ];
    }

    public static function getSupplierIdByUUID($uuid)
    {

        if ($uuid) {
            $supplier = SupplierRegistrationLink::where('uuid', $uuid)
                ->with(['supplier'])
                ->whereHas('supplier')
                ->first();

            if (!empty($supplier)) {
                return $supplier->supplier_master_id;
            }
        }

        return 0;
    }

    /**
     * create supplier approval setup
     * @param Request $request
     * @return array
     * @throws Throwable
     */
    public function supplierRegistrationApprovalSetup(Request $request)
    {
        $supplierLink = SupplierRegistrationLink::where('uuid', $request->input('supplier_uuid'))->first();

        throw_unless($supplierLink, "Something went wrong, UUID doesn't match with ERP supplier link table reocrd");

        $data = $this->supplierService->createSupplierApprovalSetup([
            'autoID'    => $supplierLink->id,
            'company'   => $supplierLink->company_id,
            'documentID'  => 107, // 107 mean documentMaster id of "Supplier Registration" document in ERP
            'email'  =>   $supplierLink->email
        ]);

        return [
            'success'   => true,
            'message'   => 'Supplier approval setup created!',
            'data'      => $data
        ];
    }

    /**
     * fetch ERP APIs
     * @param array $data
     * @return mixed
     * @throws Throwable
     */
    public function fetch(array $data)
    {
        $apiKey = $data['apiKey'];
        throw_unless($apiKey, "APIS key must be passed");

        return $this->sharedService->fetch([
            'url' => env('ERP_ENDPOINT'),
            'method' => 'POST',
            'data' => [
                'api_key'       => $apiKey,
                'request'       => $data['request'],
                'auth'          => $data['auth'],
                'extra'         => $data['extra'] ?? null,
                'supplier_uuid' => $data['supplier_uuid'] ?? null,
            ]
        ]);
    }

    /**
     * fetch ERP APIs
     * @param array $data
     * @return mixed
     * @throws Throwable
     */
    public function callSRMAPIs(array $data)
    {
        throw_unless($data['apiKey'], "Pass apiKey from calling SRM APIs");
        throw_unless($data['request'], "Pass request from calling SRM APIs");

        \Log::debug('==========$response=========');
        \Log::debug([$data]);
        \Log::debug([env('SRM_ENDPOINT')]);
        \Log::debug('==========$response=========');

        return $this->sharedService->fetch([
            'url' => env('SRM_ENDPOINT'),
            'method' => 'POST',
            'data' => [
                'api_key'       => $data['apiKey'],
                'request'       => $data['request'],
                'extra'         => $data['extra'] ?? null
            ]
        ]);
    }

    /**
     * create supplier approval setup
     * @param Request $request
     * @return array
     * @throws Throwable
     */
    public function getInvoicesList(Request $request)
    {
        $supplierID = self::getSupplierIdByUUID($request->input('supplier_uuid'));
        return [
            'success'   => true,
            'message'   => 'Record retrieved successfully',
            'data'      =>  $this->invoiceService->getInvoicesList($request, $supplierID)
        ];
    }

    /**
     * create supplier approval setup
     * @param Request $request
     * @return array
     * @throws Throwable
     */
    public function getInvoiceDetailsById(Request $request)
    {
        $supplierID = self::getSupplierIdByUUID($request->input('supplier_uuid'));
        $id = $request->input('extra.id');
        $masterData = $this->invoiceService->getInvoiceDetailsById($id, $supplierID);
        if (!empty($masterData)) {
            $masterData = $masterData->toArray();
            $input['bookingSuppMasInvAutoID']    = $id;
            $masterData['detail_data'] =  ['grvDetails' => [], 'logisticYN' => 0];

            foreach ($masterData['detail'] as $detail) {
                $input['bookingSupInvoiceDetAutoID'] = $detail['bookingSupInvoiceDetAutoID'];
                $detailData = $this->supplierInvoiceItemDetailRepository->getGRVDetailsForSupplierInvoice($input);
                if ($detailData['status']) {
                    foreach ($detailData['data']['grvDetails'] as $detailItem) {
                        array_push($masterData['detail_data']['grvDetails'], $detailItem);
                    }
                    $masterData['detail_data']['logisticYN'] = $detailData['data']['logisticYN'];
                }
            }
            $masterData['extraCharges'] = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
                ->with(['segment'])
                ->get();;
        }

        return [
            'success'   => true,
            'message'   => 'Record retrieved successfully',
            'data'      => $masterData
        ];
    }

    private function amendPoAppointment($appointmentID, $slotCompanyId)
    {
        $amendedAppointment = Appointment::where('id', $appointmentID)
            ->select('appointment.id AS appointment_id',
                'appointment.supplier_id',
                'appointment.document_system_id',
                'appointment.serial_no',
                'appointment.primary_code',
                'appointment.document_id',
                'appointment.status',
                'appointment.slot_detail_id',
                'appointment.company_id',
                'appointment.tenat_id',
                'appointment.created_by',
                'appointment.updated_at',
                'appointment.created_at',
                'appointment.confirmed_by_emp_id',
                'appointment.confirmedByName',
                'appointment.confirmedByEmpID',
                'appointment.confirmed_date',
                'appointment.approved_yn',
                'appointment.approved_date',
                'appointment.approved_by_emp_name',
                'appointment.approved_by_emp_id',
                'appointment.current_level_no',
                'appointment.timesReferred',
                'appointment.confirmed_yn',
                'appointment.refferedBackYN'
            )
            ->get()->toArray();

        $insertAppointment = AppointmentRefferedBack::insert($amendedAppointment);

        $amendedAppointmentDetails = AppointmentDetails::where('appointment_id', $appointmentID)
            ->select('appointment_details.id AS appointment_details_id',
                'appointment_details.appointment_id',
                'appointment_details.po_master_id',
                'appointment_details.po_detail_id',
                'appointment_details.item_id',
                'appointment_details.qty',
                'appointment_details.created_by',
                'appointment_details.updated_at',
                'appointment_details.created_at'

            )
            ->get()->toArray();
        $insertAppointmentDetails = AppointmentDetailsRefferedBack::insert($amendedAppointmentDetails);

        if($insertAppointment && $insertAppointmentDetails){

            $statusChange = Appointment::where('id', $appointmentID)
                ->update([
                    'approved_yn' => 0,
                    'confirmed_yn' => 0,
                    'refferedBackYN' => 0
                ]);

            if($statusChange){
                self::poAppointmentReferback($appointmentID, $slotCompanyId);

                return [
                    'success'   => true,
                    'message'   => 'Appointment amended successfully',
                    'data'      => [$insertAppointment, $insertAppointmentDetails]
                ];
            }
        }

        return [
            'success'   => false,
            'message'   => 'Appointment amendment failed',
            'data'      => 'failed'
        ];
    }

    private function poAppointmentReferback($appointmentID, $slotCompanyId)
    {
        $appointment = Appointment::find($appointmentID);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $appointmentID)
            ->where('companySystemID', $slotCompanyId)
            ->where('documentSystemID', 106)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $appointment->refferedBackYN;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        DocumentReferedHistory::insert($DocumentApprovedArray);

        DocumentApproved::where('documentSystemCode', $appointmentID)
            ->where('companySystemID', $slotCompanyId)
            ->where('documentSystemID', 106)
            ->delete();
    }
    public function supplierRegistrationApprovalAmmend(Request $request)
    {
        $kycFormDetails = SupplierRegistrationLink::where('uuid', $request->input('supplier_uuid'))
            ->first();
        $id =  $kycFormDetails->id;
        $companySystemID =  $kycFormDetails->company_id;
        $documentSystemID = 107;
        $timesReferred = $kycFormDetails->timesReferred;

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $companySystemID)
            ->where('documentSystemID', $documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();
        DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $companySystemID)
            ->where('documentSystemID', $documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $kycFormDetails->confirmed_yn = 0;
            $kycFormDetails->confirmed_by_emp_id = null;
            $kycFormDetails->confirmed_by_name = null;
            $kycFormDetails->confirmed_date = null;
            $kycFormDetails->RollLevForApp_curr = 1;
            $kycFormDetails->refferedBackYN = 0;
            $kycFormDetails->save();
        }
        return [
            'success'   => true,
            'message'   => 'Supplier Ammend',
            'data'      => $kycFormDetails
        ];
    }
    public function getERPFormData(Request $request){
        $currencyMaster = CurrencyMaster::select('currencyID','CurrencyName','CurrencyCode')->get();
        $countryMaster = CountryMaster::select('countryID','countryCode','countryName')->get();
        $supplierCategoryMaster = SupplierCategoryMaster::select('supCategoryMasterID','categoryCode','categoryDescription')->get(); 
        $supplierCategorySubMaster = SupplierCategorySub::select('supCategorySubID','supMasterCategoryID','subCategoryCode','categoryDescription')->get();
        $formData =  array(
            'currencyMaster' => $currencyMaster,
            'countryMaster' => $countryMaster,
            'supplierCategoryMaster' => $supplierCategoryMaster,
            'supplierCategorySubMaster' => $supplierCategorySubMaster,
        );
        
        return [
            'success'   => true,
            'message'   => 'ERP Form Data Retrieved',
            'data'      => $formData
        ];
    }

    public function checkAppointmentPastDate(Request $request)
    {
        $slotDetailID = $request->input('extra.slotDetailID');

        $detail = SlotDetails::where('id',$slotDetailID)->first();

        $appointments = $this->getAppointmentDeliveries($request);
        $appointment = 0;
        if(count($appointments['data']['data'])>0){
            $appointment = 1;
        }

        if(!empty($detail)){//start_date
            $endDate = Carbon::parse($detail['end_date'])->format('Y-m-d H:i:s');
            $currentDate = Carbon::parse(now())->format('Y-m-d H:i:s');
            $result['currentDate']=$currentDate;
            $result['endDate']=$endDate;

            $start_date = Carbon::parse($detail['start_date'])->format('Y-m-d');
            $current = Carbon::parse(now())->format('Y-m-d');
            $canCancel = 0;
            if($start_date>$current){
                $canCancel = 1;
            }

            if($endDate > $currentDate){
                $result['canCreate']=1;
                $result['canCancel']=$canCancel;
                $result['appointments']=$appointment;
                return [
                    'success'   => true,
                    'message'   => 'Appointment Can Be Created',
                    'data'      => $result
                ];
            }else{
                $result['canCreate']=0;
                $result['canCancel']=$canCancel;
                $result['appointments']=$appointment;
                return [
                    'success'   => true,
                    'message'   => 'Appointments can not be created for past dates',
                    'data'      => $result
                ];
            }
        }else{
            return [
                'success'   => false,
                'message'   => 'Slot Detail Not Available',
                'data'      => $detail
            ];
        }
    }

    public function  getAppointmentDetails(Request $request)
    {
        $appointmentID = $request->input('extra.appointmentID');

        $detail = AppointmentDetails::where('appointment_id',$appointmentID)
            ->with(['getPoMaster', 'getPoDetails' =>function($query) use($appointmentID){
            $query->with(['unit','appointmentDetails' => function($q) use($appointmentID){
                $q->whereHas('appointment', function ($q) use($appointmentID){
                    $q->where('refferedBackYN', '!=', -1);
                    $q->where('cancelYN', 0);
                    if(isset($appointmentID)){
                        $q->where('id','!=', $appointmentID);
                    }
                })->groupBy('po_detail_id')
                    ->select('id', 'appointment_id','qty','po_detail_id')
                    ->selectRaw('IFNULL(sum(qty),0) as qty');
            }]);
        }])->get()
            ->transform(function ($data){
                return $this->appointmentDetailFormat($data);
            });
        $result['detail']=$detail;
        $result['purchaseOrderCode']='';
        if(count($detail) > 0){
            $result['exist']=1;
            if(!empty($detail[0]['getPoMaster'])){
                $result['purchaseOrderCode']=$detail[0]['getPoMaster']['purchaseOrderCode'];
            }
            return [
                'success'   => true,
                'message'   => 'Appointment Details Available',
                'data'      => $result
            ];
        }else{
            $result['exist']=0;
            return [
                'success'   => false,
                'message'   => 'Appointment Details Not Available',
                'data'      => $result
            ];
        }
    }

    public function getPurchaseOrderDetails(Request $request)
    {
        $purchaseOrderID = $request->input('extra.purchaseOrderID');
        $appointmentID = $request->input('extra.appointmentID');

        $po = PurchaseOrderDetails::where('purchaseOrderMasterID',$purchaseOrderID)
            ->with(['order','unit','appointmentDetails' => function($q) use($appointmentID){
                $q->whereHas('appointment', function ($q) use($appointmentID){
                    $q->where('refferedBackYN', '!=', -1);
                    $q->where('cancelYN', 0);
                    if(isset($appointmentID)){
                        $q->where('id','!=', $appointmentID);
                    }
                })->groupBy('po_detail_id')
                    ->select('id', 'appointment_id','qty','po_detail_id')
                    ->selectRaw('IFNULL(sum(qty),0) as qty');
            }])->get()
            ->transform(function ($data){
                return $this->poDetailFormat($data);
            });

        $result['poDetail']=$po;
        return [
            'success'   => true,
            'message'   => 'Po Details Retrieved',
            'data'      => $result
        ];
    }

    public function poDetailFormat($data){
        if(count($data['appointmentDetails'])>0){
            $sumQty = $data['appointmentDetails'][0]['qty'];
        }else{
            $sumQty = 0;
        }
        return [
            'purchaseOrderCode' => $data['order']['purchaseOrderCode'],
            'purchaseOrderID' => $data['order']['purchaseOrderID'],
            'purchaseOrderDetailID' => $data['purchaseOrderDetailsID'],
            'itemPrimaryCode' => $data['itemPrimaryCode'],
            'itemDescription' => $data['itemDescription'],
            'UnitShortCode' => $data['unit']['UnitShortCode'],
            'noQty' => $data['noQty'],
            'receivedQty' => $data['receivedQty'],
            'sumQty' => $sumQty,
            'qty' => 0,
            'item_id' => $data['itemCode'],

        ];
    }

    public function appointmentDetailFormat($data){
        if(count($data['getPoDetails']['appointmentDetails'])>0){
            $sumQty = $data['getPoDetails']['appointmentDetails'][0]['qty'];
        }else{
            $sumQty = 0;
        }
        return [
            'purchaseOrderCode' => $data['getPoMaster']['purchaseOrderCode'],
            'purchaseOrderID' => $data['getPoMaster']['purchaseOrderID'],
            'purchaseOrderDetailID' => $data['getPoDetails']['purchaseOrderDetailsID'],
            'itemPrimaryCode' => $data['getPoDetails']['itemPrimaryCode'],
            'itemDescription' => $data['getPoDetails']['itemDescription'],
            'UnitShortCode' => $data['getPoDetails']['unit']['UnitShortCode'],
            'noQty' => $data['getPoDetails']['noQty'],
            'receivedQty' => $data['getPoDetails']['receivedQty'],
            'sumQty' => $sumQty,
            'qty' => $data['qty'],
            'item_id' => $data['item_id'],


        ];
    }

    public function getAllAppointmentList(Request $request): array
    {
        $input  = $request->all();
        $supplierID = self::getSupplierIdByUUID($request->input('supplier_uuid'));
        $warehouseId = $request->input('extra.warehouseId');
        $appointDate = $request->input('extra.appointDate');
        $search = $request->input('search.value');

        $query =  Appointment::where('supplier_id', $supplierID)
            ->with(['created_by', 'slot_detail' => function($query){
                $query->withCount(['appointment' => function($q){
                    $q->where('confirmed_yn', 1)
                        ->Where(function ($query) {
                            $query->where('approved_yn', 0)
                                ->orWhere('approved_yn', -1);
                        })
                        ->where('refferedBackYN', 0);
                }]);
            },'slot_detail.slot_master.ware_house', 'slot_detail.slot_master']);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->orWhere('primary_code', 'LIKE', "%{$search}%");
                $query->orWhere('created_at', 'LIKE', "%{$search}%");
                $query->orWhere('status', 'LIKE', "%{$search}%");
                $query->orWhere('created_by', 'LIKE', "%{$search}%");
                $query->orWhereHas('slot_detail.slot_master.ware_house', function ($query1) use ($search) {
                    $query1->where('wareHouseDescription', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('created_by', function ($query1) use ($search) {
                    $query1->where('supplierName', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('slot_detail.slot_master', function ($query1) use ($search) {
                    $query1->whereDate('from_date', "%{$search}%");
                });
            });
        }

        if(isset($warehouseId) && $warehouseId !== 0) {
            $query = $query->whereHas('slot_detail.slot_master.ware_house', function ($query) use ($warehouseId) {
                $query->where('wareHouseSystemCode', $warehouseId);
            });
        }

        if(!(is_null($appointDate)) && isset($appointDate)) {
            $query = $query->whereHas('slot_detail.slot_master', function ($query) use($appointDate){
                $query->whereDate('from_date', $appointDate);
            });
        }

        //$query = $query->orderBy('slot_detail.slot_master.from_date', 'desc');
        $data = DataTables::eloquent($query)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        // $query->orderBy('id', $input['order'][0]['dir']);
                        $query->whereHas('slot_detail.slot_master', function ($query1){
                            $query1->orderBy('from_date', 'DESC');
                        });
                    }
                }
            })
            ->addIndexColumn()
            ->make(true);

        return [
            'success'   => true,
            'message'   => 'Appointment list successfully get',
            'data'      => $data
        ];
    }

    public function getWarehouse(Request $request)
    {
        try{
            $warehouse = WarehouseMaster::where('isActive', 1)->get();
            $message = 'Warehouse list load successfully';
        } catch (\Exception $e){
            $message = $e;
        }

        return [
            'success'   => true,
            'message'   => $message,
            'data'      => $warehouse
        ];
    }

    public function getRemainingSlotCount(Request $request)
    {
        $remainingAppointments = 0;
        try{
            $slotDetailID = $request->input('extra.slotDetailID');
            $slotMasterID = $request->input('extra.slotMasterID');

            $appointmentCount = Appointment::select('id')
                ->where('slot_detail_id', $slotDetailID)
                ->where('confirmed_yn', 1)
                ->where('cancelYN', 0)
                ->Where(function ($query) {
                    $query->where('approved_yn', 0)
                        ->orWhere('approved_yn', -1);
                })
                ->where('refferedBackYN', 0)
                ->count();

            $slotMaster = SlotMaster::where('id', $slotMasterID)->first();
            $message = "Success";

            $remainingAppointments = ($slotMaster['limit_deliveries'] == 0 ? 1 : ($slotMaster['no_of_deliveries'] - $appointmentCount));
        } catch (\Exception $e){
            $message = $e;
        }

        return [
            'success'   => true,
            'message'   => $message,
            'data'      => $remainingAppointments
        ];
    }

    public function cancelAppointments(Request $request)
    {
        try{
            $id = $request->input('extra.appointmentID');
            $supplierID =  self::getSupplierIdByUUID($request->input('supplier_uuid'));

            $supplier = SupplierMaster::where('supplierCodeSystem', $supplierID)->first();

            $canceledReason = $request->input('extra.canceledReason');
            $Data['cancelYN'] = 1;
            $Data['canceledDate'] = Helper::currentDateTime();
            $Data['canceledByEmpId'] = $supplierID;
            $Data['canceledReason'] = $canceledReason;
            $Data['canceledByName'] = $supplier['supplierName'];
            $result = Appointment::where('id', $id)->update($Data);

            $message ='Delivery appointment canceled successfully';
            $success = true;
        } catch (\Exception $e){
            $success = false;
            $message = $e;
            $result = 0;
        }

        return [
            'success'   => $success,
            'message'   => $message,
            'data'      => $result
        ];
    }

    public function getSrmApprovedDetails(Request $request)
    {
        $documentSystemID = $request->input('extra.documentSystemID');
        $documentSystemCode = $request->input('extra.documentSystemCode');
        $companySystemID = $request->input('extra.companySystemID');

        $approveDetails = DocumentApproved::where('documentSystemID', $documentSystemID)
            ->where('documentSystemCode', $documentSystemCode)
            ->where('companySystemID', $companySystemID)
            ->with(['approved_by'])
            ->get();

        foreach ($approveDetails as $value) {

            if ($value['approvedYN'] == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $companySystemID)
                    ->where('documentSystemID', $documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return [
                        'success'   => false,
                        'message'   => 'Policy not found',
                        'data'      => $companyDocument
                    ];
                }

                $approvalList = EmployeesDepartment::where('employeeGroupID', $value['approvalGroupID'])
                    ->where('companySystemID', $companySystemID)
                    ->where('documentSystemID', $documentSystemID)
                    ->where('isActive', 1)
                    ->where('removedYN', 0);
                //->get();

                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $approvalList = $approvalList->where('ServiceLineSystemID', $value['serviceLineSystemID']);
                }

                $approvalList = $approvalList->with(['employee'])
                    ->whereHas('employee', function($q) {
                        $q->where('discharegedYN',0);
                    })
                    ->groupBy('employeeSystemID')
                    ->get();
                $value['approval_list'] = $approvalList;
            }
        }

        return [
            'success'   => true,
            'message'   => 'Record retrieved successfully',
            'data'      => $approveDetails
        ];

    }
}
