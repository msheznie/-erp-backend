<?php

namespace App\Http\Controllers\API\SRM;

use App\Http\Controllers\Controller;
use App\Models\CountryMaster;
use App\Models\CurrencyMaster;
use App\Models\SupplierCategoryMaster;
use App\Models\SupplierCategorySub;
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
define('GET_INVOICES', 'GET_INVOICES');
define('GET_INVOICE_DETAILS', 'GET_INVOICE_DETAILS');
define('SUPPLIER_REGISTRATION_APPROVAL_AMMEND', 'SUPPLIER_REGISTRATION_APPROVAL_AMMEND');
define('GET_ERP_FORM_DATA', 'GET_ERP_FORM_DATA');

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
            case GET_INVOICES:
                return $this->SRMService->getInvoicesList($request);
            case GET_INVOICE_DETAILS:
                return $this->SRMService->getInvoiceDetailsById($request);
            case SUPPLIER_REGISTRATION_APPROVAL_AMMEND:
                return $this->SRMService->supplierRegistrationApprovalAmmend($request);
            case GET_ERP_FORM_DATA:
                return $this->SRMService->getERPFormData($request);
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
    public function fetch(Request $request)
    {
        try {
            \Log::debug('=========$request==========');
            \Log::debug([$request->all()]);
            \Log::debug('=========$request==========');
            $response = $this->SRMService->callSRMAPIs([
                'apiKey' => $request->input('api_key'),
                'request' => $request->input('request'),
                'extra' => [
                    'auth' => $request->user(),
                    'uuid' => $request->input('uuid')
                ]
            ]);

            if ($request->input('request') == 'GET_SUPPLIER_DETAILS') {
                foreach ($response->data as $data1) {
                    foreach ($data1 as $data2) {
                        foreach ($data2 as $data3) {
                            $arrdata = [];
                            if ($data3->form_field_id == 1) { //Category 
                                $category = SupplierCategoryMaster::select('categoryDescription', 'categoryCode')->where('supCategoryMasterID', $data3->form_data_id)->first();
                                $arrdata = [
                                    'created_at' => $data3->created_at,
                                    'id' => $data3->id,
                                    'status' => $data3->status,
                                    'text' => $category->categoryDescription,
                                    'updated_at' => null,
                                    'value' => $category->categoryCode
                                ];
                                $data3->data[0] = $arrdata;
                            } else if ($data3->form_field_id == 2) { // Sub Category
                                $subCategory = SupplierCategorySub::select('categoryDescription', 'subCategoryCode')->where('supCategorySubID', $data3->form_data_id)->first();
                                $arrdata = [
                                    'created_at' => $data3->created_at,
                                    'id' => $data3->id,
                                    'status' =>  $data3->status,
                                    'text' =>   $subCategory->categoryDescription,
                                    'updated_at' => null,
                                    'value' => $subCategory->subCategoryCode
                                ];
                                $data3->data[0] = $arrdata;
                            } else if ($data3->form_field_id == 28) { // Preferred Functional Currency
                                $currency = CurrencyMaster::select('CurrencyCode')->where('currencyID', $data3->form_data_id)->first();
                                $arrdata = [
                                    'created_at' => $data3->created_at,
                                    'id' => $data3->id,
                                    'status' =>  $data3->status,
                                    'text' =>  $currency->CurrencyCode,
                                    'updated_at' => null,
                                    'value' => $currency->CurrencyCode
                                ];
                                $data3->data[0] = $arrdata;
                            } else if ($data3->form_field_id == 46) { // Country
                                $countryMaster = CountryMaster::select('countryName', 'countryCode')->where('countryID', $data3->form_data_id)->first();
                                $arrdata = [
                                    'created_at' => $data3->created_at,
                                    'id' => $data3->id,
                                    'status' =>  $data3->status,
                                    'text' =>  $countryMaster->countryName,
                                    'updated_at' => null,
                                    'value' => $countryMaster->countryCode
                                ];
                                $data3->data[0] = $arrdata;
                            }
                        }
                    }
                }
            }

            return response()->json($response);

            \Log::debug('==========$response=========');
            \Log::debug([$response]);
            \Log::debug('==========$response=========');

            throw_unless($response, "Invalid API Key or Something went wrong in SRM");
            throw_unless($response && $response->data, $response->message ?? "Something went wrong!, API couldn't fetch");

            return response()->json($response);
        } catch (RequestException $e) {
            \Log::debug('==========$e=========');
            \Log::debug([$e->getResponse()]);
            \Log::debug([$e->getTrace()]);
            \Log::debug('==========$e=========');
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
