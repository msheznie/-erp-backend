<?php

namespace App\Http\Controllers\API\SRM;

use App\Http\Controllers\Controller;
use App\Services\POService;
use App\Services\SRMService;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

// available apis name define here
define('GET_CURRENCIES', 'GET_CURRENCIES');
define('GET_PURCHASE_ORDERS', 'GET_PURCHASE_ORDERS');
define('GET_PURCHASE_ORDER_PRINT', 'GET_PURCHASE_ORDER_PRINT');
define('GET_PURCHASE_ORDER_ADDONS', 'GET_PURCHASE_ORDER_ADDONS');
define('GET_APPOINTMENT_SLOTS', 'GET_APPOINTMENT_SLOTS');
define('GET_PO', 'GET_PO');
define('SAVE_PO_APPOINTMENT', 'SAVE_PO_APPOINTMENT');
define('GET_SUPPLIER_INVITATION_INFO', 'GET_SUPPLIER_INVITATION_INFO');
define('UPDATE_SUPPLIER_INVITATION_STATUS', 'UPDATE_SUPPLIER_INVITATION_STATUS');
define('GET_PURCHASE_ORDER_APPOINTMENTS', 'GET_PURCHASE_ORDER_APPOINTMENTS');
define('GET_APPOINTMENT_DELIVERIES', 'GET_APPOINTMENT_DELIVERIES');
define('GET_PO_APPOINTMENT', 'GET_PO_APPOINTMENT');
define('DELETE_SUPPLIER_APPOINTMENT', 'DELETE_SUPPLIER_APPOINTMENT');
define('CONFIRM_SUPPLIER_APPOINTMENT', 'CONFIRM_SUPPLIER_APPOINTMENT');
define('SUPPLIER_REGISTRATION_APPROVAL_SETUP', 'SUPPLIER_REGISTRATION_APPROVAL_SETUP');


class APIController extends Controller
{

    private $SRMService = null;

    public function __construct(SRMService $SRMService)
    {
        $this->SRMService = $SRMService;
    }

    /**
     * handle api request
     * @param Request $request
     * @return array
     * @throws Throwable
     */
    public function handleRequest(Request $request): array
    {
        switch ($request->input('request')) {
            case GET_CURRENCIES:
                return $this->SRMService->getCurrencies();
            case GET_PURCHASE_ORDERS:
                return $this->SRMService->getPoList($request);
            case GET_PURCHASE_ORDER_PRINT:
                return $this->SRMService->getPoPrintData($request);
            case GET_PURCHASE_ORDER_ADDONS:
                return $this->SRMService->getPoAddons($request);
            case GET_APPOINTMENT_SLOTS:
                return $this->SRMService->getAppointmentSlots($request);
            case GET_PO:
                return $this->SRMService->getPurchaseOrders($request);
            case SAVE_PO_APPOINTMENT:
                return $this->SRMService->SavePurchaseOrderList($request);
            case GET_SUPPLIER_INVITATION_INFO:
                return $this->SRMService->getSupplierInvitationInfo($request);
            case UPDATE_SUPPLIER_INVITATION_STATUS:
                return $this->SRMService->updateSupplierInvitation($request);
            case GET_PURCHASE_ORDER_APPOINTMENTS:
                return $this->SRMService->getAppointmentSlots($request);
            case GET_APPOINTMENT_DELIVERIES:
                return $this->SRMService->getAppointmentDeliveries($request);
            case GET_PO_APPOINTMENT:
                return $this->SRMService->getPoAppointments($request);
            case DELETE_SUPPLIER_APPOINTMENT:
                return $this->SRMService->deleteSupplierAppointment($request);
            case CONFIRM_SUPPLIER_APPOINTMENT:
                return $this->SRMService->confirmSupplierAppointment($request);
            case SUPPLIER_REGISTRATION_APPROVAL_SETUP:
                return $this->SRMService->supplierRegistrationApprovalSetup($request);
            default:
                return [
                    'success'   => false,
                    'message'   => 'Requested API not available, please recheck!',
                    'data'      => null
                ];
        }
    }

    /**
     * fetch SRM services
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function fetch(Request $request){
        try {
            $response = $this->SRMService->callSRMAPIs([
                'apiKey' => $request->input('api_key'),
                'request' => $request->input('request'),
                'extra' => [
                    'auth' => $request->user(),
                    'uuid' => $request->input('uuid')
                ]
            ]);

            throw_unless($response, "Invalid API Key or Something went wrong in SRM");
            throw_unless($response && $response->data, $response->message ?? "Something went wrong!, API couldn't fetch");

            return response()->json($response);
        } catch (RequestException $e) {
            $exception = (string) $e->getResponse()->getBody();
            $exception = json_decode($exception);

            \Log::info([
                'type' => 'ERP',
                'desc' => 'ERP API call Errors',
                'exception' => $exception
            ]);

            return response()->json([
                'success' => false,
                'message' => $exception->message,
                'data' => $exception->message
            ], 500);
        }
    }
}
