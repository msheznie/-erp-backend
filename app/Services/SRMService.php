<?php

namespace App\Services;

use App\helper\Helper;
use App\Http\Controllers\API\DocumentAttachmentsAPIController;
use App\Models\Appointment;
use App\Models\AppointmentDetails;
use App\Models\AppointmentDetailsRefferedBack;
use App\Models\AppointmentRefferedBack;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CountryMaster;
use App\Models\CurrencyMaster;
use App\Models\DirectInvoiceDetails;
use App\Models\DocumentApproved;
use App\Models\DocumentAttachments;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\Employee;
use App\Models\EmployeesDepartment;
use App\Models\ProcumentOrder;
use App\Models\SlotDetails;
use App\Models\SlotMaster;
use App\Models\PurchaseOrderDetails;
use App\Models\SupplierCategoryMaster;
use App\Models\SupplierCategorySub;
use App\Models\SupplierMaster;
use App\Models\SupplierRegistrationLink;
use App\Models\TenderBidClarifications;
use App\Models\TenderFaq;
use App\Models\TenderMaster;
use App\Models\TenderMasterSupplier;
use App\Models\WarehouseMaster;
use App\Repositories\DocumentAttachmentsRepository;
use App\Repositories\SupplierInvoiceItemDetailRepository;
use App\Repositories\TenderBidClarificationsRepository;
use App\Services\Shared\SharedService;
use Aws\Ec2\Exception\Ec2Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
    private $tenderBidClarificationsRepository;
    private $documentAttachmentsRepo;

    public function __construct(
        POService $POService,
        SupplierService $supplierService,
        SharedService $sharedService,
        InvoiceService $invoiceService,
        SupplierInvoiceItemDetailRepository $supplierInvoiceItemDetailRepo,
        TenderBidClarificationsRepository $tenderBidClarificationsRepo,
        DocumentAttachmentsRepository $documentAttachmentsRepo
    ) {
        $this->POService        = $POService;
        $this->supplierService  = $supplierService;
        $this->sharedService    = $sharedService;
        $this->invoiceService   = $invoiceService;
        $this->supplierInvoiceItemDetailRepository = $supplierInvoiceItemDetailRepo;
        $this->tenderBidClarificationsRepository = $tenderBidClarificationsRepo;
        $this->documentAttachmentsRepo = $documentAttachmentsRepo;
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
        $searchText = $request->input('extra.searchText');
        $supplierID =  self::getSupplierIdByUUID($request->input('supplier_uuid'));
        $poData = [];
        $data =  $this->POService->getPurchaseOrders($wareHouseID, $supplierID, $tenantID, $searchText);

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
        $company = Company::where('companySystemID', $slotCompanyId)->first();
        $supplierID =  self::getSupplierIdByUUID($request->input('supplier_uuid'));
        $appointmentID = $request->input('extra.appointmentID');
        $amend = $request->input('extra.amend');
        $document = DocumentMaster::select('documentID', 'documentSystemID')
            ->where('documentSystemID', 106)
            ->first();
        $attachment = $request->input('extra.attachment');
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
                    $data_details['foc_qty'] = isset($val['foc_qty']) ? $val['foc_qty'] : null;
                    $data_details['total_amount_after_foc'] = $val['total_amount_after_foc'];
                    $data_details['expiry_date'] = isset($val['expiry_date']) ? $val['expiry_date'] : null;
                    $data_details['batch_no'] = isset($val['batch_no']) ? $val['batch_no'] : null;
                    $data_details['manufacturer'] = isset($val['manufacturer']) ? $val['manufacturer'] : null;
                    $data_details['brand'] = isset($val['brand']) ? $val['brand'] : null;
                    $data_details['remarks'] = isset($val['remarks']) ? $val['remarks'] : null;
                    AppointmentDetails::create($data_details);
                }
            }

            // Add Attachments
            if (isset($attachment) && !empty($attachment)) {
                $this->uploadAttachment($attachment, $slotCompanyId, $company, $document, $appointment->id);
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
            'success'   => $data['success'],
            'message'   => $data['message'],
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
                'appointment.RollLevForApp_curr',
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
            ->with(['getPoMaster', 'getPoMaster.transactioncurrency', 'getPoDetails' =>function($query) use($appointmentID){
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
        }, 'appointment.attachment'])->get()
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
        $searchText = $request->input('extra.searchText');

        $po = PurchaseOrderDetails::where('purchaseOrderMasterID',$purchaseOrderID);

        if (!empty($searchText)) {
            $searchText = str_replace("\\", "\\\\", $searchText);
            $po = $po->where(function ($query) use ($searchText) {
                $query->where('itemDescription', 'LIKE', "%{$searchText}%")
                    ->orWhere('itemPrimaryCode', 'LIKE', "%{$searchText}%");
            });
        }

        $po = $po->with(['order','unit','appointmentDetails' => function($q) use($appointmentID){
                $q->whereHas('appointment', function ($q) use($appointmentID){
                    $q->where('refferedBackYN', '!=', -1);
                    $q->where('cancelYN', 0);
                    if(isset($appointmentID)){
                        $q->where('id','!=', $appointmentID);
                    }
                })->groupBy('po_detail_id')
                    ->select('id', 'appointment_id','qty','po_detail_id')
                    ->selectRaw('IFNULL(sum(qty),0) as qty');
            },'order.transactioncurrency'])->get()
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
            'unitCost' => $data['unitCost'],
            'receivedQty' => $data['receivedQty'],
            'sumQty' => $sumQty,
            'qty' => 0,
            'item_id' => $data['itemCode'],
            'transactioncurrency' => $data['order']['transactioncurrency'],
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
            'unitCost' => $data['getPoDetails']['unitCost'],
            'foc_qty' => $data['foc_qty'],
            'total_amount_after_foc' => $data['total_amount_after_foc'],
            'expiry_date' => $data['expiry_date'],
            'batch_no' => $data['batch_no'],
            'manufacturer' => $data['manufacturer'],
            'brand' => $data['brand'],
            'remarks' => $data['remarks'],
            'item_id' => $data['item_id'],
            'attachment' => $data['appointment']['attachment'],
            'transactioncurrency' => $data['getPoMaster']['transactioncurrency'],
        ];
    }

    public function getAllAppointmentList(Request $request): array
    {
        $input  = $request->all();
        $supplierID = self::getSupplierIdByUUID($request->input('supplier_uuid'));
        $warehouseId = $request->input('extra.warehouseId');
        $appointDate = $request->input('extra.appointDate');
        $search = $request->input('search.value');

        $query = DB::table('appointment')
            ->select('*', 'appointment.id as appointmentId','appointment.refferedBackYN as appointmentRefferedBackYN', 'appointment.created_at as appointmentCreatedDate', 'suppliermaster.supplierName as appointmentCreatedBy')
            ->join('slot_details', function($query) {
                $query->on('appointment.slot_detail_id', '=', 'slot_details.id');
            })
            ->where('appointment.supplier_id', $supplierID)
            ->join('suppliermaster', 'appointment.created_by', 'suppliermaster.supplierCodeSystem')
            ->join('slot_master', 'slot_master.id', 'slot_details.slot_master_id')
            ->join('warehousemaster', 'slot_master.warehouse_id', 'warehousemaster.wareHouseSystemCode');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->orWhere('primary_code', 'LIKE', "%{$search}%");
                $query->orWhere('appointment.created_at', 'LIKE', "%{$search}%");
                $query->orWhere('wareHouseDescription', 'LIKE', "%{$search}%");
                $query->orWhere('suppliermaster.supplierName', 'LIKE', "%{$search}%");
                $query->orWhereDate('slot_details.start_date', 'LIKE', "%{$search}%");
            });
        }

        if(isset($warehouseId) && $warehouseId !== 0) {
            $query->where('wareHouseSystemCode', $warehouseId);
        }

        if(!(is_null($appointDate)) && isset($appointDate)) {
           $query->whereDate('start_date', $appointDate);
        }

        $data = DataTables::of($query)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('slot_details.start_date', $input['order'][0]['dir']);
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

            $message ='Appointment canceled successfully';
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
    public function getTenders(Request $request)
    {
        $input = $request->all();
        $supplierRegId =  self::getSupplierRegIdByUUID($request->input('supplier_uuid')); 
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }  

        if($request->input('extra.tender_status') == 1){ 
            $query = TenderMaster::with(['currency','srmTenderMasterSupplier'=> function($q) use ($supplierRegId){ 
                $q->where('purchased_by','=',$supplierRegId); 
            }])->whereDoesntHave('srmTenderMasterSupplier', function($q) use ($supplierRegId){ 
                $q->where('purchased_by','=',$supplierRegId);
            })
            ->where('published_yn',1); 
            
        }else if ($request->input('extra.tender_status') == 2) {  
            $query = TenderMaster::with(['currency','srmTenderMasterSupplier'=> function($q) use ($supplierRegId){ 
                $q->where('purchased_by','=',$supplierRegId);
            }])->whereHas('srmTenderMasterSupplier', function($q) use ($supplierRegId){ 
                $q->where('purchased_by','=',$supplierRegId); 
            })
            ->where('published_yn',1);  
         } 
        $search = $request->input('search.value');
        if($search){ 
             $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
                $query->orWhere('description_sec_lang', 'LIKE', "%{$search}%");
                $query->orWhere('title', 'LIKE', "%{$search}%");
                $query->orWhere('title_sec_lang', 'LIKE', "%{$search}%"); 
            });
        }

        $data = DataTables::eloquent($query)
        ->order(function ($query) use ($input) {
            if (request()->has('order') ) {
                if($input['order'][0]['column'] == 0)
                {
                    $query->orderBy('id', $input['order'][0]['dir']);
                }
            }
        })
        ->addIndexColumn()
        ->with('orderCondition', $sort)
        ->addColumn('Actions', 'Actions', "Actions") 
        ->make(true);  

        return [
            'success' => true,
            'message' => 'Tender list successfully get',
            'data' => $data
        ];
    }
    public function saveTenderPurchase(Request $request){ 
        $supplierRegId =  self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $tenderMasterId = $request->input('extra.tenderId');  
        $currentDate = Carbon::parse(now())->format('Y-m-d H:i:s');
        DB::beginTransaction();
        try { 
            $data['tender_master_id'] = $tenderMasterId;
            $data['purchased_date'] = $currentDate;
            $data['purchased_by'] = $supplierRegId;
            $data['created_by'] = $supplierRegId;
            DB::commit();
            $tenderMasterSupplier = TenderMasterSupplier::create($data);
            return [
                'success' => true,
                'message' => 'Tender Purchase successfully',
                'data' => $tenderMasterSupplier
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'success'   => false,
                'message'   => 'Tender Purchase failed',
                'data'      => $exception->getMessage()
            ];
        }
 
    }
    public static function getSupplierRegIdByUUID($uuid)
    {

        if ($uuid) {
            $supplier = SupplierRegistrationLink::where('uuid', $uuid) 
                ->first();

            if (!empty($supplier)) {
                return $supplier->id;
            }
        } 
        return 0;
    }

    public function getFaqList(Request $request)
    {
        $input = $request->all();
        $tenderId = $input['extra']['tenderId'];
        try{
            $query = TenderFaq::select('id','question','answer')->where('tender_master_id', $tenderId)->get();

            return [
                'success' => true,
                'message' => 'FAQ list successfully get',
                'data' => $query
            ];
        } catch (\Exception $exception){
            return [
                'success' => false,
                'message' => 'FAQ list failed get',
                'data' => $exception
            ];
        }

    }

    public function saveTenderPrebidClarification(Request $request){
        $prebidId = $request->input('extra.preBidId');
        $postAnonymous = $request->input('extra.postAnonymous');
        if(!isset($postAnonymous)){
            $postAnonymous = 0;
        }

        if($prebidId !== 0){
           return $this->updatePreBid($request, $prebidId);
        } else {$attachment = $request->input('extra.attachment');
            $supplierRegId =  self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
            $tenderMasterId = $request->input('extra.tenderId');
            $currentDate = Carbon::parse(now())->format('Y-m-d H:i:s');
            $tenderMaster = TenderMaster::find($tenderMasterId);
            $companySystemID = $tenderMaster['company_id'];
            $company = Company::where('companySystemID', $companySystemID)->first();
            $documentCode = DocumentMaster::where('documentSystemID', 109)->first();

            DB::beginTransaction();
            try {
                $data['tender_master_id'] = $tenderMasterId;
                $data['posted_by_type'] = 0;
                $data['post'] = $request->input('extra.question');
                $data['user_id'] = $request->input('extra.user_id');
                $data['supplier_id'] = $supplierRegId;
                $data['is_public'] = $request->input('extra.publish');
                $data['parent_id'] = $request->input('extra.parent_id');
                $data['created_by'] = $supplierRegId;
                $data['created_at'] = $currentDate;
                $data['document_system_id'] = $documentCode->documentSystemID;
                $data['document_id'] = $documentCode->documentID;
                $data['is_anonymous'] = $postAnonymous;
                $tenderPrebidClarification = TenderBidClarifications::create($data);

                if (isset($attachment) && !empty($attachment)) {
                    $this->uploadAttachment($attachment, $companySystemID, $company, $documentCode, $tenderPrebidClarification->id);
                }
                DB::commit();

                return [
                    'success' => true,
                    'message' => 'Tender Pre-bid Clarification successfully',
                    'data' => $tenderPrebidClarification
                ];
            } catch (\Exception $exception) {
                DB::rollBack();
                return [
                    'success'   => false,
                    'message'   => 'Tender Pre-bid Clarification failed',
                    'data'      => $exception->getMessage()
                ];
            }
        }
    }

    public function getPrebidClarificationList(Request $request)
    {
        $input = $request->all();
        $extra = $input['extra'];
        $supplierRegId =  0;
        $SearchText = "";
        if(isset($extra['SearchText'])){
            $SearchText = $extra['SearchText'];
        }

        if(isset($extra['isMyClarification']) && $extra['isMyClarification'] == true){
            $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        }

        try{
            $data = TenderMaster::with(['tenderPreBidClarification' => function ($q) use ($SearchText, $supplierRegId) {
                $q->with('attachment');
                $q->where('parent_id', 0);
                if(!empty($SearchText)){
                    $searchText = str_replace("\\", "\\\\", $SearchText);
                    $q->where('post', 'LIKE', "%{$SearchText}%");
                }

                if($supplierRegId != 0){
                    $q->where('supplier_id', $supplierRegId);
                }
                $q->with(['supplier']);
            }]);
               $data = $data->whereHas('tenderPreBidClarification', function ($q) {
                    $q->where('parent_id', 0);
                })->where('id', $extra['tenderId']);

            $data = $data->get();

            return [
                'success' => true,
                'message' => 'Pre-bid Clarification list successfully get',
                'data' => $data
            ];
        } catch (\Exception $exception){
            return [
                'success' => false,
                'message' => 'Pre-bid Clarification list failed get',
                'data' => $exception
            ];
        }
    }

    public function getPreBidClarificationsResponse(Request $request)
    {
        $id = $request->input('extra.prebidId');
        $employeeId = Helper::getEmployeeSystemID();

        $data['response'] = TenderBidClarifications::with(['supplier', 'employee' => function ($q) {
            $q->with(['profilepic']);
        },'attachment'])
            ->where('id', '=', $id)
            ->orWhere('parent_id', '=', $id)
            ->orderBy('parent_id', 'asc')
            ->get();
        $profilePic = Employee::with(['profilepic'])
            ->where('employeeSystemID', $employeeId)
            ->first();
        $data['profilePic'] = $profilePic['profilepic']['profile_image_url'];
        
        return [
            'success' => true,
            'message' => 'Pre-bid response successfully get',
            'data' => $data
        ];
    }

    public function getPreBidClarification(Request $request)
    {
        $id = $request->input('extra.prebidId');

        $data = TenderBidClarifications::with(['supplier', 'employee' => function ($q) {
            $q->with(['profilepic']);
        },'attachments'])
            ->where('id', '=', $id)
            ->first();

        return [
            'success' => true,
            'message' => 'Pre-bid clarification successfully get',
            'data' => $data
        ];
    }

    public function createClarificationResponse(Request $request)
    {
        $attachment = $request->input('extra.attachment');
        $employeeId = Helper::getEmployeeSystemID();
        $response = $request->input('extra.response');
        $id = $request->input('extra.parent_id');
        $tenderParentPost = TenderBidClarifications::where('id', $id)->first();
        $tenderMaster = TenderMaster::find($tenderParentPost['tender_master_id']);
        $companySystemID = $tenderMaster['company_id'];
        $company = Company::where('companySystemID', $companySystemID)->first();
        $documentCode = DocumentMaster::where('documentSystemID', 109)->first();
        $supplierRegId =  self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $updateRecordId = $request->input('extra.updateRecordId');
        if( $updateRecordId !== 0 ){
           return $this->updatePreBidResponse($request, $updateRecordId, $companySystemID, $company);
        }
        DB::beginTransaction();
        try {
            $data['tender_master_id'] = $tenderParentPost['tender_master_id'];
            $data['posted_by_type'] = 0;
            $data['post'] = $response;
            $data['user_id'] = $employeeId;
            $data['supplier_id'] = $supplierRegId;
            $data['is_public'] = 1;
            $data['parent_id'] = $id;
            $data['created_by'] = $employeeId;
            $data['company_id'] = $company->companySystemID;
            $data['document_system_id'] = $documentCode->documentSystemID;
            $data['document_id'] = $documentCode->documentID;
            $result = TenderBidClarifications::create($data);
            if (isset($attachment) && !empty($attachment)) {
                $this->uploadAttachment($attachment, $companySystemID, $company, $documentCode, $result->id);
            }

            if ($result) {
                $updateRec['is_answered'] = 0;
                $result =  TenderBidClarifications::where('id', $id)
                    ->update($updateRec);
                DB::commit();
                return ['success' => true, 'message' => 'Successfully saved', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e);
            return ['success' => false, 'message' => $e];
        }
    }

    public function uploadAttachment($attachments, $companySystemID, $company, $documentCode, $id)
    {
        foreach ($attachments as $attachment) {
            if (!empty($attachment) && isset($attachment['file'])) {
                $extension = $attachment['fileType'];
                $allowExtensions = ['png', 'jpg', 'jpeg', 'pdf', 'txt', 'xlsx'];

                if (!in_array(strtolower($extension), $allowExtensions)) {
                    return $this->sendError('This type of file not allow to upload.', 500);
                }

                if (isset($attachment['size'])) {
                    if ($attachment['size'] > 2097152) {
                        return $this->sendError("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.", 500);
                    }
                }
                $file = $attachment['file'];
                $decodeFile = base64_decode($file);
                $attch = time() . '_PreBidClarificationCompany.' . $extension;
                $path = $companySystemID . '/PreBidClarification/' . $attch;
                Storage::disk('s3')->put($path, $decodeFile);

                $att['companySystemID'] = $companySystemID;
                $att['companyID'] = $company->CompanyID;
                $att['documentSystemID'] = $documentCode->documentSystemID;
                $att['documentID'] = $documentCode->documentID;
                $att['documentSystemCode'] = $id;
                $att['attachmentDescription'] = 'Pre-Bid Clarification ' . time();
                $att['path'] = $path;
                $att['originalFileName'] = $attachment['originalFileName'];
                $att['myFileName'] = $company->CompanyID . '_' . time() . '_PreBidClarification.' . $extension;
                $att['sizeInKbs'] = $attachment['sizeInKbs'];
                $att['isUploaded'] = 1;
                DocumentAttachments::create($att);
            } else {
                Log::info("NO ATTACHMENT");
            }
        }

    }

    public function uploadAppointmentAttachment($request)
    {
        $attachment = $request->input('extra.attachment');
        $companySystemID = $request->input('extra.slotCompanyId');
        $appointmentID = $request->input('extra.appointmentID');
        $description = $request->input('extra.description');
        $company = Company::where('companySystemID', $companySystemID)->first();
        $documentCode = DocumentMaster::where('documentSystemID', 106)->first();
        try {
            if (!empty($attachment) && isset($attachment['file'])) {
                $extension = $attachment['fileType'];
                $allowExtensions = ['png', 'jpg', 'jpeg', 'pdf', 'txt', 'xlsx'];

                if (!in_array(strtolower($extension), $allowExtensions)) {
                    return $this->sendError('This type of file not allow to upload.', 500);
                }

                if (isset($attachment['size'])) {
                    if ($attachment['size'] > 2097152) {
                        return $this->sendError("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.", 500);
                    }
                }
                $file = $attachment['file'];
                $decodeFile = base64_decode($file);
                $attachmentNameWithExtension = time() . '_DeliveryAppointment.' . $extension;
                $path = $company->CompanyID . '/PO/' . $appointmentID . '/' . $attachmentNameWithExtension;
                Storage::disk('s3')->put($path, $decodeFile);

                $att['companySystemID'] = $companySystemID;
                $att['companyID'] = $company->CompanyID;
                $att['documentSystemID'] = $documentCode->documentSystemID;
                $att['documentID'] = $documentCode->documentID;
                $att['documentSystemCode'] = $appointmentID;
                $att['attachmentDescription'] = $description;
                $att['path'] = $path;
                $att['originalFileName'] = $attachment['originalFileName'];
                $att['myFileName'] = $company->CompanyID . '_' . time() . '_DeliveryAppointment.' . $extension;
                $att['attachmentType'] = $extension;
                $att['sizeInKbs'] = $attachment['sizeInKbs'];
                $att['isUploaded'] = 1;
                $result = DocumentAttachments::create($att);
                if ($result) {
                    return ['success' => true, 'message' => 'Successfully uploaded', 'data' => $result];
                }
            } else {
                Log::info("NO ATTACHMENT");
            }
        }catch (\Exception $e){
            return [
                'success'   => false,
                'message'   => $e,
                'data'      => ''
            ];
        }
    }

    public function updatePreBid(Request $request, $prebidId)
    {
        $input = $request->all();
        $question = $request->input('extra.question');
        $isDeleted = $request->input('extra.isDeleted');
        $companySystemID = 1;
        $company = 1;
        $documentCode = DocumentMaster::where('documentSystemID', 109)->first();
        DB::beginTransaction();
        try {
            $data['post'] = $question;
            $data['is_public'] = $request->input('extra.publish');
            $data['is_anonymous'] = $request->input('extra.postAnonymous');
            $status = $this->tenderBidClarificationsRepository->update($data, $prebidId);

            $isAttachmentExist = DocumentAttachments::where('documentSystemID', 109)
                ->where('documentSystemCode', $prebidId)
                ->count();

            if ($isAttachmentExist > 0 && isset($isDeleted) && $isDeleted == 1) {
                DocumentAttachments::where('documentSystemID', 109)
                    ->where('documentSystemCode', $prebidId)
                    ->delete();
            }

            if (!empty($attachment) && isset($attachment['file'])) {
                $attachment = $input['Attachment'];
                $this->uploadAttachment($attachment, $companySystemID, $company, $documentCode, $input['id']);
            }

            DB::commit();
            return ['success' => true, 'data' => $status, 'message' => 'Successfully updated'];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }

    public function updatePreBidResponse(Request $request, $prebidId, $companySystemID, $company)
    {
        $input = $request->all();
        $question = $request->input('extra.response');
        $documentCode = DocumentMaster::where('documentSystemID', 109)->first();
        DB::beginTransaction();
        try {
            $data['post'] = $question;
            $status = $this->tenderBidClarificationsRepository->update($data, $prebidId);

            $isAttachmentExist = DocumentAttachments::where('documentSystemID', 109)
                ->where('documentSystemCode', $prebidId)
                ->count();

            if ($isAttachmentExist > 0 && $input['isDeleted'] == 1) {
                DocumentAttachments::where('documentSystemID', 109)
                    ->where('documentSystemCode', $prebidId)
                    ->delete();
            }

            if (!empty($attachment) && isset($attachment['file'])) {
                $attachment = $input['Attachment'];
                $this->uploadAttachment($attachment, $companySystemID, $company, $documentCode, $input['id']);
            }

            DB::commit();
            return ['success' => true, 'data' => $status, 'message' => 'Successfully updated'];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }

    public function getDeliveryAppointmentAttachment($request)
    {
        $appointmentID = $request->input('extra.appointmentID');

        $data = DocumentAttachments::where('documentSystemID', 106)
            ->where('documentSystemCode', $appointmentID)
            ->get();

        return [
            'success' => true,
            'message' => 'Delivery Appointment successfully get',
            'data' => $data
        ];
    }

    public function removeDeliveryAppointmentAttachment($request)
    {
        $attachmentID = $request->input('extra.attachmentID');

        $data = DocumentAttachments::where('attachmentID', $attachmentID)
            ->delete();

        return [
            'success' => true,
            'message' => 'Attachment successfully deleted',
            'data' => $data
        ];
    }

    public function removePreBidClarificationResponse($request)
    {
        $id = $request->input('extra.id');
        DB::beginTransaction();
        try{
            $status = TenderBidClarifications::where('id', $id)
                ->delete();

            DB::commit();
            return ['success' => true, 'data' => $status, 'message' => 'Successfully deleted'];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }

}
