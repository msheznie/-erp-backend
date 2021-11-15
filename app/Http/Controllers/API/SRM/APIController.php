<?php

namespace App\Http\Controllers\API\SRM;

use App\Http\Controllers\Controller;
use App\Services\POService;
use App\Services\SRMService;
use Illuminate\Http\Request;

// available apis name define here
define('GET_CURRENCIES', 'GET_CURRENCIES');
define('GET_PURCHASE_ORDERS', 'GET_PURCHASE_ORDERS');
define('GET_PURCHASE_ORDER_PRINT', 'GET_PURCHASE_ORDER_PRINT');
define('GET_PURCHASE_ORDER_ADDONS', 'GET_PURCHASE_ORDER_ADDONS');
define('GET_APPOINTMENT_SLOTS', 'GET_APPOINTMENT_SLOTS');
define('GET_PO', 'GET_PO');
define('SAVE_PO_APPOINTMENT', 'SAVE_PO_APPOINTMENT');

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
                    
            default:
                return [
                    'success'   => false,
                    'message'   => 'Requested API not available, please recheck!',
                    'data'      => null
                ];
        }
    }
}
