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
use Illuminate\Support\Arr;
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
define('CHECK_APPOINTMENT_PAST', 'CHECK_APPOINTMENT_PAST');
define('GET_APPOINTMENT_DETAILS', 'GET_APPOINTMENT_DETAILS');
define('GET_PO_DETAILS', 'GET_PO_DETAILS');
define('GET_ALL_APPOINTMENT_DELIVERIES', 'GET_ALL_APPOINTMENT_DELIVERIES');
define('GET_WAREHOUSE', 'GET_WAREHOUSE');
define('GET_REMAINING_SLOT_COUNT', 'GET_REMAINING_SLOT_COUNT');
define('CANCEL_APPOINTMENTS', 'CANCEL_APPOINTMENTS');
define('GET_APPROVED_DETAILS', 'GET_APPROVED_DETAILS');
define('GET_TENDERS', 'GET_TENDERS');
define('SAVE_TENDER_PURCHASE', 'SAVE_TENDER_PURCHASE');
define('GET_FAQ', 'GET_FAQ');
define('GET_TENDER_PRE_BID_CLARIFICATION_LIST', 'GET_TENDER_PRE_BID_CLARIFICATION_LIST');
define('ADD_CLARIFICATION', 'ADD_CLARIFICATION');
define('GET_PRE_BID_CLARIFICATION_RESPONSE', 'GET_PRE_BID_CLARIFICATION_RESPONSE');
define('ADD_PRE_BID_CLARIFICATION_RESPONSE', 'ADD_PRE_BID_CLARIFICATION_RESPONSE');
define('GET_TENDER_PRE_BID_CLARIFICATION', 'GET_TENDER_PRE_BID_CLARIFICATION');
define('ADD_APPOINTMENT_ATTACHMENT', 'ADD_APPOINTMENT_ATTACHMENT');
define('GET_APPOINTMENT_ATTACHMENT', 'GET_APPOINTMENT_ATTACHMENT');
define('REMOVE_APPOINTMENT_ATTACHMENT', 'REMOVE_APPOINTMENT_ATTACHMENT');
define('REMOVE_CLARIFICATION_ATTACHMENT', 'REMOVE_CLARIFICATION_ATTACHMENT');
define('REMOVE_PRE_BID_CLARIFICATION_RESPONSE', 'REMOVE_PRE_BID_CLARIFICATION_RESPONSE');
define('GET_CONSOLIDATED_DATA', 'GET_CONSOLIDATED_DATA');
define('GET_CONSOLIDATED_DATA_ATTACHMENT', 'GET_CONSOLIDATED_DATA_ATTACHMENT');
define('GET_GO_NO_GO_BID_SUBMISSION', 'GET_GO_NO_GO_BID_SUBMISSION');
define('CHECK_BID_SUBMITTED', 'CHECK_BID_SUBMITTED');
define('SAVE_TECHNICAL_BID_SUBMISSION', 'SAVE_TECHNICAL_BID_SUBMISSION');
define('SAVE_TECHNICAL_BID_LINE', 'SAVE_TECHNICAL_BID_LINE');
define('SAVE_GO_NO_GO_BID_LINE', 'SAVE_GO_NO_GO_BID_LINE');
define('GET_TENDER_ATTACHMENT', 'GET_TENDER_ATTACHMENT');
define('RE_UPLOAD_TENDER_ATTACHMENT', 'RE_UPLOAD_TENDER_ATTACHMENT');

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
            case CHECK_APPOINTMENT_PAST:
                return $this->SRMService->checkAppointmentPastDate($request);
            case GET_APPOINTMENT_DETAILS:
                return $this->SRMService->getAppointmentDetails($request);
            case GET_PO_DETAILS:
                return $this->SRMService->getPurchaseOrderDetails($request);
            case GET_ALL_APPOINTMENT_DELIVERIES:
                return $this->SRMService->getAllAppointmentList($request);
            case GET_WAREHOUSE:
                return $this->SRMService->getWarehouse($request);
            case GET_REMAINING_SLOT_COUNT:
                return $this->SRMService->getRemainingSlotCount($request);
            case CANCEL_APPOINTMENTS:
                return $this->SRMService->cancelAppointments($request);
            case GET_APPROVED_DETAILS:
                return $this->SRMService->getSrmApprovedDetails($request);
            case GET_TENDERS:
                return $this->SRMService->getTenders($request);
            case SAVE_TENDER_PURCHASE:
                return $this->SRMService->saveTenderPurchase($request);
            case GET_FAQ:
                return $this->SRMService->getFaqList($request);
            case GET_TENDER_PRE_BID_CLARIFICATION_LIST:
                return $this->SRMService->getPrebidClarificationList($request);
            case ADD_CLARIFICATION:
                return $this->SRMService->saveTenderPrebidClarification($request);
            case GET_PRE_BID_CLARIFICATION_RESPONSE:
                return $this->SRMService->getPreBidClarificationsResponse($request);
            case ADD_PRE_BID_CLARIFICATION_RESPONSE:
                return $this->SRMService->createClarificationResponse($request);
            case GET_TENDER_PRE_BID_CLARIFICATION:
                return $this->SRMService->getPrebidClarification($request);
            case ADD_APPOINTMENT_ATTACHMENT:
                return $this->SRMService->uploadAppointmentAttachment($request);
            case GET_APPOINTMENT_ATTACHMENT:
                return $this->SRMService->getDeliveryAppointmentAttachment($request);
            case REMOVE_APPOINTMENT_ATTACHMENT:
                return $this->SRMService->removeDeliveryAppointmentAttachment($request);
            case REMOVE_CLARIFICATION_ATTACHMENT:
                return $this->SRMService->removeDeliveryAppointmentAttachment($request);
            case REMOVE_PRE_BID_CLARIFICATION_RESPONSE:
                return $this->SRMService->removePreBidClarificationResponse($request);
            case GET_CONSOLIDATED_DATA:
                return $this->SRMService->getConsolidatedData($request);
            case GET_CONSOLIDATED_DATA_ATTACHMENT:
                return $this->SRMService->getConsolidatedDataAttachment($request);
            case GET_GO_NO_GO_BID_SUBMISSION :
                return $this->SRMService->getGoNoGoBidSubmissionData($request);
            case CHECK_BID_SUBMITTED :
                return $this->SRMService->checkBidSubmitted($request);
            case SAVE_TECHNICAL_BID_SUBMISSION :
                return $this->SRMService->saveTechnicalBidSubmission($request);
            case SAVE_TECHNICAL_BID_LINE :
                return $this->SRMService->saveTechnicalBidSubmissionLine($request);
            case SAVE_GO_NO_GO_BID_LINE :
                return $this->SRMService->saveGoNoGoBidSubmissionLine($request);
            case GET_TENDER_ATTACHMENT :
                return $this->SRMService->getTenderAttachment($request);
            case RE_UPLOAD_TENDER_ATTACHMENT :
                return $this->SRMService->reUploadTenderAttachment($request);
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
                if ($response->data) {
                    foreach ($response->data  as $key1 => $data1) {
                        foreach ($data1->groups as $val2) {
                            foreach ($val2->controls as $val3) {
                                foreach ($val3->field->values as $key1 => $val4) {
                                    if ($val3->form_field_id == 1) { //Category 
                                        $category = SupplierCategoryMaster::select('categoryDescription', 'categoryCode')->where('supCategoryMasterID', $val4->value)->first();
                                        $val4->value = $category['categoryDescription'];
                                    } else if ($val3->form_field_id == 2) { // Sub Category 
                                        $subCategory = SupplierCategorySub::select('categoryDescription', 'subCategoryCode')->where('supCategorySubID', $val4->value)->first();
                                        $val4->value = $subCategory['categoryDescription'];
                                    } else if ($val3->form_field_id == 28) { // Preferred Functional Currency
                                        $currency = CurrencyMaster::select('CurrencyCode', 'CurrencyName')->where('currencyID', $val4->value)->first();
                                        $val4->value = $currency['CurrencyName'] . ' (' . $currency['CurrencyCode'] . ')';
                                    } else if ($val3->form_field_id == 46) { // Country
                                        $countryMaster = CountryMaster::select('countryName', 'countryCode')->where('countryID', $val4->value)->first();
                                        $val4->value = $countryMaster['countryName'];
                                    } else if ($val4->form_data_id > 0) {
                                        //$value = array_search('26', array_column($val3->field->options, 'id')); 
                                        $search = $val4->form_data_id;
                                        $data = array_filter($val3->field->options, function ($v) use ($search) {
                                            return $v->id == $search;
                                        }, ARRAY_FILTER_USE_BOTH);
                                        $value = array_values($data);
                                        $val4->value = $value[0]->option->text;
                                    }
                                }
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
