<?php

namespace App\Http\Controllers\API\SRM;

use App\Http\Controllers\Controller;
use App\Services\SRMService;
use Illuminate\Http\Request;

// available apis name define here
define('GET_CURRENCIES', 'GET_CURRENCIES');

class APIController extends Controller
{

    private $SRMService = null;

    public function __construct(SRMService $SRMService) {
        $this->SRMService = $SRMService;
    }

    /**
     * handle api request
     * @param Request $request
     * @return array
     */
    public function handleRequest(Request $request): array {
        switch ($request->input('request')){
            case GET_CURRENCIES:
                return $this->SRMService->getCurrencies();
            default:
                return [
                    'success'   => false,
                    'message'   => 'Requested API not available, please recheck!',
                    'data'      => null
                ];
        }
    }
}
