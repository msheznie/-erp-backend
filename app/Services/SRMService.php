<?php

namespace App\Services;

use App\helper\CreateExcel;
use App\helper\Helper;
use App\Http\Controllers\API\DocumentAttachmentsAPIController;
use App\Models\Appointment;
use App\Models\AppointmentDetails;
use App\Models\AppointmentDetailsRefferedBack;
use App\Models\AppointmentRefferedBack;
use App\Models\BidBoq;
use App\Models\BidMainWork;
use App\Models\BidSchedule;
use App\Models\BidSubmissionDetail;
use App\Models\BidSubmissionMaster;
use App\Models\CircularSuppliers;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyPolicyMaster;
use App\Models\CountryMaster;
use App\Models\CurrencyMaster;
use App\Models\DirectInvoiceDetails;
use App\Models\DocumentApproved;
use App\Models\DocumentAttachments;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\Employee;
use App\Models\EmployeesDepartment;
use App\Models\EvaluationCriteriaDetails;
use App\Models\EvaluationCriteriaScoreConfig;
use App\Models\PricingScheduleMaster;
use App\Models\ProcumentOrder;
use App\Models\ScheduleBidSubmission;
use App\Models\SlotDetails;
use App\Models\SlotMaster;
use App\Models\PurchaseOrderDetails;
use App\Models\SRMSupplierValues;
use App\Models\SupplierCategoryMaster;
use App\Models\SupplierCategorySub;
use App\Models\SupplierContactType;
use App\Models\SupplierMaster;
use App\Models\SupplierRegistrationLink;
use App\Models\SupplierTenderNegotiation;
use App\Models\TenderBidClarifications;
use App\Models\TenderBidNegotiation;
use App\Models\TenderBoqItems;
use App\Models\TenderCirculars;
use App\Models\TenderDocumentTypes;
use App\Models\TenderFaq;
use App\Models\TenderMainWorks;
use App\Models\TenderMaster;
use App\Models\TenderMasterSupplier;
use App\Models\TenderNegotiation;
use App\Models\TenderNegotiationArea;
use App\Models\TenderSupplierAssignee;
use App\Models\WarehouseMaster;
use App\Models\BookInvSuppMaster;
use App\Repositories\DocumentAttachmentsRepository;
use App\Repositories\SupplierInvoiceItemDetailRepository;
use App\Repositories\TenderBidClarificationsRepository;
use App\Services\Shared\SharedService;
use Aws\Ec2\Exception\Ec2Exception;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use stdClass;
use Throwable;
use Webpatser\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;
use function Clue\StreamFilter\fun;
use App\Models\TenderDocumentTypeAssign;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;
use App\Models\PricingScheduleDetail;
use App\Models\ScheduleBidFormatDetails;
use App\helper\PirceBidFormula;
use App\Models\BidDocumentVerification;
use App\Models\PaySupplierInvoiceMaster;
use App\Jobs\DeliveryAppointmentInvoice;
use App\Repositories\BookInvSuppMasterRepository;
use App\Repositories\PaySupplierInvoiceMasterRepository;
use App\Models\GRVDetails;
use App\Models\SupplierInvoiceItemDetail;
class SRMService
{
    private $POService = null;
    private $supplierService = null;
    private $sharedService = null;
    private $invoiceService = null;
    private $supplierInvoiceItemDetailRepository;
    private $tenderBidClarificationsRepository;
    private $documentAttachmentsRepo;
    private $bookInvSuppMasterRepository;
    private $paySupplierInvoiceMasterRepository;

    public function __construct(
        BookInvSuppMasterRepository $bookInvSuppMasterRepository,
        POService                           $POService,
        SupplierService                     $supplierService,
        SharedService                       $sharedService,
        InvoiceService                      $invoiceService,
        SupplierInvoiceItemDetailRepository $supplierInvoiceItemDetailRepo,
        TenderBidClarificationsRepository   $tenderBidClarificationsRepo,
        DocumentAttachmentsRepository       $documentAttachmentsRepo,
        PaySupplierInvoiceMasterRepository  $paySupplierInvoiceMasterRepository
    ) {
        $this->POService = $POService;
        $this->supplierService = $supplierService;
        $this->sharedService = $sharedService;
        $this->invoiceService = $invoiceService;
        $this->supplierInvoiceItemDetailRepository = $supplierInvoiceItemDetailRepo;
        $this->tenderBidClarificationsRepository = $tenderBidClarificationsRepo;
        $this->documentAttachmentsRepo = $documentAttachmentsRepo;
        $this->paySupplierInvoiceMasterRepository = $paySupplierInvoiceMasterRepository;
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
            'success' => true,
            'message' => 'currencies successfully get',
            'data' => $data
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
        $data = $this->POService->getPoPrintData($purchaseOrderID);
        return [
            'success' => true,
            'message' => 'Purchase order print data successfully get',
            'data' => $data
        ];
    }

    public function getPoAddons(Request $request)
    {
        $purchaseOrderID = $request->input('extra.purchaseOrderID');
        $data = $this->POService->getPoAddons($purchaseOrderID);
        return [
            'success' => true,
            'message' => 'Purchase order addon successfully get',
            'data' => $data
        ];
    }

    public function getPurchaseOrders(Request $request)
    {
        $tenantID = $request->input('tenantId');
        $wareHouseID = $request->input('extra.wareHouseID');
        $searchText = $request->input('extra.searchText');
        $supplierID = self::getSupplierIdByUUID($request->input('supplier_uuid'));
        $poData = [];
        $data = $this->POService->getPurchaseOrders($wareHouseID, $supplierID, $tenantID, $searchText);

        return [
            'success' => true,
            'message' => 'Purchase Orders successfully get',
            'data' => $data
        ];
    }

    public function SavePurchaseOrderList(Request $request)
    {
        $tenantID = $request->input('tenantId');
        $data = $request->input('extra.purchaseOrders');
        $slotDetailID = $request->input('extra.slotDetailID');
        $slotCompanyId = $request->input('extra.slotCompanyId');
        $company = Company::where('companySystemID', $slotCompanyId)->first();
        $supplierID = self::getSupplierIdByUUID($request->input('supplier_uuid'));
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
            $code = ($document['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
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
                    $data_details['expiry_date'] = isset($val['expiry_date']) ? (new Carbon($val['expiry_date']))->format('Y-m-d') : null;
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
                'success' => true,
                'message' => 'Appointment saved successfully',
                'data' => $data
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Appointment save failed',
                'data' => $exception->getMessage()
            ];
        }
    }

    public function getSupplierInvitationInfo(Request $request)
    {
        $invitationToken = $request->input('extra.token');
        $data = $this->supplierService->checkValidTokenData($invitationToken);

        if ($data == 1) {
            return [
                'success' => false,
                'message' => "Sorry, This link has already been expired",
                'data' => null
            ];
        } else if ($data == 2) {
            return [
                'success' => false,
                'message' => "Sorry, This link has already been used",
                'data' => null
            ];
        } else {
            $dataSupplier = $this->supplierService->getTokenData($invitationToken);
            return [
                'success' => true,
                'message' => 'Valid Invitation Link',
                'data' => $dataSupplier
            ];
        }
    }

    public function updateSupplierInvitation(Request $request)
    {
        $invitationToken = $request->input('extra.token');
        $supplierUuid = $request->input('supplier_uuid');
        $name = $request->input('extra.name');
        $email = $request->input('extra.email');

        $isUpdated = $this->supplierService->updateTokenStatus($invitationToken, $supplierUuid,$name,$email);

        if (!$isUpdated) {
            return [
                'success' => false,
                'message' => "Update Failed",
                'data' => null
            ];
        }

        return [
            'success' => true,
            'message' => 'Updated Successfully',
            'data' => $isUpdated
        ];
    }

    public function getAppointmentSlots(Request $request)
    {
        $tenantID = $request->input('tenantId');
        $data = $this->POService->getAppointmentSlots($tenantID);
        $supplierID = self::getSupplierIdByUUID($request->input('supplier_uuid'));
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
                    $arr[$x]['title'] = date("h:i A", strtotime($slotDetail->start_date)) . '-' . date("h:i A", strtotime($slotDetail->end_date)) . ' ' . $row->ware_house->wareHouseDescription;
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
                'success' => true,
                'message' => 'Calander appointment slots successfully get',
                'data' => $arr
            ];
        }
    }

    public function getAppointmentDeliveries(Request $request)
    {
        $slotDetailID = $request->input('extra.slotDetailID');
        $slotMasterID = $request->input('extra.slotMasterID');
        $supplierID = self::getSupplierIdByUUID($request->input('supplier_uuid'));
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
            $query->with(['getPoMaster', 'getPoDetails' => function ($query) {
                $query->with(['unit', 'appointmentDetails' => function ($q) {
                    $q->whereHas('appointment', function ($q) {
                        $q->where('refferedBackYN', '!=', -1);
                        /*$q->where('confirmed_yn', 1);*/
                    })->groupBy('po_detail_id')
                        ->select('id', 'appointment_id', 'qty', 'po_detail_id')
                        ->selectRaw('sum(qty) as qty');
                }]);
            }]);
        }, 'created_by','grv','invoice'])
            ->where('slot_detail_id', $slotDetailID)
            ->where('created_by', $supplierID)
            ->get();
        $arr['data'] = $data;
        return [
            'success' => true,
            'message' => 'Calander appointment deliveries get',
            'data' => $arr
        ];
    }

    public function getPoAppointments(Request $request)
    {
        $appointmentID = $request->input('extra.appointmentID');

        $data = Appointment::with(['detail' => function ($q) {
            $q->with(['getPoDetails' => function ($q1) {
                $q1->with(['order', 'unit', 'order.transactioncurrency']);
            }]);
        }])
            ->where('id', $appointmentID)->first();

        return [
            'success' => true,
            'message' => 'Calander appointment get',
            'data' => $data
        ];
    }

    public function deleteSupplierAppointment(Request $request)
    {
        $appointmentID = $request->input('extra.id');
        $slotMasterID = $request->input('extra.slotMasterID');
        $slotDetailID = $request->input('extra.slotDetailID');
        $appointment = Appointment::where('id', $appointmentID)->delete();
        if ($appointment) {
            $appointmentDetail = AppointmentDetails::where('appointment_id', $appointmentID)->delete();
        }
        $slotMaster = SlotMaster::select('no_of_deliveries')->where('id', $slotMasterID)->first();
        $slotDetailAppointment = Appointment::select('id')->where('slot_detail_id', $slotDetailID)->get();

        $data['AvailabelDeliveries'] = $slotMaster['no_of_deliveries'] - sizeof($slotDetailAppointment);
        if (sizeof($slotDetailAppointment) == 0) {
            $slotData['status'] = 0;
            SlotDetails::where('id', $slotDetailID)->update($slotData);
        }


        return [
            'success' => true,
            'message' => 'Appointment deleted successfully',
            'data' => $data
        ];
    }

    public function confirmSupplierAppointment(Request $request)
    {
        $params = array('autoID' => $request->input('extra.data.id'), 'company' => $request->input('extra.data.company_id'), 'document' => $request->input('extra.data.document_system_id'), 'email' => $request->input('extra.email'),);
        $confirm = \Helper::confirmDocument($params);

        return [
            'success' => $confirm['success'],
            'message' => $confirm['message'],
            'data' => $params
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

        $validateSupplierEmail = $this->validateSupplierEmail($request);

        if(!$validateSupplierEmail['status']){
            return [
                'success' => false,
                'message' => $validateSupplierEmail['message'],
                'data' => []
            ];
        }

        $supplierLink = SupplierRegistrationLink::where('uuid', $request->input('supplier_uuid'))->first();

        throw_unless($supplierLink, "Something went wrong, UUID doesn't match with ERP supplier link table reocrd");
        $userEmail = $request->input('extra.data.supplierUserEmail');
        $name = $request->input('extra.data.supplierName');


        $data = $this->supplierService->createSupplierApprovalSetup([
            'autoID' => $supplierLink->id,
            'company' => $supplierLink->company_id,
            'documentID' => 107, // 107 mean documentMaster id of "Supplier Registration" document in ERP
            'email' => $supplierLink->email
        ]);

        if($data['success']){
            $conditions = [
                'uuid' => $request->input('supplier_uuid'),
                'company_id' => $supplierLink->company_id,
                'supplier_id' => $supplierLink->id,
            ];

            $updates = [
                'user_name' => $userEmail,
                'name' => $name,
            ];

            SRMSupplierValues::customCreateOrUpdate($conditions, $updates);
        }

        return [
            'success' => $data['success'],
            'message' => $data['message'],
            'data' => $data
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
                'api_key' => $apiKey,
                'request' => $data['request'],
                'auth' => $data['auth'],
                'extra' => $data['extra'] ?? null,
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
                'api_key' => $data['apiKey'],
                'request' => $data['request'],
                'extra' => $data['extra'] ?? null
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
            'success' => true,
            'message' => 'Record retrieved successfully',
            'data' => $this->invoiceService->getInvoicesList($request, $supplierID)
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
        $typeId = (int) $request->input('extra.typeId');
        $masterData = $this->invoiceService->getInvoiceDetailsById($id, $supplierID);

        switch($typeId) {
            case 0:
                if (!empty($masterData)) {
                    $masterData = $masterData->toArray();
                    $input['bookingSuppMasInvAutoID'] = $id;
                    $masterData['detail_data'] = ['grvDetails' => [], 'logisticYN' => 0];

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
                    'success' => true,
                    'message' => 'Record retrieved successfully',
                    'data' => $masterData
                ];

                break;
            default:
                return [
                    'success' => false,
                    'message' => 'No records found',
                    'data' => []
                ];
            break;
        }



    }

    private function amendPoAppointment($appointmentID, $slotCompanyId)
    {
        $amendedAppointment = Appointment::where('id', $appointmentID)
            ->select(
                'appointment.id AS appointment_id',
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
            ->select(
                'appointment_details.id AS appointment_details_id',
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

        if ($insertAppointment && $insertAppointmentDetails) {

            $statusChange = Appointment::where('id', $appointmentID)
                ->update([
                    'approved_yn' => 0,
                    'confirmed_yn' => 0,
                    'refferedBackYN' => 0
                ]);

            if ($statusChange) {
                self::poAppointmentReferback($appointmentID, $slotCompanyId);

                return [
                    'success' => true,
                    'message' => 'Appointment amended successfully',
                    'data' => [$insertAppointment, $insertAppointmentDetails]
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Appointment amendment failed',
            'data' => 'failed'
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
        $id = $kycFormDetails->id;
        $isApprovalAmmend = $request->has('approvalAmmend') ? $request->input('approvalAmmend') : 0;
        $companySystemID = $kycFormDetails->company_id;
        $documentSystemID = 107;
        $timesReferred = $kycFormDetails->timesReferred;


        if($isApprovalAmmend == 1){ 
            $update['approved_yn'] = 0;
            SupplierRegistrationLink::where('uuid',$request->input('supplier_uuid'))->update($update);
        }

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
            'success' => true,
            'message' => 'Supplier Ammend',
            'data' => $kycFormDetails
        ];
    }

    public function getERPFormData(Request $request)
    {
        $currencyMaster = CurrencyMaster::select('currencyID', 'CurrencyName', 'CurrencyCode')->get();
        $countryMaster = CountryMaster::select('countryID', 'countryCode', 'countryName')->get();
        $supplierCategoryMaster = SupplierCategoryMaster::select('supCategoryMasterID', 'categoryCode', 'categoryName')->get();
        $supplierCategorySubMaster = SupplierCategorySub::select('supCategorySubID', 'supMasterCategoryID', 'subCategoryCode', 'categoryName')->get();
        $supplierContactType = SupplierContactType::select('supplierContactTypeID', 'supplierContactDescription')->get();
        $formData = array(
            'currencyMaster' => $currencyMaster,
            'countryMaster' => $countryMaster,
            'supplierCategoryMaster' => $supplierCategoryMaster,
            'supplierCategorySubMaster' => $supplierCategorySubMaster,
            'supplierContactType' => $supplierContactType,
        );

        return [
            'success' => true,
            'message' => 'ERP Form Data Retrieved',
            'data' => $formData
        ];
    }

    public function checkAppointmentPastDate(Request $request)
    {
        $slotDetailID = $request->input('extra.slotDetailID');

        $detail = SlotDetails::where('id', $slotDetailID)->first();

        $appointments = $this->getAppointmentDeliveries($request);
        $appointment = 0;
        if (count($appointments['data']['data']) > 0) {
            $appointment = 1;
        }

        if (!empty($detail)) { //start_date
            $endDate = Carbon::parse($detail['end_date'])->format('Y-m-d H:i:s');
            $currentDate = Carbon::parse(now())->format('Y-m-d H:i:s');
            $result['currentDate'] = $currentDate;
            $result['endDate'] = $endDate;

            $start_date = Carbon::parse($detail['start_date'])->format('Y-m-d');
            $current = Carbon::parse(now())->format('Y-m-d');
            $canCancel = 0;
            if ($start_date > $current) {
                $canCancel = 1;
            }

            if ($endDate > $currentDate) {
                $result['canCreate'] = 1;
                $result['canCancel'] = $canCancel;
                $result['appointments'] = $appointment;
                return [
                    'success' => true,
                    'message' => 'Appointment Can Be Created',
                    'data' => $result
                ];
            } else {
                $result['canCreate'] = 0;
                $result['canCancel'] = $canCancel;
                $result['appointments'] = $appointment;
                return [
                    'success' => true,
                    'message' => 'Appointments can not be created for past dates',
                    'data' => $result
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Slot Detail Not Available',
                'data' => $detail
            ];
        }
    }

    public function getAppointmentDetails(Request $request)
    {
        $appointmentID = $request->input('extra.appointmentID');

        $detail = AppointmentDetails::where('appointment_id', $appointmentID)
            ->with(['getPoMaster.segment', 'getPoMaster.transactioncurrency', 'getPoDetails' => function ($query) use ($appointmentID) {
                $query->with(['unit', 'appointmentDetails' => function ($q) use ($appointmentID) {
                    $q->whereHas('appointment', function ($q) use ($appointmentID) {
                        $q->where('refferedBackYN', '!=', -1);
                        $q->where('cancelYN', 0);
                        if (isset($appointmentID)) {
                            $q->where('id', '!=', $appointmentID);
                        }
                    })->groupBy('po_detail_id')
                        ->select('id', 'appointment_id', 'qty', 'po_detail_id')
                        ->selectRaw('IFNULL(sum(qty),0) as qty');
                }]);
            }, 'appointment.attachment'])->get()
            ->transform(function ($data) {
                return $this->appointmentDetailFormat($data);
            });
        $result['detail'] = $detail;
        $result['purchaseOrderCode'] = '';
        if (count($detail) > 0) {
            $result['exist'] = 1;
            if (!empty($detail[0]['getPoMaster'])) {
                $result['purchaseOrderCode'] = $detail[0]['getPoMaster']['purchaseOrderCode'];
            }
            return [
                'success' => true,
                'message' => 'Appointment Details Available',
                'data' => $result
            ];
        } else {
            $result['exist'] = 0;
            return [
                'success' => false,
                'message' => 'Appointment Details Not Available',
                'data' => $result
            ];
        }
    }

    public function getPurchaseOrderDetails(Request $request)
    {
        $purchaseOrderID = $request->input('extra.purchaseOrderID');
        $appointmentID = $request->input('extra.appointmentID');
        $searchText = $request->input('extra.searchText');
        $segment = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)->with(['segment'=>function($q){
            $q->select('serviceLineSystemID','ServiceLineDes');
        }])->select('purchaseOrderID','serviceLineSystemID')->first();

        $po = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID);

        if (!empty($searchText)) {
            $searchText = str_replace("\\", "\\\\", $searchText);
            $po = $po->where(function ($query) use ($searchText) {
                $query->where('itemDescription', 'LIKE', "%{$searchText}%")
                    ->orWhere('itemPrimaryCode', 'LIKE', "%{$searchText}%");
            });
        }

        $po = $po->with(['order', 'unit', 'appointmentDetails' => function ($q) use ($appointmentID) {
            $q->whereHas('appointment', function ($q) use ($appointmentID) {
                $q->where('refferedBackYN', '!=', -1);
                $q->where('cancelYN', 0);
                if (isset($appointmentID)) {
                    $q->where('id', '!=', $appointmentID);
                }
            })->groupBy('po_detail_id')
                ->select('id', 'appointment_id', 'qty', 'po_detail_id')
                ->selectRaw('IFNULL(sum(qty),0) as qty');
        }, 'order.transactioncurrency'])->get()
            ->transform(function ($data) {
                return $this->poDetailFormat($data);
            });

        $result['poDetail'] = $po;
        $result['segment'] = $segment->segment->ServiceLineDes;
        return [
            'success' => true,
            'message' => 'Po Details Retrieved',
            'data' => $result
        ];
    }

    public function poDetailFormat($data)
    {
        if (count($data['appointmentDetails']) > 0) {
            $sumQty = $data['appointmentDetails'][0]['qty'];
        } else {
            $sumQty = 0;
        }
        return [
            'purchaseOrderCode' => $data['order']['purchaseOrderCode'],
            'purchaseOrderID' => $data['order']['purchaseOrderID'],
            'segment' => $data['order']['serviceLineSystemID'],
            'segment_des' => $data['order']['segment']['ServiceLineDes'],
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

    public function appointmentDetailFormat($data)
    {
        if (count($data['getPoDetails']['appointmentDetails']) > 0) {
            $sumQty = $data['getPoDetails']['appointmentDetails'][0]['qty'];
        } else {
            $sumQty = 0;
        }
        return [
            'purchaseOrderCode' => $data['getPoMaster']['purchaseOrderCode'],
            'purchaseOrderID' => $data['getPoMaster']['purchaseOrderID'],
            'segment' => $data['getPoMaster']['serviceLineSystemID'],
            'segment_des' => $data['getPoMaster']['segment']['ServiceLineDes'],
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
        $input = $request->all();
        $supplierID = self::getSupplierIdByUUID($request->input('supplier_uuid'));
        $warehouseId = $request->input('extra.warehouseId');
        $appointDate = $request->input('extra.appointDate');
        $search = $request->input('search.value');

        $query = DB::table('appointment')
            ->select('*', 'appointment.id as appointmentId', 'appointment.refferedBackYN as appointmentRefferedBackYN', 'appointment.created_at as appointmentCreatedDate', 'suppliermaster.supplierName as appointmentCreatedBy')
            ->join('slot_details', function ($query) {
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


        if ($warehouseId != 0 && !(is_null($warehouseId))) {
            $query->where('wareHouseSystemCode', $warehouseId);
        }

        if (!(is_null($appointDate)) && isset($appointDate)) {
            $query->whereDate('start_date', Carbon::parse($appointDate)->format('Y-m-d'));
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
            'success' => true,
            'message' => 'Appointment list successfully get',
            'data' => $data
        ];
    }

    public function getWarehouse(Request $request)
    {
        try {
            $warehouse = WarehouseMaster::where('isActive', 1)->get();
            $message = 'Warehouse list load successfully';
        } catch (\Exception $e) {
            $message = $e;
        }

        return [
            'success' => true,
            'message' => $message,
            'data' => $warehouse
        ];
    }

    public function getRemainingSlotCount(Request $request)
    {
        $remainingAppointments = 0;
        try {
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
        } catch (\Exception $e) {
            $message = $e;
        }

        return [
            'success' => true,
            'message' => $message,
            'data' => $remainingAppointments
        ];
    }

    public function cancelAppointments(Request $request)
    {
        try {
            $id = $request->input('extra.appointmentID');
            $supplierID = self::getSupplierIdByUUID($request->input('supplier_uuid'));

            $supplier = SupplierMaster::where('supplierCodeSystem', $supplierID)->first();

            $canceledReason = $request->input('extra.canceledReason');
            $Data['cancelYN'] = 1;
            $Data['canceledDate'] = Helper::currentDateTime();
            $Data['canceledByEmpId'] = $supplierID;
            $Data['canceledReason'] = $canceledReason;
            $Data['canceledByName'] = $supplier['supplierName'];
            $result = Appointment::where('id', $id)->update($Data);

            $message = 'Appointment canceled successfully';
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            $message = $e;
            $result = 0;
        }

        return [
            'success' => $success,
            'message' => $message,
            'data' => $result
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
                        'success' => false,
                        'message' => 'Policy not found',
                        'data' => $companyDocument
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
                    ->whereHas('employee', function ($q) {
                        $q->where('discharegedYN', 0);
                    })
                    ->groupBy('employeeSystemID')
                    ->get();
                $value['approval_list'] = $approvalList;
            }
        }

        return [
            'success' => true,
            'message' => 'Record retrieved successfully',
            'data' => $approveDetails
        ];
    }

    public function getTenders(Request $request)
    {
        $input = $request->all();
        $registrationLinkIds = array();
        $tenderMasterId = array();
        $supplierRegId =  self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $supplierRegIdAll =  $this->getAllSupplierRegIdByUUID($request->input('supplier_uuid'));
        $is_rfx = $request->input('extra.rfx');

        foreach ($supplierRegIdAll as $supplierReg) {
            $registrationLinkIds[] = $supplierReg['id'];
        }
        $supplierData =  self::getSupplierData($request->input('supplier_uuid'));

        //Get Purchased tenders for user
        $purchasedTenderIds = TenderMasterSupplier::select(DB::raw('DISTINCT tender_master_id'))->whereIn('purchased_by', $registrationLinkIds)->get()->toArray();

        //Get Assigned tenders for user without purchased tenders
        $tenderIds = TenderSupplierAssignee::select('tender_master_id')
            ->whereIn('registration_link_id', $registrationLinkIds)
            ->whereNotIn('tender_master_id', $purchasedTenderIds)
            ->get()
            ->toArray();

        foreach ($tenderIds as $tenderId) {
            $tenderMasterId[] = $tenderId['tender_master_id'];
        }
        //Get Open Tenders Not Purchased
        if($is_rfx)
        {
            $type = [1,2,3];
        }
        else
        {
            $type = [0];
        }

        $openTendersNotPurchased = TenderMaster::select('id')->where('tender_type_id', 1)
        ->whereNotIn('id', $purchasedTenderIds)
        ->whereIn('document_type',$type)
        ->get()
        ->toArray();


        foreach ($openTendersNotPurchased as $openTendersNotPurchasedId) {
            $tenderMasterId[] = $openTendersNotPurchasedId['id'];
        }

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        if ($request->input('extra.tender_status') == 1) {
            $query = TenderMaster::with(['currency', 'srmTenderMasterSupplier' => function ($q) use ($supplierRegId) {
                $q->where('purchased_by', '=', $supplierRegId);
            }, 'tenderSupplierAssignee'])->whereDoesntHave('srmTenderMasterSupplier', function ($q) use ($supplierRegId) {
                $q->where('purchased_by', '=', $supplierRegId);
            })->whereIn('id', $tenderMasterId)->where('published_yn', 1)->where('final_tender_awarded', 0);
        } else if ($request->input('extra.tender_status') == 2) {

            $negotiatedTenders = TenderNegotiation::select('srm_tender_master_id')
                ->where('status', 2)->whereHas('SupplierTenderNegotiation', function ($q) use ($supplierRegId) {
                    $q->where('suppliermaster_id', $supplierRegId);
                })->get()->pluck('srm_tender_master_id')->toArray();

            $query = TenderMaster::with(['currency', 'tender_negotiation.SupplierTenderNegotiation', 'srm_bid_submission_master' => function ($query) use ($supplierRegId) {
                $query->where('supplier_registration_id', '=', $supplierRegId);
            }, 'srmTenderMasterSupplier' => function ($q) use ($supplierRegId) {
                $q->where('purchased_by', '=', $supplierRegId);
            }])->whereHas('srmTenderMasterSupplier', function ($q) use ($supplierRegId) {
                $q->where('purchased_by', '=', $supplierRegId);
            })->whereNotIn('id', $negotiatedTenders)->where('published_yn', 1)->where('final_tender_awarded', 0);
        } else if ($request->input('extra.tender_status') == 3) {

            $query = TenderMaster::with(['currency', 'tender_negotiation.SupplierTenderNegotiation' => function ($q) use ($supplierRegId) {
                $q->where('suppliermaster_id', $supplierRegId);
            },'srm_bid_submission_master' => function ($query) use ($supplierRegId) {
                    $query->where('supplier_registration_id', '=', $supplierRegId);
            }, 'srmTenderMasterSuppliers' => function ($q) use ($supplierRegId) {
                    $q->where('purchased_by', '=', $supplierRegId);
            }, 'awardedSupplier' => function ($query) use ($supplierRegId) {
                    $query->where('supplier_id', $supplierRegId);
                }])->where(function ($query) {
                $query->where('final_tender_awarded', 1)
                    ->orWhere(function ($query) {
                        $query->where('negotiation_is_awarded', 1)
                            ->where('final_tender_awarded', 1);
                    });
            })->where('published_yn', 1);
    }

            if($is_rfx)
            {
                $type = [1,2,3];
            }
            else
            {
                $type = [0];
            }

            $query->whereIn('document_type',$type);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
                $query->orWhere('description_sec_lang', 'LIKE', "%{$search}%");
                $query->orWhere('title', 'LIKE', "%{$search}%");
                $query->orWhere('title_sec_lang', 'LIKE', "%{$search}%");
            });
        }

        if($is_rfx)
        {
            if($request->input('extra.rfx_typ') != '')
            {
              $query->where('document_type', $request->input('extra.rfx_typ'));
            }
        }


        $data = DataTables::eloquent($query)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
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

    public function getNegotiationTenders(Request $request)
    {
        $input = $request->all();
        $supplierRegId =  self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $is_rfx = $request->input('extra.rfx');

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

       if ($request->input('extra.tender_status') == 2) {
            $query = TenderMaster::with(['currency', 'tender_negotiation.SupplierTenderNegotiation' => function ($q) use ($supplierRegId) {
               $q->where('suppliermaster_id', $supplierRegId);
           }, 'srm_bid_submission_master' => function ($query) use ($supplierRegId) {
                $query->with('SupplierTenderNegotiation')->where('supplier_registration_id', '=', $supplierRegId);
            }, 'srmTenderMasterSupplier' => function ($q) use ($supplierRegId) {
                $q->where('purchased_by', '=', $supplierRegId);
            }])->whereHas('srmTenderMasterSupplier', function ($q) use ($supplierRegId) {
                $q->where('purchased_by', '=', $supplierRegId);
            })->whereHas('tender_negotiation', function ($q) use ($supplierRegId) {
                $q->where('status', '=', 2);
            })->whereHas('tender_negotiation.SupplierTenderNegotiation', function ($q) use ($supplierRegId) {
                $q->where('suppliermaster_id', $supplierRegId);
            })->where('published_yn', 1)->where('final_tender_awarded', '!=', 1);
        }

        $type = $is_rfx ? [1, 2, 3] : [0];

        $query->whereIn('document_type',$type);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
                $query->orWhere('description_sec_lang', 'LIKE', "%{$search}%");
                $query->orWhere('title', 'LIKE', "%{$search}%");
                $query->orWhere('title_sec_lang', 'LIKE', "%{$search}%");
            });
        }

        if($is_rfx && !empty($is_rfx))
        {
            if($request->input('extra.rfx_typ') != '')
            {
              $query->where('document_type', $request->input('extra.rfx_typ'));
            }
        }

        $data = DataTables::eloquent($query)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
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
            'message' => 'Tender Negotiation list successfully get',
            'data' => $data
        ];
    }

    public function saveTenderPurchase(Request $request)
    {
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
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
                'success' => false,
                'message' => 'Tender Purchase failed',
                'data' => $exception->getMessage()
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

    public static function getAllSupplierRegIdByUUID($uuid)
    {
        if ($uuid) {
            $supplierResult = SupplierRegistrationLink::select('id')->where('uuid', $uuid)->get()->toArray();

            if (!empty($supplierResult)) {
                return $supplierResult;
            }
        }
        return array();
    }


    public function getFaqList(Request $request)
    {
        $input = $request->all();
        $tenderId = $input['extra']['tenderId'];
        $SearchText = "";
        if (isset($input['extra']['SearchText'])) {
            $SearchText = $input['extra']['SearchText'];
        }
        try {
            $queryRecordsCount = TenderFaq::where('tender_master_id', $tenderId)->firstOrFail()->toArray();
            if (sizeof($queryRecordsCount)) {
                $result = TenderFaq::select('id', 'question', 'answer')
                    ->where('tender_master_id', $tenderId);
                if (!empty($SearchText)) {
                    $SearchText = str_replace("\\", "\\\\", $SearchText);
                    $result = $result->where(function ($query) use ($SearchText) {
                        $query->where('answer', 'LIKE', "%{$SearchText}%");
                        $query->orWhere('question', 'LIKE', "%{$SearchText}%");
                    });
                }

                if(sizeof($result->get()) > 0 ){
                    return [
                        'success' => true,
                        'message' => 'FAQ list successfully get',
                        'data' => $result->get()
                    ];
                } else {
                    return [
                        'success' => true,
                        'message' => 'No records found',
                        'data' => new stdClass()
                    ];
                }
            } else {
                return [
                    'success' => true,
                    'message' => 'No records found',
                    'data' => ''
                ];
            }
        } catch (\RuntimeException $exception) {
            return [
                'success' => true,
                'message' => 'FAQ list failed get',
                'data' => $exception
            ];
        }
    }

    public function saveTenderPrebidClarification(Request $request)
    {
        $prebidId = $request->input('extra.preBidId');
        $postAnonymous = $request->input('extra.postAnonymous');
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $tenderMasterId = $request->input('extra.tenderId');
        $currentDate = Carbon::parse(now())->format('Y-m-d H:i:s');
        $tenderMaster = TenderMaster::find($tenderMasterId);
        $companySystemID = $tenderMaster['company_id'];
        $company = Company::where('companySystemID', $companySystemID)->first();
        if (!isset($postAnonymous)) {
            $postAnonymous = 0;
        }

        if ($prebidId !== 0) {
            return $this->updatePreBid($request, $prebidId, $company, $companySystemID);
        } else {
            $attachment = $request->input('extra.attachment');
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
                    try {
                        $this->uploadAttachment($attachment, $companySystemID, $company, $documentCode, $tenderPrebidClarification->id);
                    } catch (Exception $exception) {
                        if ($exception->getCode() == 500) {
                            DB::rollBack();
                            return [
                                'success' => false,
                                'message' => $exception->getMessage(),
                                'data' => $exception->getMessage()
                            ];
                        }
                    }
                }
                DB::commit();

                return [
                    'success' => true,
                    'message' => 'Pre-bid clarification created successfully',
                    'data' => $tenderPrebidClarification
                ];
            } catch (\Exception $exception) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Pre-bid clarification failed',
                    'data' => $exception->getMessage()
                ];
            }
        }
    }

    public function getPrebidClarificationList(Request $request)
    {
        $input = $request->all();
        $extra = $input['extra'];
        $supplierRegId = 0;
        $SearchText = "";
        if (isset($extra['SearchText'])) {
            $SearchText = $extra['SearchText'];
        }

        if (isset($extra['isMyClarification']) && $extra['isMyClarification'] == true) {
            $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        }

        try {
            $data = TenderMaster::with(['tenderPreBidClarification' => function ($q) use ($SearchText, $supplierRegId) {
                $q->with('attachment');
                $q->where('parent_id', 0);
                if (!empty($SearchText)) {
                    $SearchText = str_replace("\\", "\\\\", $SearchText);
                    $q->where('post', 'LIKE', "%{$SearchText}%");
                }

                if ($supplierRegId != 0) {
                    $q->where('supplier_id', $supplierRegId);
                }
                $q->with(['supplier']);
                $q->with(['replies']);
            }]);
            $data = $data->whereHas('tenderPreBidClarification', function ($q) {
                $q->where('parent_id', 0);
            })->where('id', $extra['tenderId']);

            $data = $data->get();

            $getDates = TenderMaster::select('pre_bid_clarification_start_date', 'pre_bid_clarification_end_date')->where('id', $extra['tenderId'])->get();

            $data = [
                'data' => $data,
                'dates' => $getDates,
                'supplier_id' => self::getSupplierRegIdByUUID($request->input('supplier_uuid')),
            ];

            return [
                'success' => true,
                'message' => 'Pre-bid Clarification list successfully get',
                'data' => $data
            ];
        } catch (\Exception $exception) {
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
        }, 'attachments'])
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
        }, 'attachments'])
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
        $newAttachment = $request->input('extra.addAttachment');
        $employeeId = Helper::getEmployeeSystemID();
        $response = $request->input('extra.response');
        $id = $request->input('extra.parent_id');
        $tenderParentPost = TenderBidClarifications::where('id', $id)->first();
        $tenderMaster = TenderMaster::find($tenderParentPost['tender_master_id']);
        $companySystemID = $tenderMaster['company_id'];
        $company = Company::where('companySystemID', $companySystemID)->first();
        $documentCode = DocumentMaster::where('documentSystemID', 109)->first();
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $updateRecordId = $request->input('extra.updateRecordId');
        if ($updateRecordId !== 0) {
            return $this->updatePreBidResponse($request, $updateRecordId, $companySystemID, $company, $newAttachment);
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
            if (isset($attachment) && !empty($attachment) && $newAttachment) {
                $this->uploadAttachment($attachment, $companySystemID, $company, $documentCode, $result->id);
            }

            if ($result) {
                $updateRec['is_answered'] = 0;
                $result = TenderBidClarifications::where('id', $id)
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
                $allowExtensions = ['png', 'jpg', 'jpeg', 'pdf', 'txt', 'xlsx', 'docx'];

                if (!in_array(strtolower($extension), $allowExtensions)) {
                    throw new Exception("This file type is not allowed to upload.", 500);
                }

                if (isset($attachment['size'])) {
                    if ($attachment['size'] > 2097152) {
                        throw new Exception("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.", 500);
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
                $allowExtensions = ['png', 'jpg', 'jpeg', 'pdf', 'txt', 'xlsx', 'docx'];

                if (!in_array(strtolower($extension), $allowExtensions)) {
                    return [
                        'success' => false,
                        'message' => 'This file type is not allowed to upload.',
                        'data' => 'This file type is not allowed to upload.'
                    ];
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
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e,
                'data' => ''
            ];
        }
    }

    public function updatePreBid(Request $request, $prebidId, $company, $companySystemID)
    {
        $question = $request->input('extra.question');
        $isDeleted = $request->input('extra.isDeleted');
        $attachment = $request->input('extra.attachment');
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

            if (!empty($attachment)) {
                $this->uploadAttachment($attachment, $companySystemID, $company, $documentCode, $prebidId);
            }

            DB::commit();
            return ['success' => true, 'data' => $status, 'message' => 'Successfully updated'];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }

    public function updatePreBidResponse(Request $request, $prebidId, $companySystemID, $company, $newAttachment)
    {
        $input = $request->all();
        $question = $request->input('extra.response');
        $attachment = $request->input('extra.attachment');
        $documentCode = DocumentMaster::where('documentSystemID', 109)->first();
        DB::beginTransaction();
        try {
            $data['post'] = $question;
            $status = $this->tenderBidClarificationsRepository->update($data, $prebidId);
            if ($newAttachment) {
                $isAttachmentExist = DocumentAttachments::where('documentSystemID', 109)
                    ->where('documentSystemCode', $prebidId)
                    ->count();

                if ($isAttachmentExist > 0) {
                    DocumentAttachments::where('documentSystemID', 109)
                        ->where('documentSystemCode', $prebidId)
                        ->delete();
                }

                if (!empty($attachment) && isset($attachment[0]['file'])) {
                    $this->uploadAttachment($attachment, $companySystemID, $company, $documentCode, $prebidId);
                }
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

        $queryRecordsCount = DocumentAttachments::where('documentSystemID', 106)
            ->where('documentSystemCode', $appointmentID)
            ->firstOrFail()->toArray();

        if (sizeof($queryRecordsCount)) {
            $result = DocumentAttachments::where('documentSystemID', 106)
                ->where('documentSystemCode', $appointmentID)
                ->get();

            return [
                'success' => true,
                'message' => 'Delivery Appointment successfully get',
                'data' => $result
            ];
        } else {
            return [
                'success' => true,
                'message' => 'No records found',
                'data' => ''
            ];
        }
    }

    public function removeDeliveryAppointmentAttachment($request)
    {
        $attachmentID = $request->input('extra.attachmentID');

        $data = DocumentAttachments::where('attachmentID', $attachmentID)
            ->delete();

        return [
            'success' => true,
            'message' => 'Attachment deleted successfully ',
            'data' => $data
        ];
    }

    public static function getSupplierData($uuid)
    {

        if ($uuid) {
            $supplier = SupplierRegistrationLink::where('uuid', $uuid)
                ->first();

            if (!empty($supplier)) {
                return $supplier;
            }
        }
        return 0;
    }

    public function removePreBidClarificationResponse($request)
    {
        $id = $request->input('extra.id');
        DB::beginTransaction();
        try {
            $parentId = TenderBidClarifications::select('parent_id')->where('id', $id)->first();
            $parentIdList = TenderBidClarifications::select('id', 'parent_id', 'post', 'supplier_id')
                ->where('parent_id', $parentId['parent_id'])
                ->orderBy('id', 'desc')
                ->get();
            $status = TenderBidClarifications::where('id', $id)
                ->delete();

            if ($status && !empty($parentId)) {
                if (empty($parentIdList[1]['supplier_id']) && sizeof($parentIdList) != 1) {
                    $data['is_answered'] = 1;
                    $this->tenderBidClarificationsRepository->update($data, $parentId['parent_id']);
                }
            }
            DB::commit();
            return ['success' => true, 'data' => $status, 'message' => 'Successfully deleted'];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }

    public function getConsolidatedData($request)
    {
        $tenderMasterId = $request->input('extra.tenderId');
        $supplierRegId =  self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $assignDocumentTypesDeclared = [1];
        $assignDocumentTypes = TenderDocumentTypeAssign::where('tender_id',$tenderMasterId)->whereNotIn('document_type_id',[2, 3])->pluck('document_type_id')->toArray();
        $tenderDates = [];
        $doucments = (array_merge($assignDocumentTypesDeclared,$assignDocumentTypes));
        $tenderMaster = TenderMaster::select(
            'title',
            'tender_code',
            'document_type',
            'document_sales_start_date',
            'document_sales_end_date',
            'pre_bid_clarification_start_date',
            'pre_bid_clarification_end_date',
            'site_visit_date',
            'site_visit_end_date',
            'bid_submission_opening_date',
            'bid_submission_closing_date',
            'bid_opening_date',
            'bid_opening_end_date',
            'technical_bid_opening_date',
            'technical_bid_closing_date',
            'commerical_bid_opening_date',
            'commerical_bid_closing_date',
            'no_of_alternative_solutions',
            'is_active_go_no_go',
            'stage',
            'document_system_id'
        )
            ->where('id', $tenderMasterId)
            ->first();

        if($tenderMaster['document_system_id'] == 108){
            $doctype = "Tender";
        }else{
            $doctype = "";
        }

        $ClanderDetails = DB::table('srm_calendar_dates_detail')->selectRaw(
            'srm_calendar_dates.calendar_date, 
            DATE(srm_calendar_dates_detail.from_date) as from_date,
            DATE(srm_calendar_dates_detail.to_date) as to_date'
        )->join('srm_calendar_dates', function ($query) {
            $query->on('srm_calendar_dates_detail.calendar_date_id', '=', 'srm_calendar_dates.id');
        })
            ->where('tender_id', $tenderMasterId)
            ->get();

        $ClanderDetailsArrayData = array_map(function ($query) {
            return (array)$query;
        }, $ClanderDetails->toArray());

        if (!empty($tenderMaster)) {
            $tenderDates = array(
                [
                    'calendar_date' => $doctype.' Document Sale',
                    'from_date' => (!is_null($tenderMaster['document_sales_start_date'])) ? Carbon::parse($tenderMaster['document_sales_start_date'])->format('Y-m-d') : null,
                    'to_date' => (!is_null($tenderMaster['document_sales_end_date'])) ? Carbon::parse($tenderMaster['document_sales_end_date'])->format('Y-m-d') : null
                ],
                [
                    'calendar_date' => 'Pre-bid Clarification',
                    'from_date' => (!is_null($tenderMaster['pre_bid_clarification_start_date'])) ? Carbon::parse($tenderMaster['pre_bid_clarification_start_date'])->format('Y-m-d') : null,
                    'to_date' => (!is_null($tenderMaster['pre_bid_clarification_end_date'])) ? Carbon::parse($tenderMaster['pre_bid_clarification_end_date'])->format('Y-m-d') : null
                ],
                [
                    'calendar_date' => 'Site Visit',
                    'from_date' => (!is_null($tenderMaster['site_visit_date'])) ? Carbon::parse($tenderMaster['site_visit_date'])->format('Y-m-d') : null,
                    'to_date' => (!is_null($tenderMaster['site_visit_end_date'])) ? Carbon::parse($tenderMaster['site_visit_end_date'])->format('Y-m-d') : null
                ],
                [
                    'calendar_date' => 'Bid Submission Date',
                    'from_date' => (!is_null($tenderMaster['bid_submission_opening_date'])) ? Carbon::parse($tenderMaster['bid_submission_opening_date'])->format('Y-m-d') : null,
                    'to_date' => (!is_null($tenderMaster['bid_submission_closing_date'])) ? Carbon::parse($tenderMaster['bid_submission_closing_date'])->format('Y-m-d') : null
                ],
            );
            if($tenderMaster['stage'] == 1){
                array_push($tenderDates,
                    [
                        'calendar_date' => 'Bid Opening Date',
                        'from_date' => (!is_null($tenderMaster['bid_opening_date'])) ? Carbon::parse($tenderMaster['bid_opening_date'])->format('Y-m-d') : null,
                        'to_date' => (!is_null($tenderMaster['bid_opening_end_date'])) ? Carbon::parse($tenderMaster['bid_opening_end_date'])->format('Y-m-d') : null
                    ]);
            }

            if($tenderMaster['stage'] == 2){
                array_push($tenderDates,
                    [
                        'calendar_date' => 'Technical Bid Opening Date',
                        'from_date' => (!is_null($tenderMaster['technical_bid_opening_date'])) ? Carbon::parse($tenderMaster['technical_bid_opening_date'])->format('Y-m-d') : null,
                        'to_date' => (!is_null($tenderMaster['technical_bid_closing_date'])) ? Carbon::parse($tenderMaster['technical_bid_closing_date'])->format('Y-m-d') : null
                    ],
                    [
                        'calendar_date' => 'Commercial Bid Opening Date',
                        'from_date' => (!is_null($tenderMaster['commerical_bid_opening_date'])) ? Carbon::parse($tenderMaster['commerical_bid_opening_date'])->format('Y-m-d') : null,
                        'to_date' => (!is_null($tenderMaster['commerical_bid_closing_date'])) ? Carbon::parse($tenderMaster['commerical_bid_closing_date'])->format('Y-m-d') : null
                    ]);
            }
        }

        $calendarDateMerge = collect($tenderDates)->merge($ClanderDetailsArrayData);

        $currentSequence = collect($calendarDateMerge)->map(function ($group) {
            $data = null;
            if ($group['from_date'] <= Carbon::now()->format("Y-m-d") && $group['to_date'] >= Carbon::now()->format("Y-m-d")) {
                $data = $group['calendar_date'];
            }
            return $data;
        });
        $data['currentSequence'] = $currentSequence->filter()->last();
        $data['noOfBids'] = $tenderMaster['no_of_alternative_solutions'];
        $data['goNoGoEnable'] = $tenderMaster['is_active_go_no_go'];
        $data['title'] = $tenderMaster['title'];
        $data['tender_code'] = $tenderMaster['tender_code'];
        $data['document_type'] = $tenderMaster['document_type'];
        $data['sequenceDate'] = $calendarDateMerge;
        $data['isBidSubmission'] = ($data['currentSequence'] === 'Bid Submission Date' ? 1 : 0);
        $attachments = TenderDocumentTypes::with(['attachments' => function ($q) use ($tenderMasterId,$tenderMaster) {
            $q->where('documentSystemCode', $tenderMasterId);
            $q->where(function($query) use($tenderMaster){
                if($tenderMaster->document_type == 0)
                {
                   $type = 108;
                }
                else
                {
                   $type = 113;
                }
                $query->where('documentSystemID', $type);
            });
        }])
        ->whereIn('id',$doucments)
        ->where('srm_action', '!=', 2)
        ->WhereHas('attachments', function ($q1) use ($tenderMasterId,$tenderMaster) {
                $q1->where('documentSystemCode', $tenderMasterId)
                   ->where(function($query) use($tenderMaster){
                        if($tenderMaster->document_type == 0)
                        {
                        $type = 108;
                        }
                        else
                        {
                        $type = 113;
                        }
                        $query->where('documentSystemID', $type);
                    });
        })
        ->get();

        $data['attachments'] = $attachments;
        $data['tenderCirculars'] = TenderCirculars::with(['document_amendments.document_attachments'])
            ->whereHas('srm_circular_suppliers', function($q) use($supplierRegId) {
                    $q->where('supplier_id', $supplierRegId);
            })
            ->where('tender_id', $tenderMasterId)
            ->where('status', 1)

            ->get();
        $data['tenders'] = TenderMaster::where('id',$tenderMasterId)->select('id','document_type')->first();

        return [
            'success' => true,
            'message' => 'Consolidated view data Successfully get',
            'data' => $data
        ];
    }

    public function getConsolidatedDataAttachment($request)
    {
        $tenderMasterId = $request->input('extra.tenderId');
        $attachmentId = $request->input('extra.attachmentId');
        $tender = TenderMaster::where('id',$tenderMasterId)->select('id','document_type')->first();

        $attachment = DocumentAttachments::where('attachmentID', $attachmentId)
            ->where(function($query) use($tender){
                if($tender->document_type == 0)
                {
                $type = 108;
                }
                else
                {
                $type = 113;
                }
                $query->where('documentSystemID', $type);
            })
            ->first();

        $data['attachmentPath'] = Helper::getFileUrlFromS3($attachment['path']);
        $data['extension'] = strtolower(pathinfo($attachment['path'], PATHINFO_EXTENSION));
        return [
            'success' => true,
            'message' => 'Consolidated view data Successfully get',
            'data' => $data
        ];
    }

    public function getGoNoGoBidSubmissionData($request, $negotiationStatus = false, $arr = [])
    {

        $tenderId = (sizeof($arr) > 0) ? $arr[0] : null;
        $critera_type_id = 1;
        $bidMasterId = (sizeof($arr) > 0) ? $arr[1] : null;
        $negotiation = $negotiationStatus;

        if(!$negotiationStatus){
            $tenderId = $request->input('extra.tenderId');
            $critera_type_id = $request->input('extra.critera_type_id');
            $bidMasterId = $request->input('extra.bidMasterId');
            $negotiation = $request->input('extra.negotiation');
        }

        if($negotiation){
            $tenderNegotiationArea = $this->getTenderNegotiationArea($tenderId, $bidMasterId);
            $data['technical_evaluation'] = $tenderNegotiationArea->technical_evaluation;
            $data['tender_documents'] = $tenderNegotiationArea->tender_documents;
        }

        $data['criteriaDetail'] = EvaluationCriteriaDetails::with(['evaluation_criteria_score_config', 'evaluation_criteria_type', 'tender_criteria_answer_type', 'bid_submission_detail' => function ($q) use ($bidMasterId) {
            $q->where('bid_master_id', $bidMasterId);
        }, 'child' => function ($q) use ($bidMasterId) {
            $q->with(['evaluation_criteria_score_config', 'evaluation_criteria_type', 'tender_criteria_answer_type', 'bid_submission_detail' => function ($q) use ($bidMasterId) {
                $q->where('bid_master_id', $bidMasterId);
            }, 'child' => function ($q) use ($bidMasterId) {
                $q->with(['evaluation_criteria_score_config', 'evaluation_criteria_type', 'tender_criteria_answer_type', 'bid_submission_detail' => function ($q) use ($bidMasterId) {
                    $q->where('bid_master_id', $bidMasterId);
                }, 'child' => function ($q) use ($bidMasterId) {
                    $q->with(['evaluation_criteria_score_config', 'evaluation_criteria_type', 'tender_criteria_answer_type', 'bid_submission_detail' => function ($q) use ($bidMasterId) {
                        $q->where('bid_master_id', $bidMasterId);
                    }]);
                }]);
            }]);
        }])->where('tender_id', $tenderId)->where('level', 1)->where('critera_type_id', $critera_type_id)->get();

        foreach ($data['criteriaDetail'] as $key1 => $val1){
            if($val1['is_final_level']==1){
                if(!empty($val1['bid_submission_detail'])){
                    $val1['finalTotal'] = $val1['bid_submission_detail']['result'];
                }else{
                    $val1['finalTotal'] = 0;
                }
            }else{
                if(count($val1['child'])>0){
                    foreach ($val1['child'] as $key2 => $val2){
                        if($val2['is_final_level']==1) {
                            if(!empty($val2['bid_submission_detail'])){
                                $val1['finalTotal'] += $val2['bid_submission_detail']['result'];
                                $val2['finalTotal'] += $val2['bid_submission_detail']['result'];
                            }else{
                                $val1['finalTotal'] += 0;
                                $val2['finalTotal'] += 0;
                            }
                        }else{
                            if(count($val2['child'])>0){
                                foreach ($val2['child'] as $key2 => $val3){
                                    if($val3['is_final_level']==1) {
                                        if(!empty($val3['bid_submission_detail'])){
                                            $val1['finalTotal'] += $val3['bid_submission_detail']['result'];
                                            $val2['finalTotal'] += $val3['bid_submission_detail']['result'];
                                            $val3['finalTotal'] += $val3['bid_submission_detail']['result'];
                                        }else{
                                            $val1['finalTotal'] += 0;
                                            $val2['finalTotal'] += 0;
                                            $val3['finalTotal'] += 0;
                                        }
                                    }else{
                                        if(count($val3['child'])>0){
                                            foreach ($val3['child'] as $key3 => $val4){
                                                if($val4['is_final_level']==1) {
                                                    if(!empty($val4['bid_submission_detail'])){
                                                        $val1['finalTotal'] += $val4['bid_submission_detail']['result'];
                                                        $val2['finalTotal'] += $val4['bid_submission_detail']['result'];
                                                        $val3['finalTotal'] += $val4['bid_submission_detail']['result'];
                                                    }else{
                                                        $val1['finalTotal'] += 0;
                                                        $val2['finalTotal'] += 0;
                                                        $val3['finalTotal'] += 0;
                                                    }
                                                }
                                            }
                                        }else{
                                            if(!empty($val3['bid_submission_detail'])){
                                                $val1['finalTotal'] = $val3['bid_submission_detail']['result'];
                                                $val2['finalTotal'] = $val3['bid_submission_detail']['result'];
                                                $val3['finalTotal'] = $val3['bid_submission_detail']['result'];
                                            }else{
                                                $val1['finalTotal'] = 0;
                                                $val2['finalTotal'] = 0;
                                                $val3['finalTotal'] = 0;
                                            }
                                        }
                                    }
                                }
                            }else{
                                if(!empty($val2['bid_submission_detail'])){
                                    $val1['finalTotal'] = $val2['bid_submission_detail']['result'];
                                    $val2['finalTotal'] = $val2['bid_submission_detail']['result'];
                                }else{
                                    $val1['finalTotal'] = 0;
                                    $val2['finalTotal'] = 0;
                                }
                            }
                        }
                    }
                }else{
                    if(!empty($val1['bid_submission_detail'])){
                        $val1['finalTotal'] = $val1['bid_submission_detail']['result'];
                    }else{
                        $val1['finalTotal'] = 0;
                    }
                }
            }
        }

        $data['bidSubmitted'] = $this->getBidMasterData($bidMasterId);
        $data['showTechnicalCriteria'] = TenderMaster::select('show_technical_criteria')->where('id', $tenderId)->first();

        return [
            'success' => true,
            'message' => 'Go No Go Bid Submission Successfully get',
            'data' => $data
        ];
    }

    public function checkBidSubmitted($request)
    {
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $tender_id = $request->input('extra.tenderId');
        $tender_negotiation = $request->input('extra.tender_negotiation');
        $tender_negotiation_data = $request->input('extra.tender_negotiation_data');

        $bidSubmitted = BidSubmissionMaster::where('tender_id', $tender_id)
            ->where('supplier_registration_id', $supplierRegId)
            ->orderBy('id', 'ASC')
            ->get();

        if($tender_negotiation){
            $bidSubmitted = TenderBidNegotiation::select('tender_id', 'tender_negotiation_id', 'bid_submission_master_id_old', 'bid_submission_master_id_new','bid_submission_code_old', 'supplier_id')
               ->with(['tender_negotiation_area'])
                ->where('bid_Submission_code_old', $tender_negotiation_data[0]['supplier_tender_negotiation']['bidSubmissionCode'])
                ->where('supplier_id', $supplierRegId)
                ->get();

            $oldBidSubmission = BidSubmissionMaster::where('bidSubmissionCode', $tender_negotiation_data[0]['supplier_tender_negotiation']['bidSubmissionCode'])->first();
            $tenderNegotiation_records = TenderNegotiation::with(['area'])->where('srm_tender_master_id', $tender_id)->get();
        }

        if (!empty($bidSubmitted) && count($bidSubmitted) > 0) {
            $bidMasterId = 0;
        } else {

            DB::beginTransaction();
            try {

                $lastSerialNo = BidSubmissionMaster::orderBy('id', 'desc')
                ->first();

                if(isset($lastSerialNo->serialNumber) && $lastSerialNo->serialNumber != null)
                {

                    $lastSerialValue = 1;
                    if ($lastSerialNo) {
                        $lastSerialValue = intval($lastSerialNo->serialNumber) + 1;
                    }

                    $att['serialNumber'] = $lastSerialValue;
                    $att['bidSubmissionCode'] = 'Bid_'.str_pad($lastSerialValue, 10, '0', STR_PAD_LEFT);

                }
                else
                {
                    $att['serialNumber'] = 1;
                    $att['bidSubmissionCode'] = 'Bid_'.str_pad(1, 10, '0', STR_PAD_LEFT);
                }

                $att['tender_id'] = $tender_id;
                $att['supplier_registration_id'] = $supplierRegId;
                $att['uuid'] = Uuid::generate()->string;
                $att['bid_sequence'] = 1;

               if($tender_negotiation && isset($tenderNegotiation_records[0]['area']['tender_documents']) && ($tenderNegotiation_records[0]['area']['tender_documents'] == 0)){
                    $att['doc_verifiy_yn'] = $oldBidSubmission->doc_verifiy_yn;
                    $att['doc_verifiy_by_emp'] = $oldBidSubmission->doc_verifiy_by_emp;
                    $att['doc_verifiy_date'] = $oldBidSubmission->doc_verifiy_date;
                    $att['doc_verifiy_status'] = $oldBidSubmission->doc_verifiy_status;
                    $att['doc_verifiy_comment'] = $oldBidSubmission->doc_verifiy_comment;
                    TenderMaster::where('id', $tender_id)->update([
                       'negotiation_doc_verify_comment' => $oldBidSubmission->doc_verifiy_comment,
                       'negotiation_doc_verify_status' => $oldBidSubmission->doc_verifiy_status
                    ]);

               }

                if($tender_negotiation && isset($tenderNegotiation_records[0]['area']['pricing_schedule']) && ($tenderNegotiation_records[0]['area']['pricing_schedule'] == 0)){
                    $att['commercial_verify_status'] = $oldBidSubmission->commercial_verify_status;
                    $att['commercial_verify_at'] = $oldBidSubmission->commercial_verify_at;
                    $att['commercial_verify_by'] = $oldBidSubmission->commercial_verify_by;
                }

                if($tender_negotiation && isset($tenderNegotiation_records[0]['area']['technical_evaluation']) && ($tenderNegotiation_records[0]['area']['technical_evaluation'] == 0)){
                    $att['technical_verify_status'] = $oldBidSubmission->technical_verify_status;
                    $att['technical_verify_at'] = $oldBidSubmission->technical_verify_at;
                    $att['technical_verify_by'] = $oldBidSubmission->technical_verify_by;
                    $att['technical_eval_remarks'] = $oldBidSubmission->technical_eval_remarks;
                }

                $att['created_at'] = Carbon::now();
                $att['created_by'] = $supplierRegId;
                $result = BidSubmissionMaster::create($att);
                $bidMasterId = $result['id'];

                if(count($bidSubmitted) == 0 && $tender_negotiation){
                    $this->crateNewNegotiationTender($tender_id,$tender_negotiation_data,$bidMasterId,$supplierRegId,$att);
                }

                $details = PricingScheduleMaster::select('id', 'price_bid_format_id')->with(['tender_bid_format_master', 'pricing_shedule_details'=>function($query){
                    $query->select('pricing_schedule_master_id', 'company_id')->where('field_type',4);
                }])->where('tender_id', $tender_id)->get();


                foreach($details as $detail)
                {

                    foreach($detail->pricing_shedule_details as $bid)
                    {
                            $data['bid_format_detail_id'] = $bid->id;
                            $data['schedule_id'] = $bid->pricing_schedule_master_id;
                            $data['value'] = null;
                            $data['created_by'] = $supplierRegId;
                            $data['company_id'] = $bid->company_id;
                            $data['bid_master_id'] = $bidMasterId;
                            $results = ScheduleBidFormatDetails::create($data);





                    }

                    if(count($detail->pricing_shedule_details) > 0)
                    {
                        $outcome = DB::table('srm_pricing_schedule_detail')->where('bid_format_id',$detail->price_bid_format_id)->where('pricing_schedule_master_id',$detail->id)
                        //->leftJoin('srm_schedule_bid_format_details', 'srm_pricing_schedule_detail.id', '=', 'srm_schedule_bid_format_details.bid_format_detail_id')
                        ->join('tender_field_type', 'srm_pricing_schedule_detail.field_type', '=', 'tender_field_type.id')
                        ->leftJoin('srm_bid_main_work', function($join) use($bidMasterId){
                            $join->on('srm_pricing_schedule_detail.id', '=', 'srm_bid_main_work.main_works_id');
                            $join->where('srm_bid_main_work.bid_master_id',$bidMasterId) ;
                        })
                        ->leftJoin('srm_schedule_bid_format_details', function($join) use($bidMasterId){
                            $join->on('srm_pricing_schedule_detail.id', '=', 'srm_schedule_bid_format_details.bid_format_detail_id');
                            //$join->where('srm_schedule_bid_format_details.bid_master_id',$bidMasterId) ;
                        })
                        ->select('srm_pricing_schedule_detail.id as id','srm_pricing_schedule_detail.is_disabled','srm_pricing_schedule_detail.field_type as typeId','srm_pricing_schedule_detail.formula_string','srm_pricing_schedule_detail.bid_format_detail_id',
                                DB::raw('(CASE WHEN srm_pricing_schedule_detail.field_type = 4 THEN srm_schedule_bid_format_details.value 
                                            WHEN (srm_pricing_schedule_detail.field_type != 4 && srm_pricing_schedule_detail.is_disabled = 1) THEN srm_schedule_bid_format_details.value    
                                            WHEN (srm_pricing_schedule_detail.field_type != 4 && srm_pricing_schedule_detail.boq_applicable = 1) THEN srm_bid_main_work.total_amount    
                                            WHEN (srm_pricing_schedule_detail.is_disabled = 0 && srm_pricing_schedule_detail.boq_applicable = 0) THEN srm_bid_main_work.total_amount 
                                            END) AS value'))
                        ->get();

                        $details_obj = array_map(function($item) {
                            return (array)$item;
                        }, $outcome->toArray());


                         $formula_cal = PirceBidFormula::process($details_obj,$tender_id);


                        foreach($formula_cal as $val)
                        {
                            foreach($val as $key=>$val1)
                            {
                                $formatted_val =  round($val1, 3);
                                $flight = ScheduleBidFormatDetails::updateOrCreate(
                                    ['bid_format_detail_id' => $key, 'schedule_id' => $detail->id,'bid_master_id'=>$bidMasterId],
                                    ['value' => $val1,'bid_master_id',$bidMasterId]
                                );

                            }


                        }
                    }

                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                return ['success' => false, 'data' => '', 'message' => $e];
            }
        }

        $data['bidMasterId'] = $bidMasterId;
        $data['bidSubmittedData'] = $bidSubmitted;

        return [
            'success' => true,
            'message' => 'Retrieved Bid Submission id',
            'data' => $data
        ];
    }

    public function saveTechnicalBidSubmission($request)
    {
        $tenderId = $request->input('extra.tenderMasterId');
        $bidMasterId = $request->input('extra.bidMasterId');
        $criteriaDetail = $request->input('extra.criteriaDetail');
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $details = array();

        if (count($criteriaDetail) > 0) {
            BidSubmissionDetail::where('bid_master_id', $bidMasterId)->where('tender_id', $tenderId)->delete();
            foreach ($criteriaDetail as $val) {
                if ($val['is_final_level'] == 1) {
                    if ($val['bid_submission_detail']['score_id'] > 0 && $val['bid_submission_detail']['score_id'] != null) {
                        if ($val['answer_type_id'] == 4 || $val['answer_type_id'] == 2) {
                            $score = EvaluationCriteriaScoreConfig::where('id', $val['bid_submission_detail']['score_id'])->first();
                            $push['bid_master_id'] = $bidMasterId;
                            $push['tender_id'] = $tenderId;
                            $push['evaluation_detail_id'] = $val['id'];
                            $push['score_id'] = $val['bid_submission_detail']['score_id'];
                            $push['score'] = $score['score'];
                            $push['created_by'] = $supplierRegId;
                            array_push($details, $push);
                        }
                    }
                    if ($val['bid_submission_detail']['score'] != null) {
                        if ($val['answer_type_id'] == 1 || $val['answer_type_id'] == 3) {
                            $push['bid_master_id'] = $bidMasterId;
                            $push['tender_id'] = $tenderId;
                            $push['evaluation_detail_id'] = $val['id'];
                            $push['score_id'] = null;
                            $push['score'] = $val['bid_submission_detail']['score'];
                            $push['created_by'] = $supplierRegId;
                            array_push($details, $push);
                        }
                    }
                }

                foreach ($val['child'] as $val2) {
                    if ($val2['is_final_level'] == 1) {
                        if ($val2['bid_submission_detail']['score_id'] > 0 && $val2['bid_submission_detail']['score_id'] != null) {
                            if ($val2['answer_type_id'] == 4 || $val2['answer_type_id'] == 2) {
                                $score = EvaluationCriteriaScoreConfig::where('id', $val2['bid_submission_detail']['score_id'])->first();
                                $push['bid_master_id'] = $bidMasterId;
                                $push['tender_id'] = $tenderId;
                                $push['evaluation_detail_id'] = $val2['id'];
                                $push['score_id'] = $val2['bid_submission_detail']['score_id'];
                                $push['score'] = $score['score'];
                                $push['created_by'] = $supplierRegId;
                                array_push($details, $push);
                            }
                        }
                        if ($val2['bid_submission_detail']['score'] != null) {
                            if ($val2['answer_type_id'] == 1 || $val2['answer_type_id'] == 3) {
                                $push['bid_master_id'] = $bidMasterId;
                                $push['tender_id'] = $tenderId;
                                $push['evaluation_detail_id'] = $val2['id'];
                                $push['score_id'] = null;
                                $push['score'] = $val2['bid_submission_detail']['score'];
                                $push['created_by'] = $supplierRegId;
                                array_push($details, $push);
                            }
                        }
                    }

                    foreach ($val2['child'] as $val3) {
                        if ($val3['is_final_level'] == 1) {
                            if ($val3['bid_submission_detail']['score_id'] > 0 && $val3['bid_submission_detail']['score_id'] != null) {
                                if ($val3['answer_type_id'] == 4 || $val3['answer_type_id'] == 2) {
                                    $score = EvaluationCriteriaScoreConfig::where('id', $val3['bid_submission_detail']['score_id'])->first();
                                    $push['bid_master_id'] = $bidMasterId;
                                    $push['tender_id'] = $tenderId;
                                    $push['evaluation_detail_id'] = $val3['id'];
                                    $push['score_id'] = $val3['bid_submission_detail']['score_id'];
                                    $push['score'] = $score['score'];
                                    $push['created_by'] = $supplierRegId;
                                    array_push($details, $push);
                                }
                            }
                            if ($val3['bid_submission_detail']['score'] != null) {
                                if ($val3['answer_type_id'] == 1 || $val3['answer_type_id'] == 3) {
                                    $push['bid_master_id'] = $bidMasterId;
                                    $push['tender_id'] = $tenderId;
                                    $push['evaluation_detail_id'] = $val3['id'];
                                    $push['score_id'] = null;
                                    $push['score'] = $val3['bid_submission_detail']['score'];
                                    $push['created_by'] = $supplierRegId;
                                    array_push($details, $push);
                                }
                            }
                        }

                        foreach ($val3['child'] as $val4) {
                            if ($val4['is_final_level'] == 1) {
                                if ($val4['bid_submission_detail']['score_id'] > 0 && $val4['bid_submission_detail']['score_id'] != null) {
                                    if ($val4['answer_type_id'] == 4 || $val4['answer_type_id'] == 2) {
                                        $score = EvaluationCriteriaScoreConfig::where('id', $val4['bid_submission_detail']['score_id'])->first();
                                        $push['bid_master_id'] = $bidMasterId;
                                        $push['tender_id'] = $tenderId;
                                        $push['evaluation_detail_id'] = $val4['id'];
                                        $push['score_id'] = $val4['bid_submission_detail']['score_id'];
                                        $push['score'] = $score['score'];
                                        $push['created_by'] = $supplierRegId;
                                        array_push($details, $push);
                                    }
                                }
                                if ($val4['bid_submission_detail']['score'] != null) {
                                    if ($val4['answer_type_id'] == 1 || $val4['answer_type_id'] == 3) {
                                        $push['bid_master_id'] = $bidMasterId;
                                        $push['tender_id'] = $tenderId;
                                        $push['evaluation_detail_id'] = $val4['id'];
                                        $push['score_id'] = null;
                                        $push['score'] = $val4['bid_submission_detail']['score'];
                                        $push['created_by'] = $supplierRegId;
                                        array_push($details, $push);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            return ['success' => false, 'data' => '', 'message' => 'No Record Found'];
        }

        DB::beginTransaction();
        try {
            if (count($details) > 0) {
                foreach ($details as $dt) {
                    $att['bid_master_id'] = $dt['bid_master_id'];
                    $att['created_at'] = Carbon::now();
                    $att['created_by'] = $dt['created_by'];
                    $att['evaluation_detail_id'] = $dt['evaluation_detail_id'];
                    $att['score'] = $dt['score'];
                    $att['score_id'] = $dt['score_id'];
                    $att['tender_id'] = $dt['tender_id'];
                    $result = BidSubmissionDetail::create($att);
                }
            }
            DB::commit();
            return [
                'success' => true,
                'message' => 'Successfully Saved',
                'data' => $details
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }

    public function saveTechnicalBidSubmissionLine($request)
    {
        $tenderId = $request->input('extra.tenderMasterId');
        $bidMasterId = $request->input('extra.bidMasterId');
        $criteriaDetail = $request->input('extra.criteriaDetail');
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));

        DB::beginTransaction();
        try {
            $showTechnicalCriteria = TenderMaster::select('show_technical_criteria')->where('id', $tenderId)->first();

            if ($criteriaDetail['answer_type_id'] == 4 || $criteriaDetail['answer_type_id'] == 2) {
                if (($showTechnicalCriteria['show_technical_criteria'] == 0 && $criteriaDetail['bid_submission_detail']['score_id'] > 0 && $criteriaDetail['bid_submission_detail']['score_id'] != null) || $showTechnicalCriteria['show_technical_criteria'] == 1) {
                    $score = EvaluationCriteriaScoreConfig::where('id', $criteriaDetail['bid_submission_detail']['score_id'])->first();
                    $push['bid_master_id'] = $bidMasterId;
                    $push['tender_id'] = $tenderId;
                    $push['evaluation_detail_id'] = $criteriaDetail['id'];
                    $push['score_id'] = $criteriaDetail['bid_submission_detail']['score_id'];
                    $push['score'] = $score['score'];
                    $push['created_by'] = $supplierRegId;

                    if (isset($criteriaDetail['bid_submission_detail']['id'])) {
                        $push['id'] = $criteriaDetail['bid_submission_detail']['id'];
                    } else {
                        $push['id'] = 0;
                    }
                } else {
                    $result = BidSubmissionDetail::where('bid_master_id', $bidMasterId)->where('tender_id', $tenderId)->where('evaluation_detail_id', $criteriaDetail['id'])->delete();
                    DB::commit();
                    return [
                        'success' => true,
                        'message' => 'Successfully Saved',
                        'data' => $result
                    ];
                }
            }

            if ($criteriaDetail['answer_type_id'] == 1 || $criteriaDetail['answer_type_id'] == 3) {
                if (!is_null($criteriaDetail['bid_submission_detail']['score'])) {
                    $push['bid_master_id'] = $bidMasterId;
                    $push['tender_id'] = $tenderId;
                    $push['evaluation_detail_id'] = $criteriaDetail['id'];
                    $push['score_id'] = null;
                    $push['score'] = $criteriaDetail['bid_submission_detail']['score'];
                    $push['created_by'] = $supplierRegId;
                    if (isset($criteriaDetail['bid_submission_detail']['id'])) {
                        $push['id'] = $criteriaDetail['bid_submission_detail']['id'];
                    } else {
                        $push['id'] = 0;
                    }
                } else {
                    $result = BidSubmissionDetail::where('bid_master_id', $bidMasterId)->where('tender_id', $tenderId)->where('evaluation_detail_id', $criteriaDetail['id'])->delete();
                    DB::commit();
                    return [
                        'success' => true,
                        'message' => 'Successfully Saved',
                        'data' => $result
                    ];
                }
            }
            $result = ($push['score']/$criteriaDetail['max_value'])*$criteriaDetail['weightage'];

            $att['bid_master_id'] = $push['bid_master_id'];
            $att['evaluation_detail_id'] = $push['evaluation_detail_id'];
            $att['score'] = $push['score'];
            $att['score_id'] = $push['score_id'];
            $att['result'] = $result;
            $att['tender_id'] = $push['tender_id'];
            if ($push['id'] == 0) {
                $att['created_at'] = Carbon::now();
                $att['created_by'] = $push['created_by'];
                $result = BidSubmissionDetail::create($att);
            } else {
                $att['updated_at'] = Carbon::now();
                $att['updated_by'] = $push['created_by'];
                $result = BidSubmissionDetail::where('id', $push['id'])->update($att);
            }

            DB::commit();
            return [
                'success' => true,
                'message' => 'Successfully Saved',
                'data' => $push
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }

    public function saveGoNoGoBidSubmissionLine($request)
    {
        $tenderId = $request->input('extra.tenderMasterId');
        $bidMasterId = $request->input('extra.bidMasterId');
        $criteriaDetail = $request->input('extra.criteriaDetail');
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));

        DB::beginTransaction();
        try {
            if ($criteriaDetail['bid_submission_detail']['score_id'] > 0 && $criteriaDetail['bid_submission_detail']['score_id'] != null) {
                $score = EvaluationCriteriaScoreConfig::where('id', $criteriaDetail['bid_submission_detail']['score_id'])->first();
                $push['bid_master_id'] = $bidMasterId;
                $push['tender_id'] = $tenderId;
                $push['evaluation_detail_id'] = $criteriaDetail['id'];
                $push['score_id'] = $criteriaDetail['bid_submission_detail']['score_id'];
                if ($criteriaDetail['bid_submission_detail']['score_id'] == 1) {
                    $push['score'] = 0;
                } else {
                    $push['score'] = 1;
                }
                $push['created_by'] = $supplierRegId;

                if (isset($criteriaDetail['bid_submission_detail']['id'])) {
                    $push['id'] = $criteriaDetail['bid_submission_detail']['id'];
                } else {
                    $push['id'] = 0;
                }
            } else {
                $result = BidSubmissionDetail::where('bid_master_id', $bidMasterId)->where('tender_id', $tenderId)->where('evaluation_detail_id', $criteriaDetail['id'])->delete();
                DB::commit();
                return [
                    'success' => true,
                    'message' => 'Successfully Saved',
                    'data' => $result
                ];
            }

            $att['bid_master_id'] = $push['bid_master_id'];
            $att['evaluation_detail_id'] = $push['evaluation_detail_id'];
            $att['score'] = $push['score'];
            $att['score_id'] = $push['score_id'];
            $att['tender_id'] = $push['tender_id'];
            if ($push['id'] == 0) {
                $att['created_at'] = Carbon::now();
                $att['created_by'] = $push['created_by'];
                $result = BidSubmissionDetail::create($att);
            } else {
                $att['updated_at'] = Carbon::now();
                $att['updated_by'] = $push['created_by'];
                $result = BidSubmissionDetail::where('id', $push['id'])->update($att);
            }

            DB::commit();
            return [
                'success' => true,
                'message' => 'Successfully Saved',
                'data' => $push
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }

    public function getTenderAttachment($request)
    {
        $tenderId = $request->input('extra.tenderId');
        $bidMasterId = $request->input('extra.bidMasterId');
        $envelopType = $request->input('extra.envelopType');
        $assignDocumentTypesDeclared = [1,2,3];
        $assignDocumentTypes = TenderDocumentTypeAssign::where('tender_id',$tenderId)->pluck('document_type_id')->toArray();
        $doucments = (array_merge($assignDocumentTypesDeclared,$assignDocumentTypes));
        $negotiation = $request->input('extra.negotiation');

        $type = TenderMaster::where('id',$tenderId)->select('document_type')->first();

        $data['attachments'] = DocumentAttachments::with(['tender_document_types' => function ($q) use ($doucments){
            $q->whereIn('id',$doucments);
            $q->where('srm_action', 1);

        }, 'document_attachments' => function ($q) use ($bidMasterId) {
            $q->where('documentSystemCode', $bidMasterId);
        }])->whereHas('tender_document_types', function ($q) use ($doucments){
            $q->whereIn('id',$doucments);
            $q->where('srm_action', 1);
        })->where('documentSystemCode', $tenderId)->where(function($query) use($type){
            if($type->document_type == 0)
            {
                $query->where('documentSystemID', 108);
            }
            else
            {
                $query->where('documentSystemID', 113);
            }

        })->where('parent_id', null)->where('envelopType', $envelopType)->get();

        $data['bidSubmitted'] = $this->getBidMasterData($bidMasterId);

        if($negotiation){
            $tenderNegotiationArea = $this->getTenderNegotiationArea($tenderId, $bidMasterId);
            $data['tender_documents'] = $tenderNegotiationArea->tender_documents;
            $data['technical_evaluation'] = $tenderNegotiationArea->technical_evaluation;
            $data['pricing_schedule'] = $tenderNegotiationArea->pricing_schedule;
        }

        return [
            'success' => true,
            'message' => 'Successfully Received',
            'data' => $data
        ];
    }

    public function getCommonAttachment($request) {
        $tenderId = $request->input('extra.tenderId');
        $bidMasterId = $request->input('extra.bidMasterId');
        $envelopType = $request->input('extra.envelopType');
        $negotiation = $request->input('extra.negotiation');
        $assignDocumentTypesDeclared = [1,2,3];
        $assignDocumentTypes = TenderDocumentTypeAssign::where('tender_id',$tenderId)->pluck('document_type_id')->toArray();
        $doucments = (array_merge($assignDocumentTypesDeclared,$assignDocumentTypes));
        $doc_type = TenderMaster::where('id',$tenderId)->select('document_type')->first()->document_type;

        $data['attachments'] = DocumentAttachments::with(['tender_document_types' => function ($q) use ($doucments){
            $q->where('srm_action', 1);
        }, 'document_attachments' => function ($q) use ($bidMasterId) {
            $q->where('documentSystemCode', $bidMasterId);
        }])->whereHas('tender_document_types', function ($q) use ($doucments){
        })->where('documentSystemCode', $tenderId)->where('parent_id', null)
        ->where(function($query) use($doc_type){
            if($doc_type == 0)
            {
               $type = 108;
            }
            else
            {
               $type = 113;
            }
            $query->where('documentSystemID', $type);
        })
        ->where('envelopType', 3)->where('attachmentType',2)->get();

        $data['bidSubmitted'] = $this->getBidMasterData($bidMasterId);

        if($negotiation){
            $tenderNegotiationArea = $this->getTenderNegotiationArea($tenderId, $bidMasterId);
            $data['tender_documents'] = $tenderNegotiationArea->tender_documents;
        }

        return [
            'success' => true,
            'message' => 'Successfully Received',
            'data' => $data
        ];
    }

    public function reUploadTenderAttachment($request)
    {
        $tenderId = $request->input('extra.tenderId');
        $bidMasterId = $request->input('extra.bidMasterId');
        $parentId = $request->input('extra.masterId');
        $attachment = $request->input('extra.attachment');
        /*return [
            'success' => true,
            'message' => 'Attached Successfully',
            'data' =>  $request->input('extra')
        ];*/
        $tenderMaster = TenderMaster::find($tenderId);
        $doc_type = $tenderMaster->document_type;
        $parent = DocumentAttachments::find($parentId);
        $companySystemID = $tenderMaster['company_id'];
        $company = Company::where('companySystemID', $companySystemID)->first();
        $documentCode = DocumentMaster::where(function($query) use($doc_type){
            if($doc_type == 0)
            {
               $type = 108;
            }
            else
            {
               $type = 113;
            }
            $query->where('documentSystemID', $type);
        })->first();
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));

        $extension = $attachment['fileType'];
        $allowExtensions = ['png', 'jpg', 'jpeg', 'pdf', 'txt', 'xlsx', 'docx', 'pptx'];

        if (!in_array(strtolower($extension), $allowExtensions)) {
            throw new Exception("This file type is not allowed to upload.", 500);
        }

        if (isset($attachment['size'])) {
            if ($attachment['size'] > 2097152) {
                throw new Exception("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.", 500);
            }
        }

        DB::beginTransaction();
        try {
            $file = $attachment['file'];
            $decodeFile = base64_decode($file);
            $attch = time() . '_BidSubmission.' . $extension;
            $path = $companySystemID . '/BidSubmission/' . $attch;
            Storage::disk('s3')->put($path, $decodeFile);

            $att['companySystemID'] = $companySystemID;
            $att['companyID'] = $company->CompanyID;
            $att['documentSystemID'] = $documentCode->documentSystemID;
            $att['documentID'] = $documentCode->documentID;
            $att['documentSystemCode'] = $bidMasterId;
            $att['attachmentDescription'] = 'Bid Submission ' . time();
            $att['path'] = $path;
            $att['parent_id'] = $parentId;
            $att['attachmentType'] = 0;
            $att['originalFileName'] = $attachment['originalFileName'];
            $att['myFileName'] = $company->CompanyID . '_' . time() . '_BidSubmission.' . $extension;
            $att['sizeInKbs'] = $attachment['sizeInKbs'];
            $att['isUploaded'] = 1;
            $att['envelopType'] = $parent->envelopType;
            $result = DocumentAttachments::create($att);

            if($parent->envelopType == 3)
            {
                $bid_date['attachment_id'] = $result['attachmentID'];
                $bid_date['bis_submission_master_id'] = $bidMasterId;
                BidDocumentVerification::create($bid_date);
            }



            DB::commit();
            return [
                'success' => true,
                'message' => 'Attached Successfully',
                'data' => $att
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }

    public function deleteBidSubmissionAttachment($request)
    {
        $attachment = $request->input('extra.attachment');

        $data = DocumentAttachments::where('attachmentID', $attachment['attachmentID'])
            ->delete();
        $attachment_varify =   BidDocumentVerification::where('attachment_id',$attachment['attachmentID']);
        if($attachment_varify->count() > 0)
        {
        $attachment_varify->delete();
        }

        return [
            'success' => true,
            'message' => 'Attachment deleted successfully ',
            'data' => $data
        ];
    }

    public function getCommercialBidSubmissionData($request)
    {
        $tenderId = $request->input('extra.tenderId');
        $bidMasterId = $request->input('extra.bidMasterId');
        $negotiation = $request->input('extra.negotiation');

        if($negotiation){
            $tenderNegotiationArea = $this->getTenderNegotiationArea($tenderId, $bidMasterId);
            $data['pricing_schedule'] = $tenderNegotiationArea->pricing_schedule;
            $data['tender_documents'] = $tenderNegotiationArea->tender_documents;
        }

        $data['commercialBid'] = PricingScheduleMaster::with(['tender_bid_format_master', 'bid_schedule' => function ($q) use ($bidMasterId) {
            $q->where('bid_master_id', $bidMasterId);
        }, 'pricing_shedule_details' => function ($q) use ($bidMasterId) {
            $q->with(['bid_main_work' => function ($q) use ($bidMasterId) {
                $q->where('bid_master_id', $bidMasterId);
            },'bid_format_detail' =>function ($q) use ($bidMasterId) {
                $q->where('bid_master_id', $bidMasterId);
                $q->orWhere('bid_master_id', null);
            },
                'tender_bid_format_detail' => function ($q) {
                    $q->select('id', 'tender_id', 'label', 'field_type', 'finalTotalYn');
                    $q->where('finalTotalYn', 1);
                }
            ]);
        }])->where('tender_id', $tenderId)->get();

        $data['bidSubmitted'] = $this->getBidMasterData($bidMasterId);


        return [
            'success' => true,
            'message' => 'Successfully Received',
            'data' =>  $data
        ];
    }

    public function saveBidSchedule($request)
    {
        $tenderId = $request->input('extra.tenderMasterId');
        $bidMasterId = $request->input('extra.bidMasterId');
        $detail = $request->input('extra.detail');

        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $att['remarks'] = $detail['bid_schedule']['remarks'];
        if (isset($detail['bid_schedule']['id'])) {
            $att['updated_at'] = Carbon::now();
            $att['updated_by'] = $supplierRegId;
            $result = BidSchedule::where('id', $detail['bid_schedule']['id'])->update($att);
        } else {
            $att['schedule_id'] = $detail['id'];
            $att['bid_master_id'] = $bidMasterId;
            $att['tender_id'] = $tenderId;
            $att['supplier_registration_id'] = $supplierRegId;
            $att['created_at'] = Carbon::now();
            $att['created_by'] = $supplierRegId;
            $result = BidSchedule::create($att);
        }
        return [
            'success' => true,
            'message' => 'Successfully Saved',
            'data' =>  $result
        ];
    }

    public function getMainEnvelopData($request)
    {
        $tenderId = $request->input('extra.tenderId');
        $bidMasterId = $request->input('extra.bidMasterId');
        $supplierRegId =  self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $supplierData =  self::getSupplierData($request->input('supplier_uuid'));
        $negotiation = $request->input('extra.negotiation');

        $bidSubmissionData = self::BidSubmissionStatusData($bidMasterId, $tenderId);

        $evaluvationCriteriaDetailsCount = EvaluationCriteriaDetails::where('tender_id',$tenderId)->where('critera_type_id',1)->count();
        $bidSubmissionDataCount = BidSubmissionDetail::join('srm_evaluation_criteria_details','srm_bid_submission_detail.evaluation_detail_id','=','srm_evaluation_criteria_details.id')
            ->where('srm_bid_submission_detail.tender_id',$tenderId)
            ->where('srm_bid_submission_detail.bid_master_id',$bidMasterId)
            ->where('srm_evaluation_criteria_details.critera_type_id',1)->count();

        $documentTypeAssingedCount = TenderDocumentTypeAssign::where('tender_id',$tenderId)->count();

        $doc_type = TenderMaster::where('id',$tenderId)->select('document_type')->first()->document_type;


        // $data['technicalBidSubmissionYn'] = ($documentAttachment > 0 || $technicalEvaluationCriteria > 0) ? 1 : 0;
        $data['technicalBidSubmissionYn'] = $bidSubmissionData['technicalEvaluationCriteria'];
        $data['commercialBidSubmission'] = $bidSubmissionData['filtered'];
        $data['isBidSubmissionStatus'] = $bidSubmissionData['bidsubmission'];

        $doucments = [];

        $documentAttachedCountIds = DocumentAttachments::with(['tender_document_types' => function ($q) use ($doucments){
            $q->where('srm_action', 1);
        }, 'document_attachments' => function ($q) use ($bidMasterId) {
            $q->where('documentSystemCode', $bidMasterId);
        }])->whereHas('tender_document_types', function ($q) use ($doucments){
        })->where('documentSystemCode', $tenderId)->where('parent_id', null)
        ->where(function($query) use($doc_type){
            if($doc_type == 0)
            {
               $type = 108;
            }
            else
            {
               $type = 113;
            }
            $query->where('documentSystemID', $type);
        })
        ->where('envelopType', 3)->where('attachmentType',2)->pluck('attachmentID')->toArray();

        $documentAttachedCountAnswer = DocumentAttachments::whereIn('parent_id', $documentAttachedCountIds)
        ->where(function($query) use($doc_type){
            if($doc_type == 0)
            {
               $type = 108;
            }
            else
            {
               $type = 113;
            }
            $query->where('documentSystemID', $type);
        })
        ->where('documentSystemCode', $bidMasterId)->count();


        $documentAttachedCountIdsTechnical = DocumentAttachments::with(['tender_document_types' => function ($q) use ($doucments){
            $q->where('srm_action', 1);
        }, 'document_attachments' => function ($q) use ($bidMasterId) {
            $q->where('documentSystemCode', $bidMasterId);
        }])->whereHas('tender_document_types', function ($q) use ($doucments){
        })->where('documentSystemCode', $tenderId)->where('parent_id', null)
        ->where(function($query) use($doc_type){
            if($doc_type == 0)
            {
               $type = 108;
            }
            else
            {
               $type = 113;
            }
            $query->where('documentSystemID', $type);
        })
        ->where('envelopType', 2)->where('attachmentType',2)->pluck('attachmentID')->toArray();

        $documentAttachedCountAnswerTechnical = DocumentAttachments::whereIn('parent_id', $documentAttachedCountIdsTechnical)
        ->where(function($query) use($doc_type){
            if($doc_type == 0)
            {
               $type = 108;
            }
            else
            {
               $type = 113;
            }
            $query->where('documentSystemID', $type);
        })
        ->where('documentSystemCode', $bidMasterId)->count();

        $documentAttachedCountIdsCommercial = DocumentAttachments::with(['tender_document_types' => function ($q) {
            $q->where('srm_action', 1);
        }, 'document_attachments' => function ($q) use ($bidMasterId) {
            $q->where('documentSystemCode', $bidMasterId);
        }])->whereHas('tender_document_types', function ($q) {
        })->where('documentSystemCode', $tenderId)->where('parent_id', null)
        ->where(function($query) use($doc_type){
            if($doc_type == 0)
            {
               $type = 108;
            }
            else
            {
               $type = 113;
            }
            $query->where('documentSystemID', $type);
        })
        ->where('envelopType', 1)->where('attachmentType',2)->pluck('attachmentID')->toArray();


        $documentAttachedCountAnswerCommercial = DocumentAttachments::whereIn('parent_id', $documentAttachedCountIdsCommercial)
        ->where(function($query) use($doc_type){
            if($doc_type == 0)
            {
               $type = 108;
            }
            else
            {
               $type = 113;
            }
            $query->where('documentSystemID', $type);
        })
        ->where('documentSystemCode', $bidMasterId)->count();


        $technicalEvaluationCriteria = EvaluationCriteriaDetails::where('is_final_level', 0)
            ->where('critera_type_id', 2)
            ->where('tender_id', $tenderId)
            ->where('created_by',$supplierData->id)
            ->count();

        $technicalEvaluationCriteriaAnswer = EvaluationCriteriaDetails::where('critera_type_id', 2)
            ->where('tender_id', $tenderId)
            ->where('is_final_level', 3)
            ->where('created_by',$supplierData->id)
            ->count();

        // $pring_schedul_master_ids = PricingScheduleMaster::where('tender_id',$tenderId)->where('status',1)->pluck('id')->toArray();
        $pring_schedul_master_ids =  PricingScheduleMaster::with(['tender_main_works' => function ($q1) use ($tenderId, $bidMasterId) {
            $q1->where('tender_id', $tenderId);
            $q1->with(['bid_main_work' => function ($q2) use ($tenderId, $bidMasterId) {
                $q2->where('tender_id', $tenderId);
                $q2->where('bid_master_id', $bidMasterId);
            }]);
        }])
            ->where('tender_id', $tenderId)
            ->where('status',1)->pluck('id')->toArray();

        $main_works_ids = PricingScheduleDetail::whereIn('pricing_schedule_master_id',$pring_schedul_master_ids)
            ->where('is_disabled',0)
            ->select('id','boq_applicable','field_type','bid_format_detail_id','is_disabled')
            ->get();
        $has_work_ids = Array();
        $i = 0;

        foreach($main_works_ids as $main_works_id) {
            if($main_works_id->boq_applicable) {
                $boqItems = TenderBoqItems::where('main_work_id',$main_works_id->id)->get();

                foreach($boqItems as $boqItem) {
                    $dataBidBoq = BidBoq::where('boq_id',$boqItem->id)->where('bid_master_id',$bidMasterId)->where('created_by',$supplierData->id)->where('main_works_id',$main_works_id->id)->get();
                    if(count($dataBidBoq) > 0) {
                        foreach($dataBidBoq as $bidBoq){
                            if($bidBoq->total_amount >= 0 && isset($bidBoq->unit_amount) && isset($bidBoq->qty)) {
                                $has_work_ids[$i] = "true";
                            }else {
                                $has_work_ids[$i]  = "false";
                            }
                            $i++;
                        }
                    }else {
                        $has_work_ids[$i]  = "false";
                        $i++;
                    }
                }


            }else {
                if($main_works_id->field_type == 4) {
                    $bid_format_details = DB::table('srm_schedule_bid_format_details')->where('bid_format_detail_id',$main_works_id->id)->get();


                    if(count($bid_format_details) > 0) {
                        $has_work_ids[$i] = "true";
                    }else {
                        $has_work_ids[$i]  = "false";
                    }
                    $i++;
                }else {
                    $dataBidBoq2 = BidMainWork::where('tender_id',$tenderId)->where('main_works_id',$main_works_id->id)->where('bid_master_id',$bidMasterId)->where('created_by',$supplierData->id)->get();
                    if(count($dataBidBoq2) > 0) {
                        foreach($dataBidBoq2 as $bidBoq){
                            if($bidBoq->total_amount >= 0 && isset($bidBoq->amount) && isset($bidBoq->qty)) {
                                $has_work_ids[$i] = "true";
                            }else {
                                $has_work_ids[$i]  = "false";
                            }
                            $i++;
                        }
                    }else {
                        $has_work_ids[$i]  = "false";
                        $i++;
                    }
                }
            }


        }

        $showTechnicalCriteria = TenderMaster::select('show_technical_criteria')->where('id', $tenderId)->first();

        if($evaluvationCriteriaDetailsCount == $bidSubmissionDataCount)  {
            $data['goNoGoStatus'] = 0;
        }else {
            $data['goNoGoStatus'] = 1;
        }

        if($evaluvationCriteriaDetailsCount == 0) {
            $data['goNoGoStatus'] = -1;
        }



        if(count($documentAttachedCountIds) == $documentAttachedCountAnswer) {
            $data['commonStatus'] = 0;
        }else {
            $data['commonStatus'] = 1;
        }

        if(count($documentAttachedCountIds) == 0) {
            $data['commonStatus'] = -1;
        }


        if((count($documentAttachedCountIdsTechnical) == $documentAttachedCountAnswerTechnical) && $bidSubmissionData['technicalEvaluationCriteria'] == 0) {
            $data['technicalStatus'] = 0;
        } else if((count($documentAttachedCountIdsTechnical) == $documentAttachedCountAnswerTechnical) && $showTechnicalCriteria['show_technical_criteria'] == 1) {
            $data['technicalStatus'] = 0;
        }else {
            $data['technicalStatus'] =1;
        }

        if($doc_type != 0)
        {
            $technicalEvaluationCriteriaExit = EvaluationCriteriaDetails::where('is_final_level', 1)
                ->where('critera_type_id', 2)
                ->where('tender_id', $tenderId)
                ->count();

            if((count($documentAttachedCountIdsTechnical)) == 0 && $technicalEvaluationCriteriaExit == 0)
            {
                $data['technicalStatus'] = -1;
            }
        }

        if((count($documentAttachedCountIdsCommercial) == $documentAttachedCountAnswerCommercial)) {
            if((count(array_flip($has_work_ids)) === 1 && end($has_work_ids) === 'true')) {
                $data['commercial_bid_submission_status'] = 0;
            }else {
                $data['commercial_bid_submission_status'] = 1;
            }
        }else {
            $data['commercial_bid_submission_status'] =1;
        }


        $activeTab = 0;

        if($activeTab == 0 &&  $data['goNoGoStatus'] != -1) {
            $activeTab = 1;
        }

        if($activeTab == 0 &&  $data['commonStatus'] != -1) {
            $activeTab = 2;
        }

        if($activeTab == 0 &&  $data['technicalStatus'] == 1) {
            $activeTab = 3;
        }

        if($activeTab == 0 &&  $data['commercial_bid_submission_status'] != -1) {
            $activeTab = 4;
        }

        $data['activeTab'] = $activeTab;
        $data['documentAttachedCountIdsCommercial'] = count($documentAttachedCountIdsCommercial);
        $data['documentAttachedCountIdsTechnical'] = count($documentAttachedCountIdsTechnical);

        if($negotiation){
            $tenderNegotiationArea = $this->getTenderNegotiationArea($tenderId);
            $bidSubmissionParentCode = TenderBidNegotiation::select('bid_submission_code_old')
                ->where('bid_submission_master_id_new', $bidMasterId)
                ->where('supplier_id', $supplierData->id)
                ->first();
            $data['pricing_schedule'] = $tenderNegotiationArea->pricing_schedule;
            $data['technical_evaluation'] = $tenderNegotiationArea->technical_evaluation;
            $data['tender_documents'] = $tenderNegotiationArea->tender_documents;
            $data['bidSubmissionParentCode'] = $bidSubmissionParentCode->bid_submission_code_old;
        }

        return [
            'success' => true,
            'message' => 'Main Envelop data retrieved successfully',
            'data' => $data
        ];
    }

    public function saveBidMainWork($request)
    {
        $tenderId = $request->input('extra.tenderMasterId');
        $bidMasterId = $request->input('extra.bidMasterId');
        $detail = $request->input('extra.detail');
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));

        DB::beginTransaction();
        try {
            $att['main_works_id'] = $detail['id'];
            $att['bid_master_id'] = $bidMasterId;
            $att['tender_id'] = $tenderId;
            $att['bid_format_detail_id'] = $detail['bid_format_detail_id'];
            $att['qty'] = $detail['bid_main_work']['qty'];
            $att['amount'] = $detail['bid_main_work']['amount'];
            $att['total_amount'] = round($detail['bid_main_work']['total_amount'],3);
            $att['remarks'] = $detail['bid_main_work']['remarks'];
            $att['supplier_registration_id'] = $supplierRegId;

            if (isset($detail['bid_main_work']['id'])) {
                if (empty($detail['bid_main_work']['qty']) && empty($detail['bid_main_work']['amount']) && empty($detail['bid_main_work']['remarks'])) {
                    $result = BidMainWork::where('id', $detail['bid_main_work']['id'])->delete();
                } else {
                    $att['updated_at'] = Carbon::now();
                    $att['updated_by'] = $supplierRegId;
                    $result = BidMainWork::where('id', $detail['bid_main_work']['id'])->update($att);
                }
            } else {
                $att['created_at'] = Carbon::now();
                $att['created_by'] = $supplierRegId;
                $result = BidMainWork::create($att);
            }


          $pricing_shedule = PricingScheduleDetail::where('id',$detail['id'])->first();


        $outcome = DB::table('srm_pricing_schedule_detail')->where('bid_format_id',$detail['bid_format_id'])->where('pricing_schedule_master_id',$detail['pricing_schedule_master_id'])
        //->leftJoin('srm_schedule_bid_format_details', 'srm_pricing_schedule_detail.id', '=', 'srm_schedule_bid_format_details.bid_format_detail_id')
        ->join('tender_field_type', 'srm_pricing_schedule_detail.field_type', '=', 'tender_field_type.id')
        ->leftJoin('srm_bid_main_work', function($join) use($bidMasterId){
            $join->on('srm_pricing_schedule_detail.id', '=', 'srm_bid_main_work.main_works_id');
            $join->where('srm_bid_main_work.bid_master_id',$bidMasterId) ;
        })
        ->leftJoin('srm_schedule_bid_format_details', function($join) use($bidMasterId){
            $join->on('srm_pricing_schedule_detail.id', '=', 'srm_schedule_bid_format_details.bid_format_detail_id');
         //   $join->where('srm_schedule_bid_format_details.bid_master_id',$bidMasterId) ;
           // $join->orWhere('srm_schedule_bid_format_details.bid_master_id', null);
        })
        ->select('srm_pricing_schedule_detail.id as id','srm_pricing_schedule_detail.is_disabled','srm_pricing_schedule_detail.field_type as typeId','srm_pricing_schedule_detail.formula_string','srm_pricing_schedule_detail.bid_format_detail_id','srm_schedule_bid_format_details.bid_master_id',
                DB::raw('(CASE WHEN srm_pricing_schedule_detail.field_type = 4 THEN srm_schedule_bid_format_details.value 
                            WHEN (srm_pricing_schedule_detail.field_type != 4 && srm_pricing_schedule_detail.is_disabled = 1) THEN srm_schedule_bid_format_details.value    
                            WHEN (srm_pricing_schedule_detail.field_type != 4 && srm_pricing_schedule_detail.boq_applicable = 1) THEN srm_bid_main_work.total_amount    
                            WHEN (srm_pricing_schedule_detail.is_disabled = 0 && srm_pricing_schedule_detail.boq_applicable = 0) THEN srm_bid_main_work.total_amount  
                            END) AS value'))
        ->get();

        $details = array_map(function($item) {
            return (array)$item;
        }, $outcome->toArray());




            $formula_cal = PirceBidFormula::process($details,$tenderId);

            foreach($formula_cal as $val)
            {
                foreach($val as $key=>$val1)
                {
                    $formatted_val =  round($val1, 3);

                    $flight = ScheduleBidFormatDetails::updateOrCreate(
                        ['bid_format_detail_id' => $key, 'schedule_id' => $pricing_shedule->pricing_schedule_master_id,'bid_master_id'=>$bidMasterId],
                        ['value' => $formatted_val,'bid_master_id',$bidMasterId]
                    );

                }


            }



            DB::commit();
            return [
                'success' => true,
                'message' => 'Successfully Saved',
                'data' =>  $result
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }

    public function getBidBoqData($request)
    {
        $mainWorkId = $request->input('extra.mainWorkId');
        $bidMasterId = $request->input('extra.bidMasterId');
        $data['boqItems'] = TenderBoqItems::with(['unit', 'bid_boq' => function ($q) use ($bidMasterId) {
            $q->where('bid_master_id', $bidMasterId);
        }])->where('main_work_id', $mainWorkId)->get();

        $data['bidSubmitted'] = $this->getBidMasterData($bidMasterId);

        return [
            'success' => true,
            'message' => 'Successfully Received',
            'data' =>  $data
        ];
    }

    public function saveBidBoq($request)
    {
        $mainWorkId = $request->input('extra.mainWorkId');
        $bidMasterId = $request->input('extra.bidMasterId');
        $detail = $request->input('extra.detail');
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));

        $tenderMainWork = PricingScheduleDetail::where('id', $detail['main_work_id'])->first();

        DB::beginTransaction();
        try {
            $att['boq_id'] = $detail['id'];
            $att['bid_master_id'] = $bidMasterId;
            $att['main_works_id'] = $detail['main_work_id'];
            $att['qty'] = $detail['bid_boq']['qty'];
            $att['remarks'] = isset($detail['bid_boq']['remarks']) ? $detail['bid_boq']['remarks'] : null;
            $att['unit_amount'] = $detail['bid_boq']['unit_amount'];
            $att['total_amount'] = round($detail['bid_boq']['total_amount'],3);
            $att['supplier_registration_id'] = $supplierRegId;

            if (isset($detail['bid_boq']['id'])) {
                if (empty($detail['bid_boq']['unit_amount']) && empty($detail['bid_boq']['qty'])) {
                    $result = BidBoq::where('id', $detail['bid_boq']['id'])->delete();
                } else {
                    $att['updated_at'] = Carbon::now();
                    $att['updated_by'] = $supplierRegId;
                    $result = BidBoq::where('id', $detail['bid_boq']['id'])->update($att);
                }
            } else {
                $att['created_at'] = Carbon::now();
                $att['created_by'] = $supplierRegId;
                $result = BidBoq::create($att);
            }
            $boqTot = BidBoq::selectRaw('SUM(qty) as qty , SUM(total_amount) as total_amount')->where('bid_master_id', $bidMasterId)->where('main_works_id', $detail['main_work_id'])->first();

            if (!empty($boqTot['qty'])) {
                $bidMainWork = BidMainWork::where('main_works_id', $detail['main_work_id'])->where('bid_master_id', $bidMasterId)->first();
                if (!empty($bidMainWork)) {
                    $mainWork['qty'] = $boqTot['qty'];
                    $mainWork['total_amount'] = round($boqTot['total_amount'],3);
                    $mainWork['updated_at'] = Carbon::now();
                    $mainWork['updated_by'] = $supplierRegId;
                    BidMainWork::where('id', $bidMainWork['id'])->update($mainWork);
                } else {

                    $mainWork['main_works_id'] = $detail['main_work_id'];
                    $mainWork['bid_master_id'] = $bidMasterId;
                    $mainWork['tender_id'] = $tenderMainWork['tender_id'];
                    $mainWork['bid_format_detail_id'] = $tenderMainWork['bid_format_detail_id'];
                    $mainWork['qty'] = $boqTot['qty'];
                    $mainWork['total_amount'] = round($boqTot['total_amount'],3);
                    $mainWork['supplier_registration_id'] = $supplierRegId;

                    $mainWork['created_at'] = Carbon::now();
                    $mainWork['created_by'] = $supplierRegId;
                    BidMainWork::create($mainWork);
                }



        $pricing_shedule = PricingScheduleDetail::where('id',$mainWorkId)->first();

        $outcome = DB::table('srm_pricing_schedule_detail')->where('bid_format_id',$pricing_shedule->bid_format_id)->where('pricing_schedule_master_id',$pricing_shedule->pricing_schedule_master_id)
         //->leftJoin('srm_schedule_bid_format_details', 'srm_pricing_schedule_detail.id', '=', 'srm_schedule_bid_format_details.bid_format_detail_id')
         ->join('tender_field_type', 'srm_pricing_schedule_detail.field_type', '=', 'tender_field_type.id')
         ->leftJoin('srm_bid_main_work', function($join) use($bidMasterId){
            $join->on('srm_pricing_schedule_detail.id', '=', 'srm_bid_main_work.main_works_id');
            $join->where('srm_bid_main_work.bid_master_id',$bidMasterId) ;
         })
         ->leftJoin('srm_schedule_bid_format_details', function($join) use($bidMasterId){
            $join->on('srm_pricing_schedule_detail.id', '=', 'srm_schedule_bid_format_details.bid_format_detail_id');
            //$join->where('srm_schedule_bid_format_details.bid_master_id',$bidMasterId) ;
        })
        ->select('srm_pricing_schedule_detail.id as id','srm_pricing_schedule_detail.is_disabled','srm_pricing_schedule_detail.field_type as typeId','srm_pricing_schedule_detail.formula_string','srm_pricing_schedule_detail.bid_format_detail_id',
                DB::raw('(CASE WHEN srm_pricing_schedule_detail.field_type = 4 THEN srm_schedule_bid_format_details.value 
                               WHEN (srm_pricing_schedule_detail.field_type != 4 && srm_pricing_schedule_detail.is_disabled = 1) THEN srm_schedule_bid_format_details.value    
                               WHEN (srm_pricing_schedule_detail.field_type != 4 && srm_pricing_schedule_detail.boq_applicable = 1) THEN srm_bid_main_work.total_amount    
                               WHEN (srm_pricing_schedule_detail.is_disabled = 0 && srm_pricing_schedule_detail.boq_applicable = 0) THEN srm_bid_main_work.total_amount 
                               END) AS value'))
        ->get();

        $details = array_map(function($item) {
            return (array)$item;
        }, $outcome->toArray());



            $formula_cal = PirceBidFormula::process($details,$tenderMainWork['tender_id']);

            foreach($formula_cal as $val)
            {
                foreach($val as $key=>$val1)
                {
                    $formatted_val =  round($val1, 3);
                    $flight = ScheduleBidFormatDetails::updateOrCreate(
                        ['bid_format_detail_id' => $key, 'schedule_id' => $pricing_shedule->pricing_schedule_master_id,'bid_master_id'=>$bidMasterId],
                        ['value' => $val1,'bid_master_id',$bidMasterId]
                    );

                }


            }


            } else {
                BidMainWork::where('main_works_id', $detail['main_work_id'])->where('bid_master_id', $bidMasterId)->delete();
            }

            DB::commit();
            return [
                'success' => true,
                'message' => 'Successfully Saved',
                'data' =>  $result
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }
    public function submitBidTender($request)
    {
        $bidMasterId = $request->input('extra.bidMasterId');
        $tenderId = $request->input('extra.tenderId');
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));

        DB::beginTransaction();
        try {
            $updateData['status'] = 1;
            $updateData['updated_at'] = Carbon::now();
            $updateData['updated_by'] = $supplierRegId;
            $updateData['bidSubmittedYN'] = 1;
            $updateData['bidSubmitedBySupID'] = $supplierRegId;
            $updateData['bidSubmittedDatetime'] = Carbon::now();
            $result = BidSubmissionMaster::where('id', $bidMasterId)
                ->where('tender_id', $tenderId)
                ->update($updateData);
            DB::commit();
            return [
                'success' => true,
                'message' => 'Successfully Saved',
                'data' =>  $result
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }

    public function getBidMasterData($bidMasterId)
    {
        $bidMaster = BidSubmissionMaster::where('id', $bidMasterId)->first();
        if ($bidMaster['status'] == 1) {
            return 1;
        } else {
            return 0;
        }
    }
    public function BidSubmissionStatusData($bidMasterId, $tenderId)
    {
        $technicalEvaluationCriteria = EvaluationCriteriaDetails::whereDoesntHave('bid_submission_detail', function ($q2) use ($tenderId, $bidMasterId) {
            $q2->where('bid_master_id', $bidMasterId);
            $q2->where('tender_id', $tenderId);
        })
            ->where('is_final_level', 1)
            ->where('critera_type_id', 2)
            ->where('tender_id', $tenderId)
            ->count();

        $pricingScheduleMaster =  PricingScheduleMaster::with(['tender_main_works' => function ($q1) use ($tenderId, $bidMasterId) {
            $q1->where('tender_id', $tenderId);
            $q1->with(['bid_main_work' => function ($q2) use ($tenderId, $bidMasterId) {
                $q2->where('tender_id', $tenderId);
                $q2->where('bid_master_id', $bidMasterId);
                $q2->with(['tender_boq_items' => function ($q3)  use ($bidMasterId) {
                    $q3->whereDoesntHave('bid_boq', function ($query) use ($bidMasterId) {
                        $query->where('bid_master_id', '=', $bidMasterId);
                    });
                }]);
            }]);
        }])
            ->where('tender_id', $tenderId)
            ->get();


        $tenderArr = collect($pricingScheduleMaster)->map(function ($group) {
            return $group['tender_main_works'];
        });

        $singleArr = Arr::flatten($tenderArr);

        $tenderArrFilter = collect($singleArr)->map(function ($group) {
            if ($group['bid_main_work'] == null) {
                $group['isExist'] = 1;
            } else if (count($group['bid_main_work']['tender_boq_items']) == 0 && ($group['bid_main_work']['total_amount'] == 0 && $group['bid_main_work']['qty'] == 0)) {
                $group['isExist'] = 1;
            } else if (count($group['bid_main_work']['tender_boq_items']) > 0) {
                $group['isExist'] = 1;
            } else {
                $group['isExist'] = 0;
            }
            return $group['isExist'];
        });


        $filtered = $tenderArrFilter->filter(function ($value, $key) {
            return $value > 0;
        });
        $filtered->all();

        $bidsubmission =  self::getBidMasterData($bidMasterId);

        $data['tenderArrFilter'] = $tenderArrFilter;
        $data['filtered'] = $filtered->count();
        $data['technicalEvaluationCriteria'] = $technicalEvaluationCriteria > 0 ? 1 : 0;
        $data['bidsubmission'] = $bidsubmission;

        return $data;
    }
    public function submitBidSubmissionCreate($request)
    {
        $tenderId = $request->input('extra.tenderId');
        $noOfBids = $request->input('extra.noOfBids') + 1;
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $lastSerialNumber = 1;




        DB::beginTransaction();
        try {


            $lastSerialNo = BidSubmissionMaster::orderBy('id', 'desc')
            ->first();

            if(isset($lastSerialNo->serialNumber) && $lastSerialNo->serialNumber != null)
            {

                $lastSerialValue = 1;
                if ($lastSerialNo) {
                    $lastSerialValue = intval($lastSerialNo->serialNumber) + 1;
                }

                $att['serialNumber'] = $lastSerialValue;
                $att['bidSubmissionCode'] = 'Bid_'.str_pad($lastSerialValue, 10, '0', STR_PAD_LEFT);

            }
            else
            {
                $att['serialNumber'] = 1;
                $att['bidSubmissionCode'] = 'Bid_'.str_pad(1, 10, '0', STR_PAD_LEFT);
            }


            $lastSerial = BidSubmissionMaster::where('tender_id', $tenderId)
				->where('supplier_registration_id', $supplierRegId)
				->orderBy('id', 'desc')
				->first();
			if ($lastSerial) {
				$lastSerialNumber = intval($lastSerial->bid_sequence) + 1;
			}

            $att['tender_id'] = $tenderId;
            $att['supplier_registration_id'] = $supplierRegId;
            $att['uuid'] = Uuid::generate()->string;
            $att['bid_sequence'] = 1;
            $att['created_at'] = Carbon::now();
            $att['created_by'] = $supplierRegId;
            $att['bid_sequence'] = $lastSerialNumber;
            $result = BidSubmissionMaster::create($att);

            $bidMasterId = $result['id'];

            $submittedCount = BidSubmissionMaster::where('tender_id',$tenderId)
            ->where('supplier_registration_id',$supplierRegId)
            ->get();

            if( count($submittedCount) > $noOfBids){
                return [
                    'success' => false,
                    'message' => 'Cannot have more than '.(int)$noOfBids.' bids for this tender',
                    'data' =>  ' '
                ];
            }

            $details = PricingScheduleMaster::with(['tender_bid_format_master', 'bid_schedule' , 'pricing_shedule_details'=>function($query){
                $query->where('field_type',4);
            }])->where('tender_id', $tenderId)->get();

            foreach($details as $detail)
            {

                foreach($detail->pricing_shedule_details as $bid)
                {
                        $data['bid_format_detail_id'] = $bid->id;
                        $data['schedule_id'] = $bid->pricing_schedule_master_id;
                        $data['value'] = null;
                        $data['created_by'] = $supplierRegId;
                        $data['company_id'] = $bid->company_id;
                        $data['bid_master_id'] = $result['id'];
                        $results = ScheduleBidFormatDetails::create($data);

                }


                if(count($detail->pricing_shedule_details) > 0)
                {
                    $outcome = DB::table('srm_pricing_schedule_detail')->where('bid_format_id',$detail->price_bid_format_id)->where('pricing_schedule_master_id',$detail->id)
                    //->leftJoin('srm_schedule_bid_format_details', 'srm_pricing_schedule_detail.id', '=', 'srm_schedule_bid_format_details.bid_format_detail_id')
                    ->join('tender_field_type', 'srm_pricing_schedule_detail.field_type', '=', 'tender_field_type.id')
                    ->leftJoin('srm_bid_main_work', function($join) use($bidMasterId){
                        $join->on('srm_pricing_schedule_detail.id', '=', 'srm_bid_main_work.main_works_id');
                        $join->where('srm_bid_main_work.bid_master_id',$bidMasterId) ;
                    })
                    ->leftJoin('srm_schedule_bid_format_details', function($join) use($bidMasterId){
                        $join->on('srm_pricing_schedule_detail.id', '=', 'srm_schedule_bid_format_details.bid_format_detail_id');
                        //$join->where('srm_schedule_bid_format_details.bid_master_id',$bidMasterId) ;
                    })
                    ->select('srm_pricing_schedule_detail.id as id','srm_pricing_schedule_detail.is_disabled','srm_pricing_schedule_detail.field_type as typeId','srm_pricing_schedule_detail.formula_string','srm_pricing_schedule_detail.bid_format_detail_id',
                            DB::raw('(CASE WHEN srm_pricing_schedule_detail.field_type = 4 THEN srm_schedule_bid_format_details.value 
                                        WHEN (srm_pricing_schedule_detail.field_type != 4 && srm_pricing_schedule_detail.is_disabled = 1) THEN srm_schedule_bid_format_details.value    
                                        WHEN (srm_pricing_schedule_detail.field_type != 4 && srm_pricing_schedule_detail.boq_applicable = 1) THEN srm_bid_main_work.total_amount    
                                        WHEN (srm_pricing_schedule_detail.is_disabled = 0 && srm_pricing_schedule_detail.boq_applicable = 0) THEN srm_bid_main_work.total_amount 
                                        END) AS value'))
                    ->get();

                    $details_obj = array_map(function($item) {
                        return (array)$item;
                    }, $outcome->toArray());


                    $formula_cal = PirceBidFormula::process($details_obj,$tenderId);

                    foreach($formula_cal as $val)
                    {
                        foreach($val as $key=>$val1)
                        {
                            $formatted_val =  round($val1, 3);
                            $flight = ScheduleBidFormatDetails::updateOrCreate(
                                ['bid_format_detail_id' => $key, 'schedule_id' => $detail->id,'bid_master_id'=>$bidMasterId],
                                ['value' => $val1,'bid_master_id',$bidMasterId]
                            );

                        }


                    }
                }



            }

            DB::commit();
            return [
                'success' => true,
                'message' => 'Successfully bid created',
                'data' =>  $result
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }
    public function getBidSubmittedData($request)
    {
        $tenderId = $request->input('extra.tenderId');
        $tenderNegotiation = $request->input('extra.tender_negotiation');
        $tenderNegotiationData = $request->input('extra.tender_negotiation_data');
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $bidSubmitted = BidSubmissionMaster::with(['SupplierRegistrationLink']);

        if ($tenderNegotiation) {
            $bidSubmitted->whereHas('TenderBidNegotiation', function ($query) use ($tenderNegotiationData) {
                $query->where('bid_submission_code_old' , $tenderNegotiationData[0]['supplier_tender_negotiation']['bidSubmissionCode']);
            });
        } else {
            $bidSubmitted->whereDoesntHave('TenderBidNegotiation', function ($query) use ($tenderNegotiationData) {
                $query->where('bid_submission_code_old', '!=' ,$tenderNegotiationData[0]['supplier_tender_negotiation']['bidSubmissionCode']);
            });
        }

        $bidSubmitted = $bidSubmitted->where('tender_id', $tenderId)
            ->where('supplier_registration_id', $supplierRegId)
            ->orderBy('id', 'ASC')
            ->get();

        $bidSubmitted = collect($bidSubmitted)->map(function ($group) {
            $bidMasterId = $group['id'];
            $bidSubmissionData = self::BidSubmissionStatusData($group['id'], $group['tender_id']);
            $tender = $group['tender_id'];
            $type = TenderMaster::where('id',$tender)->select('document_type','envelop_type_id')->first();

            $assignDocumentTypesDeclared = [1,2,3];
            $assignDocumentTypes = TenderDocumentTypeAssign::where('tender_id',$tender)->pluck('document_type_id')->toArray();
            $doucments = (array_merge($assignDocumentTypesDeclared,$assignDocumentTypes));

            $attachments = DocumentAttachments::with(['tender_document_types' => function ($q) use ($doucments){
                $q->whereIn('id',$doucments);
                $q->where('srm_action', 1);

            }, 'document_attachments' => function ($q) use ($bidMasterId) {
                $q->where('documentSystemCode', $bidMasterId);
            }])->whereHas('tender_document_types', function ($q) use ($doucments){
                $q->whereIn('id',$doucments);
                $q->where('srm_action', 1);
            })->where('documentSystemCode', $tender)->where(function($query) use($type){
                if($type->document_type == 0)
                {
                    $type = 108;
                }
                else
                {
                    $type = 113;
                }
                $query->where('documentSystemID', $type);

            })->where('parent_id', null)->where('envelopType', 2)->count();


            $criteriaDetail = EvaluationCriteriaDetails::with(['evaluation_criteria_score_config', 'evaluation_criteria_type', 'tender_criteria_answer_type', 'bid_submission_detail' => function ($q) use ($bidMasterId) {
                $q->where('bid_master_id', $bidMasterId);
            }, 'child' => function ($q) use ($bidMasterId) {
                $q->with(['evaluation_criteria_score_config', 'evaluation_criteria_type', 'tender_criteria_answer_type', 'bid_submission_detail' => function ($q) use ($bidMasterId) {
                    $q->where('bid_master_id', $bidMasterId);
                }, 'child' => function ($q) use ($bidMasterId) {
                    $q->with(['evaluation_criteria_score_config', 'evaluation_criteria_type', 'tender_criteria_answer_type', 'bid_submission_detail' => function ($q) use ($bidMasterId) {
                        $q->where('bid_master_id', $bidMasterId);
                    }, 'child' => function ($q) use ($bidMasterId) {
                        $q->with(['evaluation_criteria_score_config', 'evaluation_criteria_type', 'tender_criteria_answer_type', 'bid_submission_detail' => function ($q) use ($bidMasterId) {
                            $q->where('bid_master_id', $bidMasterId);
                        }]);
                    }]);
                }]);
            }])->where('tender_id', $tender)->where('level', 1)->where('critera_type_id', 2)->count();
            $group['technical_status'] = true;
            if($criteriaDetail == 0 && $attachments == 0)
            {
                $group['technical_status'] = false;
            }

            $evaluvationCriteriaDetailsCount = EvaluationCriteriaDetails::where('tender_id',$group['tender_id'])->where('critera_type_id',1)->count();
            $bidSubmissionDataCount = BidSubmissionDetail::join('srm_evaluation_criteria_details','srm_bid_submission_detail.evaluation_detail_id','=','srm_evaluation_criteria_details.id')
                ->where('srm_bid_submission_detail.tender_id',$group['tender_id'])
                ->where('srm_bid_submission_detail.bid_master_id',$bidMasterId)
                ->where('srm_evaluation_criteria_details.critera_type_id',1)
                ->count();
            $goNoGoActiveStatus = TenderMaster::select('is_active_go_no_go')->where('id', $group['tender_id'])->first();

            $document_type = TenderMaster::select('document_type')->where('id', $group['tender_id'])->first();

            $group['document_type'] =  $document_type['document_type'];

            $documentTypeAssignedCount = TenderDocumentTypeAssign::where('tender_id',$group['tender_id'])->count();

            $documents = [];
            $documentAttachedCountIds = DocumentAttachments::with(['tender_document_types' => function ($q) use ($documents){
                $q->where('srm_action', 1);
            }, 'document_attachments' => function ($q) use ($bidMasterId) {
                $q->where('documentSystemCode', $bidMasterId);
            }])->whereHas('tender_document_types', function ($q) use ($documents){
            })->where('documentSystemCode', $group['tender_id'])->where('parent_id', null)
            ->where(function($query) use($type){
                if($type->document_type == 0)
                {
                    $type = 108;
                }
                else
                {
                    $type = 113;
                }
                $query->where('documentSystemID', $type);

            })
            ->where('envelopType', 3)->where('attachmentType',2)->pluck('attachmentID')->toArray();

            $documentAttachedCountAnswer = DocumentAttachments::whereIn('parent_id', $documentAttachedCountIds)
                ->where(function($query) use($type){
                    if($type->document_type == 0)
                    {
                        $type = 108;
                    }
                    else
                    {
                        $type = 113;
                    }
                    $query->where('documentSystemID', $type);

                })
                ->where('documentSystemCode', $bidMasterId)
                ->count();

            if($goNoGoActiveStatus['is_active_go_no_go'] == 1){
                if($evaluvationCriteriaDetailsCount == $bidSubmissionDataCount)  {
                    $group['goNoGoStatus'] = 0;
                } else {
                    $group['goNoGoStatus'] = 1;
                }
                $group['is_active_go_no_go'] = 1;
            } else if($goNoGoActiveStatus['is_active_go_no_go'] == 0) {
                $group['is_active_go_no_go'] = -1;
            }

            if(count($documentAttachedCountIds) != 0){
                if(count($documentAttachedCountIds) == $documentAttachedCountAnswer || count($documentAttachedCountIds) == 0) {
                    $group['commonStatus'] = 0;
                } else {
                    $group['commonStatus'] = 1;
                }
                $group['is_active_common_docs'] = 1;
            } else {
                $group['is_active_common_docs'] = -1;
            }

            $documentAttachedCountIdsCommercial = DocumentAttachments::with(['tender_document_types' => function ($q) {
                $q->where('srm_action', 1);
            }, 'document_attachments' => function ($q) use ($bidMasterId) {
                $q->where('documentSystemCode', $bidMasterId);
            }])->whereHas('tender_document_types', function ($q) {
            })->where('documentSystemCode', $group['tender_id'])->where('parent_id', null)
            ->where(function($query) use($type){
                if($type->document_type == 0)
                {
                    $type = 108;
                }
                else
                {
                    $type = 113;
                }
                $query->where('documentSystemID', $type);

            })
            ->where('envelopType', 1)->where('attachmentType',2)->pluck('attachmentID')->toArray();

            $documentAttachedCountAnswerCommercial = DocumentAttachments::whereIn('parent_id', $documentAttachedCountIdsCommercial)
            ->where(function($query) use($type){
                if($type->document_type == 0)
                {
                    $type = 108;
                }
                else
                {
                    $type = 113;
                }
                $query->where('documentSystemID', $type);

            })
            ->where('documentSystemCode', $bidMasterId)->count();

            $pring_schedul_master_ids =  PricingScheduleMaster::with(['tender_main_works' => function ($q1) use ($tender, $bidMasterId) {
                $q1->where('tender_id', $tender);
                $q1->with(['bid_main_work' => function ($q2) use ($tender, $bidMasterId) {
                    $q2->where('tender_id', $tender);
                    $q2->where('bid_master_id', $bidMasterId);
                }]);
            }])
                ->where('tender_id', $tender)
                ->where('status',1)->pluck('id')->toArray();

            $main_works_ids = PricingScheduleDetail::whereIn('pricing_schedule_master_id',$pring_schedul_master_ids)
                ->where('is_disabled',0)
                ->select('id','boq_applicable','field_type','bid_format_detail_id','is_disabled')
                ->get();
            $has_work_ids = Array();
            $i = 0;

            foreach($main_works_ids as $main_works_id) {
                if($main_works_id->boq_applicable) {
                    $boqItems = TenderBoqItems::where('main_work_id',$main_works_id->id)->get();
                    foreach($boqItems as $boqItem) {
                        $dataBidBoq = BidBoq::where('boq_id',$boqItem->id)->where('bid_master_id',$bidMasterId)->where('main_works_id',$main_works_id->id)->get();

                        if(count($dataBidBoq) > 0) {
                            foreach($dataBidBoq as $bidBoq){
                                if($bidBoq->total_amount >= 0 && isset($bidBoq->unit_amount) && isset($bidBoq->qty)) {
                                    $has_work_ids[$i] = "true";
                                } else {
                                    $has_work_ids[$i]  = "false";
                                }
                                $i++;
                            }
                        } else {
                            $has_work_ids[$i]  = "false";
                            $i++;
                        }
                    }
                } else {
                    if($main_works_id->field_type == 4) {
                        $bid_format_details = DB::table('srm_schedule_bid_format_details')->where('bid_format_detail_id', $main_works_id->id)->get();
                        if(count($bid_format_details) > 0) {
                            $has_work_ids[$i] = "true";
                        }else {
                            $has_work_ids[$i]  = "false";
                        }
                        $i++;
                    } else {
                        $dataBidBoq = BidMainWork::where('tender_id', $tender)
                            ->where('main_works_id', $main_works_id->id)
                            ->where('bid_master_id', $bidMasterId)
                            ->get();

                        if(count($dataBidBoq) > 0) {
                            foreach($dataBidBoq as $bidBoq){
                                if($bidBoq->total_amount >= 0 && isset($bidBoq->amount) && isset($bidBoq->qty)) {
                                    $has_work_ids[$i] = "true";
                                }else {
                                    $has_work_ids[$i]  = "false";
                                }
                                $i++;
                            }
                        } else {
                            $has_work_ids[$i]  = "false";
                            $i++;
                        }
                    }
                }
            }

            /*$bid_boq = BidBoq::where('bid_master_id',$bidMasterId)->count();
            $bid_boq_answer = BidBoq::where('bid_master_id',$bidMasterId)->where('total_amount','>',0)->count();*/

            if((count($documentAttachedCountIdsCommercial) == $documentAttachedCountAnswerCommercial)) {
                if((count(array_flip($has_work_ids)) === 1 && end($has_work_ids) === 'true')) {
                    $group['commercial_bid_submission_status'] = "Completed";
                } else {
                    $group['commercial_bid_submission_status'] = "Not Completed";
                }
            } else {
                $group['commercial_bid_submission_status'] = "Not Completed";
            }
            /*if($bid_boq == 0 && count($documentAttachedCountIdsCommercial) == 0) {
                $group['commercial_bid_submission_status'] = "Not Completed";
            }*/

            $documentAttachedCountIdsTechnical = DocumentAttachments::with(['tender_document_types' => function ($q) use ($doucments){
                $q->where('srm_action', 1);
            }, 'document_attachments' => function ($q) use ($bidMasterId) {
                $q->where('documentSystemCode', $bidMasterId);
            }])->whereHas('tender_document_types', function ($q) use ($doucments){
            })->where('documentSystemCode', $tender)->where('parent_id', null)
            ->where(function($query) use($type){
                if($type->document_type == 0)
                {
                   $type = 108;
                }
                else
                {
                   $type = 113;
                }
                $query->where('documentSystemID', $type);
            })
            ->where('envelopType', 2)->where('attachmentType',2)->pluck('attachmentID')->toArray();

            $documentAttachedCountAnswerTechnical = DocumentAttachments::whereIn('parent_id', $documentAttachedCountIdsTechnical)
            ->where(function($query) use($type){
                if($type->document_type == 0)
                {
                   $type = 108;
                }
                else
                {
                   $type = 113;
                }
                $query->where('documentSystemID', $type);
            })
            ->where('documentSystemCode', $bidMasterId)->count();


            $showTechnicalCriteria = TenderMaster::select('show_technical_criteria')->where('id', $tender)->first();

            if((count($documentAttachedCountIdsTechnical) == $documentAttachedCountAnswerTechnical) && $bidSubmissionData['technicalEvaluationCriteria'] == 0) {
                $group['technical_bid_submission_status'] = 0;
            } else if( (count($documentAttachedCountIdsTechnical) == $documentAttachedCountAnswerTechnical) && $showTechnicalCriteria['show_technical_criteria'] == 1){
                $group['technical_bid_submission_status'] = 0;
            }else {
                $group['technical_bid_submission_status'] =1;
            }

            //$group['technical_bid_submission_status'] = $bidSubmissionData['technicalEvaluationCriteria'];
            $group['bid_submission_status'] = $bidSubmissionData['bidsubmission'];
            $group['documentAttachedCountIdsCommercial'] = count($documentAttachedCountIdsCommercial);
            $group['documentAttachedCountIdsTechnical'] = count($documentAttachedCountIdsTechnical);

            $tenderNegotiationArea =  $this->getTenderNegotiationArea($tender);
            if($tenderNegotiationArea != null){
                $group['pricing_schedule'] = $tenderNegotiationArea->pricing_schedule;
                $group['technical_evaluation'] = $tenderNegotiationArea->technical_evaluation;
                $group['tender_documents'] = $tenderNegotiationArea->tender_documents;
            }

            return $group;
        });



        if(!empty($bidSubmitted) && count($bidSubmitted) > 0){
            return [
                'success' => true,
                'message' => 'Successfully retrived',
                'data' =>  $bidSubmitted
            ];
        }else {
            return [
                'success' => true,
                'message' => 'Successfully retrived Data',
                'data' =>  ' '
            ];
        }

    }

    public function deleteBidData($request){
        $tenderId = $request->input('extra.tenderId');
        $bidId = $request->input('extra.bidId');
        $supplierRegId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
        $tenderNegotiation = $request->input('extra.tender_negotiation');

        BidSubmissionMaster::where('tender_id', $tenderId)
        ->where('id',$bidId)
        ->delete();

        BidBoq::where('bid_master_id',$bidId)->delete();

        BidMainWork::where('bid_master_id',$bidId)
        ->where('tender_id',$tenderId)
        ->delete();

        BidSchedule::where('tender_id',$tenderId)
        ->where('bid_master_id',$bidId)
        ->delete();

        BidSubmissionDetail::where('bid_master_id',$bidId)
        ->where('tender_id',$tenderId)
        ->delete();

        DocumentAttachments::where('documentSystemID',108)
        ->whereNotNull('parent_id')
        ->where('documentSystemCode',$bidId)
        ->delete();

        if($tenderNegotiation){
            TenderBidNegotiation::where('bid_submission_master_id_new', $bidId)->delete();
        }

        if(ScheduleBidFormatDetails::where('bid_master_id',$bidId)->count() > 0)
        {
            ScheduleBidFormatDetails::where('bid_master_id',$bidId)
            ->delete();

        }


        return [
            'success' => true,
            'message' => 'Successfully retrived',
            'data' =>  ' '
        ];
    }

    public function exportReport(Request $request)
    {
        $tenderId = $request->input('extra.tenderId');
        $reportID = $request->input('extra.reportID');

        switch ($reportID) {
            case 'FAQ':
                $type = 'xlsx';
                $supplierId = self::getSupplierRegIdByUUID($request->input('supplier_uuid'));
                $erpUser = '';
                $data = array();
                $parentIdArr = array();
                $nonParentIdArr = array();
                $dataPrebid = array();

                $output = DB::table("srm_tender_faq")
                    ->selectRaw("srm_tender_faq.question,
                                srm_tender_faq.answer")
                    ->where('tender_master_id', $tenderId)
                    ->orderBy('srm_tender_faq.id', 'ASC')->get();
                $prebidDate = $this->getPreBidClarificationsResponseForExcel($tenderId);

                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $x++;
                        $data[$x]['Question'] = $val->question;
                        $data[$x]['Answer'] = html_entity_decode(strip_tags($val->answer));
                    }
                }

                if(sizeof($output) == 0){
                    $data[$x]['Question'] = '';
                    $data[$x]['Answer'] = '';
                }

                $x = 0;
                if((sizeof($prebidDate) == 0) && (sizeof($output) == 0)){
                    return [
                        'success' => false,
                        'message' => 'There are no FAQ or Pre bid clarifications to download',
                        'data' =>  new stdClass()
                    ];
                }
                foreach ($prebidDate as $val) {
                    foreach ($val as $valIn) {
                        if(!($valIn['is_public'] === 0 && $supplierId !== $valIn['created_by'])) {
                            $x++;
                            if ($supplierId == $valIn['created_by']) {
                                $supplierName = $valIn['supplier']['name'];
                            } elseif (($supplierId != $valIn['created_by']) && ($valIn['is_anonymous'] == 0)) {
                                $supplierName = $valIn['supplier']['name'];
                            } elseif (($supplierId != $valIn['created_by']) && ($valIn['is_anonymous'] == 1)) {
                                $supplierName = "Anonymous";
                            }

                            if(isset($valIn['employee'])){
                                $erpUser = $valIn['employee']['empName'];
                            } else {
                                $erpUser = '';
                            }

                            if ($valIn['parent_id'] === 0) {
                                $parentIdArr[] = $x + 1;
                            } else {
                                $nonParentIdArr[] = $x + 1;
                            }

                            $dataPrebid[$x]['Question Id'] = $valIn['id'];
                            $dataPrebid[$x]['Supplier'] = isset($valIn['supplier']['name']) ? $supplierName : $erpUser;
                            $dataPrebid[$x]['Question / Answer'] = html_entity_decode(strip_tags($valIn['post']));
                            $dataPrebid[$x]['Parent Question Id'] = $valIn['parent_id'];
                            $dataPrebid[$x]['Publish as'] = ($valIn['is_public'] === 0) ? "Private" : "Public";
                            $dataPrebid[$x]['Created At'] = Carbon::createFromFormat('Y-m-d H:i:s', $valIn['created_at'])->format('Y-m-d H:i A');
                            $dataPrebid[$x]['Is Thread Closed'] = ($valIn['is_closed'] === 1) ? 'Yes' : 'No';
                        }
                    }
                }

                if(sizeof($prebidDate) == 0){
                    $dataPrebid[$x]['Question Id'] = '';
                    $dataPrebid[$x]['Supplier'] = '';
                    $dataPrebid[$x]['Question / Answer'] = '';
                    $dataPrebid[$x]['Parent Question Id'] = '';
                    $dataPrebid[$x]['Publish as'] = '';
                    $dataPrebid[$x]['Created At'] = '';
                    $dataPrebid[$x]['Is Thread Closed'] = '';
                }

                $fileNameFaq = 'faq';
                $fileNamePreBid = 'pre-bid_clarifications';
                $path = 'srm/faq/report/excel/';
                CreateExcel::process($data, $type, $fileNameFaq, $path);

                $prebidConfig['origin'] = 'SRM';
                $prebidConfig['faq_data'] = $data;
                $prebidConfig['prebid'] = 'PREBID';
                $prebidConfig['prebid_data'] = $dataPrebid;
                $prebidConfig['parentIdList'] = $parentIdArr;
                $prebidConfig['nonParentIdList'] = $nonParentIdArr;
                $basePath = CreateExcel::process($dataPrebid, $type, $fileNamePreBid, $path, $prebidConfig);

                if($basePath == '')
                {
                    return ['success' => false, 'data' => '', 'message' => 'Unable to export excel'];
                } else {
                    return [
                        'success' => true,
                        'message' => 'Successfully retrieved',
                        'data' =>  $basePath
                    ];
                }
            default:
                return ['success' => false, 'data' => '', 'message' => 'No report ID found'];
        }
    }

    private function getPreBidClarificationsResponseForExcel($tenderId)
        {
        $array_value = array();
        $x = 0;
        $parentPreBid = TenderBidClarifications::where('tender_master_id', $tenderId)
            ->where('parent_id', '=', 0)
            ->get();

        foreach ($parentPreBid as $parent) {
            $prebidResponse = TenderBidClarifications::with(['supplier', 'employee'])
                ->where('id', '=', $parent->id)
                ->orWhere('parent_id', '=', $parent->id)
                ->orderBy('parent_id', 'asc')
                ->get()
                ->toArray();

            $array_value[] = $prebidResponse;
            $x++;
        }

        return $array_value;
    }

    public function addInvoiceAttachment(Request $request) {
        $attachment = $request->input('extra.attachment');
        $companySystemID = $request->input('extra.slotCompanyId');
        $invoiceID = $request->input('extra.invoiceID');
        $description = $request->input('extra.description');
        $company = Company::where('companySystemID', $companySystemID)->first();
        $documentCode = DocumentMaster::where('documentSystemID', 11)->first();

        try {
            if (!empty($attachment) && isset($attachment['file'])) {
                $extension = $attachment['fileType'];
                $allowExtensions = ['png', 'jpg', 'jpeg', 'pdf', 'txt', 'xlsx', 'docx'];

                if (!in_array(strtolower($extension), $allowExtensions)) {
                    return [
                        'success' => false,
                        'message' => 'This file type is not allowed to upload.',
                        'data' => 'This file type is not allowed to upload.'
                    ];
                }

                if (isset($attachment['size'])) {
                    if ($attachment['size'] > 2097152) {
                        return $this->sendError("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.", 500);
                    }
                }
                $file = $attachment['file'];
                $decodeFile = base64_decode($file);
                $attachmentNameWithExtension = time() . '_Supplier_Invoice.' . $extension;
                $path = $company->CompanyID . '/SI/' . $invoiceID . '/' . $attachmentNameWithExtension;
                Storage::disk('s3')->put($path, $decodeFile);

                $att['companySystemID'] = $companySystemID;
                $att['companyID'] = $company->CompanyID;
                $att['documentSystemID'] = $documentCode->documentSystemID;
                $att['documentID'] = $documentCode->documentID;
                $att['documentSystemCode'] = $invoiceID;
                $att['attachmentDescription'] = $description;
                $att['path'] = $path;
                $att['originalFileName'] = $attachment['originalFileName'];
                $att['myFileName'] = $company->CompanyID . '_' . time() . '_Supplier_Invoice.' . $extension;
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
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e,
                'data' => ''
            ];
        }
    }

    public function getInvoiceAttachment($request)
    {
        $id = $request->input('extra.id');

       $query = DocumentAttachments::where('documentSystemID', 11)
            ->where('documentSystemCode', $id)
            ->where('attachmentType', 0);
        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
            $query->where('originalFileName', 'LIKE', "%{$search}%")
                 ->orWhere('attachmentDescription', 'LIKE', "%{$search}%");
            });
        }

            $data = DataTables::of($query)
                ->addColumn('Actions', 'Actions', "Actions")
                ->order(function ($query){
                })
                ->addIndexColumn()
                ->make(true);

            return [
                'success' => true,
                'message' => 'Invoice attachment successfully get',
                'data' => $data
            ];


    }

    public function removeInvoiceAttachment($request)
    {
        $attachmentID = $request->input('extra.attachmentID');

        $data = DocumentAttachments::where('attachmentID', $attachmentID)
            ->delete();

        return [
            'success' => true,
            'message' => 'Attachment deleted successfully ',
            'data' => $data
        ];
    }

    public function createInvoice(Request $request)
    {

        $data['id'] = $request->input('extra.id');
        $data['companySystemID'] = $request->input('extra.companySystemID');

        $acc_d = DeliveryAppointmentInvoice::dispatch($data);
        return [
            'success' => true,
            'message' => 'Invoice created successfully ',
            'data' => $data
        ];

    }

    public function convertArrayToSelectedValue ($input,$params){
        foreach ($input as $key => $value) {
            if(in_array($key,$params)){
                if (is_array($input[$key])){
                    if(count($input[$key]) > 0){
                        $input[$key] = $input[$key][0];
                    }
                }
            }
        }
        return $input;
    }

    public function getPaymentVouchers(Request $request) {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month'.'companyID', 'year', 'cancelYN', 'confirmedYN', 'approved', 'invoiceType', 'supplierID', 'chequePaymentYN', 'BPVbank', 'BPVAccount', 'chequeSentToTreasury', 'payment_mode', 'projectID','payeeTypeID'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $supplierID = self::getSupplierIdByUUID($request->input('supplier_uuid'));

        $employeeID = $request['employeeID'];
        $employeeID = (array)$employeeID;
        $employeeID = collect($employeeID)->pluck('id');

        $projectID = $request['projectID'];
        $projectID = (array)$projectID;
        $projectID = collect($projectID)->pluck('id');

        $search = $request->input('search.value');


        $paymentVoucher = PaySupplierInvoiceMaster::where('BPVsupplierID', $supplierID)->whereIn('invoiceType',[2,3,5])->where('approved',-1)->with(['supplier', 'created_by', 'suppliercurrency', 'bankcurrency', 'expense_claim_type', 'paymentmode', 'project']);



        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $search_without_comma = str_replace(",", "", $search);
            $paymentVoucher = $paymentVoucher->where(function ($query) use ($search, $search_without_comma) {
                $query->where('BPVcode', 'LIKE', "%{$search}%")
                    ->orWhere('BPVNarration', 'LIKE', "%{$search}%")->orWhere('suppAmountDocTotal', 'LIKE', "%{$search_without_comma}%")->orWhere('payAmountBank', 'LIKE', "%{$search_without_comma}%")->orWhere('BPVchequeNo', 'LIKE', "%{$search_without_comma}%")->orWhere('directPaymentPayee', 'LIKE', "%{$search_without_comma}%");
            });
        }

        $data = \DataTables::eloquent($paymentVoucher)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('PayMasterAutoId', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);


        return [
            'success' => true,
            'message' => 'Payment Vouchers successfully get',
            'data' => $data
        ];
    }

    public function getPaymentVouchersDetails(Request $request) {

        $input = $request->all();

        $output = PaySupplierInvoiceMaster::where('PayMasterAutoId',  $input['extra']['id'])
            ->with(['project','supplier', 'bankaccount', 'transactioncurrency', 'paymentmode',
                'supplierdetail' => function ($query) {
                    $query->with(['pomaster']);
                },
                'company', 'localcurrency', 'rptcurrency', 'advancedetail', 'confirmed_by',
                'modified_by', 'cheque_treasury_by', 'directdetail' => function ($query) {
                    $query->with('project','segment');
                }, 'approved_by' => function ($query) {
                    $query->with('employee');
                    $query->where('documentSystemID', 4);
                }, 'created_by', 'cancelled_by', 'bankledgers' => function ($query) {
                    $query->where('documentSystemID', 4);
                    $query->with(['bankrec_by']);
                },
                'bankledger_by' => function ($query) {
                    $query->where('documentSystemID', 4);
                    $query->with(['bankrec_by', 'bank_transfer']);
                },'audit_trial.modified_by'])->first();


        $isProjectBase = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
        ->where('companySystemID', $output->companySystemID)
        ->where('isYesNO', 1)
        ->exists();
        $output['isProjectBase'] = $isProjectBase;

        return [
            'success' => true,
            'message' => 'Payment Vouchers successfully get',
            'data' => $output
        ];

    }


    public function checkGrvCreation(Request $request)
    {

        $id = $request->input('extra.id');
        $data['exit'] = false;

        $is_grv_exit = Appointment::where('id',$id)->where('grv_create_yn',0)->first();
        if(isset($is_grv_exit))
        {
            $data['exit'] = true;

        }
        return [
            'success' => true,
            'message' => 'Invoice created successfully ',
            'data' => $data
        ];

    }

    private function crateNewNegotiationTender($tender_id, $tender_negotiation_data, $bidMasterId, $supplierRegId, $att){
        $tenderNegotiationArea = TenderNegotiation::select('id')->with('area')->where('srm_tender_master_id', $tender_id)->first();
        $pricingSchedule = $tenderNegotiationArea->area->pricing_schedule;
        $technicalEvaluation = $tenderNegotiationArea->area->technical_evaluation;
        $tenderDocuments = $tenderNegotiationArea->area->tender_documents;

        $envelopeType = [];
        $envelopeType[] = $pricingSchedule ? 1 : null;
        $envelopeType[] = $technicalEvaluation ? 2 : null;
        $envelopeType[] = $tenderDocuments ? 3 : null;

        $envelopeType = array_filter($envelopeType);


        $pricingSchedule = $tenderNegotiationArea->area->pricing_schedule;
        $technicalEvaluation = $tenderNegotiationArea->area->technical_evaluation;
        $tenderDocuments = $tenderNegotiationArea->area->tender_documents;
        $data['tender_id'] = $tender_id;
        $data['tender_negotiation_id'] = $tender_negotiation_data[0]['supplier_tender_negotiation']['tender_negotiation_id'];
        $data['bid_submission_master_id_old'] = $tender_negotiation_data[0]['supplier_tender_negotiation']['srm_bid_submission_master_id'];
        $data['bid_submission_master_id_new'] = $bidMasterId;
        $data['bid_submission_code_old'] = $tender_negotiation_data[0]['supplier_tender_negotiation']['bidSubmissionCode'];
        $data['supplier_id'] = $supplierRegId;
        $att['created_at'] = Carbon::now();
        TenderBidNegotiation::create($data);

        if ($tender_negotiation_data[0]['supplier_tender_negotiation']['srm_bid_submission_master_id']) {
            $newBidMasterId = $bidMasterId;

            $goNoGoBidSubmissionData = $this->getGoNoGoBidSubmissionData(null,true, [$tender_id, $tender_negotiation_data[0]['supplier_tender_negotiation']['srm_bid_submission_master_id']]);
            $criteriaDetailArr = array();
            foreach($goNoGoBidSubmissionData['data']['criteriaDetail'] as $criteriaDetail){
                $criteriaDetailArr[] = $criteriaDetail['id'];
            }

            $bidSubmissionDetails = BidSubmissionDetail::select('evaluation_detail_id', 'score_id', 'score', 'result', 'go_no_go_criteria_result', 'eval_score', 'eval_result', 'evaluate_by', 'evaluate_at', 'bid_selection_id', 'eval_score_id')
                ->where('bid_master_id', $tender_negotiation_data[0]['supplier_tender_negotiation']['srm_bid_submission_master_id']);
            if ($technicalEvaluation){
                $bidSubmissionDetails = $bidSubmissionDetails->whereIn('evaluation_detail_id', $criteriaDetailArr);
            }
            $bidSubmissionDetails = $bidSubmissionDetails->get()->toArray();

            foreach ($bidSubmissionDetails as $bidSubmissionDetail){
                $newBidSubmissionDetail = new BidSubmissionDetail;
                $newBidSubmissionDetail->bid_master_id = $newBidMasterId;
                $newBidSubmissionDetail->tender_id = $tender_id;
                $newBidSubmissionDetail->evaluation_detail_id = $bidSubmissionDetail['evaluation_detail_id'];
                $newBidSubmissionDetail->score_id = $bidSubmissionDetail['score_id'];
                $newBidSubmissionDetail->score =  $bidSubmissionDetail['score'] ;
                $newBidSubmissionDetail->result = $bidSubmissionDetail['result'];
                $newBidSubmissionDetail->created_at = Carbon::now();
                $newBidSubmissionDetail->created_by = $supplierRegId;
                $newBidSubmissionDetail->go_no_go_criteria_result = $bidSubmissionDetail['go_no_go_criteria_result'];
                $newBidSubmissionDetail->eval_score = $bidSubmissionDetail['eval_score'];
                $newBidSubmissionDetail->eval_result = $bidSubmissionDetail['eval_result'];
                $newBidSubmissionDetail->evaluate_by = $bidSubmissionDetail['evaluate_by'];
                $newBidSubmissionDetail->evaluate_at = $bidSubmissionDetail['evaluate_at'];
                $newBidSubmissionDetail->bid_selection_id = $bidSubmissionDetail['bid_selection_id'];
                $newBidSubmissionDetail->eval_score_id = $bidSubmissionDetail['eval_score_id'];
                $newBidSubmissionDetail->save();
            }
        }

        $docAttachments = DocumentAttachments::where('documentSystemCode', $tender_negotiation_data[0]['supplier_tender_negotiation']['srm_bid_submission_master_id']);

        $docAttachments = $docAttachments->get()->whereNotIn('envelopType',$envelopeType);

        if(count($docAttachments) > 0){
            foreach ($docAttachments as $docAttachment){
                        $doc = new DocumentAttachments();
                        $doc->companySystemID = $docAttachment['companySystemID'];
                        $doc->companyID = $docAttachment['companyID'];
                        $doc->documentSystemID = $docAttachment['documentSystemID'];
                        $doc->documentID = $docAttachment['documentID'];
                        $doc->documentSystemCode = $newBidMasterId;
                        $doc->approvalLevelOrder = $docAttachment['approvalLevelOrder'];
                        $doc->attachmentDescription = $docAttachment['attachmentDescription'];
                        $doc->location = $docAttachment['location'];
                        $doc->path = $docAttachment['path'];
                        $doc->originalFileName = $docAttachment['originalFileName'];
                        $doc->myFileName = $docAttachment['myFileName'];
                        $doc->docExpirtyDate = $docAttachment['docExpirtyDate'];
                        $doc->attachmentType = $docAttachment['attachmentType'];
                        $doc->sizeInKbs = $docAttachment['sizeInKbs'];
                        $doc->isUploaded = $docAttachment['isUploaded'];
                        $doc->pullFromAnotherDocument = $docAttachment['pullFromAnotherDocument'];
                        $doc->parent_id = $docAttachment['parent_id'];
                        $doc->timeStamp = Carbon::now();
                        $doc->envelopType = $docAttachment['envelopType'];
                        $doc->order_number = $docAttachment['order_number'];
                        $doc->save();
            }
        }

        $bidMainWorks = BidMainWork::where('bid_master_id', $tender_negotiation_data[0]['supplier_tender_negotiation']['srm_bid_submission_master_id'])->get();

        if(count($bidMainWorks) > 0 && !$pricingSchedule){
            foreach ($bidMainWorks as $bidMainWork){
                $bid = new BidMainWork();
                $bid->main_works_id = $bidMainWork['main_works_id'];
                $bid->bid_master_id = $newBidMasterId;
                $bid->tender_id = $bidMainWork['tender_id'];
                $bid->bid_format_detail_id = $bidMainWork['bid_format_detail_id'];
                $bid->qty = $bidMainWork['qty'];
                $bid->amount = $bidMainWork['amount'];
                $bid->total_amount = $bidMainWork['total_amount'];
                $bid->remarks = $bidMainWork['remarks'];
                $bid->supplier_registration_id = $bidMainWork['supplier_registration_id'];
                $bid->created_at = Carbon::now();
                $bid->created_by = $supplierRegId;
                $bid->save();
            }
        }

        $bidSchedules = BidSchedule::where('bid_master_id', $tender_negotiation_data[0]['supplier_tender_negotiation']['srm_bid_submission_master_id'])->get();

        if(count($bidSchedules) > 0){
            foreach ($bidSchedules as $bidSchedule){
                $bid = new BidSchedule();
                $bid->schedule_id = $bidSchedule['schedule_id'];
                $bid->bid_master_id = $newBidMasterId;
                $bid->tender_id = $bidSchedule['tender_id'];
                $bid->supplier_registration_id = $bidSchedule['supplier_registration_id'];
                if(!$pricingSchedule){
                    $bid->remarks = $bidSchedule['remarks'];
                }
                $bid->created_at = Carbon::now();
                $bid->created_by = $supplierRegId;
                $bid->save();
            }
        }

        $bidBoqs = BidBoq::where('bid_master_id', $tender_negotiation_data[0]['supplier_tender_negotiation']['srm_bid_submission_master_id'])->get();

        if(count($bidBoqs) > 0 && !$pricingSchedule){
            foreach ($bidBoqs as $bidBoq){
                $bidBoqRecord = new BidBoq();
                $bidBoqRecord->boq_id = $bidBoq['boq_id'];
                $bidBoqRecord->bid_master_id = $newBidMasterId;
                $bidBoqRecord->main_works_id = $bidBoq['main_works_id'];
                $bidBoqRecord->qty = $bidBoq['qty'];
                $bidBoqRecord->unit_amount = $bidBoq['unit_amount'];
                $bidBoqRecord->total_amount = $bidBoq['total_amount'];
                $bidBoqRecord->remarks = $bidBoq['remarks'];
                $bidBoqRecord->supplier_registration_id = $bidBoq['supplier_registration_id'];
                $bidBoqRecord->created_at = Carbon::now();
                $bidBoqRecord->created_by = $supplierRegId;
                $bidBoqRecord->save();
            }
        }
    }

    private function getTenderNegotiationArea($tenderId)
    {
        $tenderNegotiationResults = TenderNegotiation::select('id')->with('area')->where('srm_tender_master_id', $tenderId)->first();

        return ($tenderNegotiationResults) ? $tenderNegotiationResults->area : null;
    }

    public static function getNegotiationBids($tenderId){ 
        return TenderBidNegotiation::where('tender_id', $tenderId)
        ->pluck('bid_submission_master_id_new')
        ->toArray();
    }

    public function getSupplierRegistrationData(Request $request) {
         $companyId = $request['companyId'];

         $supRegData = SupplierRegistrationLink::select('id','email','uuid')
              ->where('company_id',$companyId)
              ->where('STATUS',1)
              ->get();

        return [
            'success' => true,
            'message' => 'ERP Form Data Retrieved',
            'data' => $supRegData
        ];
    }

    public function validateSupplierEmail($request)
    {
        $companyId = $request->input('extra.data.company_id');
        $userEmail = $request->input('extra.data.supplierUserEmail');
        $supplierUuid = $request->input('supplier_uuid');
        $request->merge([
            'companyId' => $companyId
        ]);

        $supRegData = $this->getSupplierRegistrationData($request);
        $filteredData = $supRegData['data']->filter(function ($item) use ($supplierUuid) {
            return $item->uuid != $supplierUuid;
        });

        $emails = $filteredData->pluck('email')->toArray();
        if (in_array($userEmail, $emails)) {
            return ['status' => false, 'message' => 'Email already exists'];
        }
        return ['status' => true, 'message' => 'Success'];
    }
    public function getCurrentServerDateTime()
    {
        $currentdate = Carbon::now();

        return [
            'success' => true,
            'message' => 'Current Server DateTime Retrieved',
            'data' => $currentdate
        ];
    }
}
