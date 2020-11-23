<?php

namespace App\helper;
use Carbon\Carbon;
use App\Models\CustomerMaster;
use App\Models\Company;
use App\helper\IvmsService;

class IvmsDeliveryOrderService
{
	public static function postIvmsDeliveryOrder($deliveryOrder)
    {
        $customerData = CustomerMaster::with(['customer_default_contacts' => function($query) {
                                                $query->where('isDefault', -1);
                                        }])->where('customerCodeSystem', $deliveryOrder->customerID)
                                        ->first();

        $customerPhone = (!is_null($customerData->customer_default_contacts)) ? $customerData->customer_default_contacts->contactPersonTelephone : "";
        $contactPersonEmail = (!is_null($customerData->customer_default_contacts)) ? $customerData->customer_default_contacts->contactPersonEmail : "";
        

        $deliveryOrderDate  = Carbon::parse($deliveryOrder->deliveryOrderDate)->format('Y-m-d');

        $deliveryFrom = strtotime($deliveryOrderDate.' 00:00');
        $deliveryTo = strtotime($deliveryOrderDate.' 23:59');

        $orderName = addslashes($deliveryOrder->deliveryOrderCode);

        $orderParams = '{
                            "uid": 0,
                            "id": 0,
                            "n": "'.$orderName.'",
                            "p": {
                                "n": "'.$customerData->CustomerName.'",
                                "p": "'.$customerPhone.'",
                                "p2": "",
                                "e": "'.$contactPersonEmail.'",
                                "a": "'.$customerData->customerAddress1.'",
                                "v": 0,
                                "w": 0,
                                "c": '.$deliveryOrder->transactionAmount.',
                                "d": "'.$deliveryOrder->narration.'",
                                "ut": 0,
                                "t": "",
                                "r": null,
                                "cid": "",
                                "uic": "",
                                "ntf": 0,
                                "pr": 0,
                                "tags": []
                            },
                            "f": 1,
                            "tf": '.$deliveryFrom.',
                            "tt": '.$deliveryTo.',
                            "r": 0,
                            "y": 0,
                            "x": 0,
                            "u": 0,
                            "trt": 0,
                            "itemId": 55,
                            "callMode": "create"
                        }';

        $res = IvmsService::postDeliveryOrder($orderParams);

        if (!$res['status']) {
        	return ['status' => false, 'message' => "Error occured while posting delivery order in IVMS"];
        }

        return ['status' => true, 'message' => "SUCCESS"];
    }
}