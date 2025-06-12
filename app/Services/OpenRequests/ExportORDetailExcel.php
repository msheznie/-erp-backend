<?php

namespace App\Services\OpenRequests;

use App\helper\CreateExcel;
use App\helper\Helper;
use App\Models\Company;
use App\Models\ProcumentOrder;
use App\Services\WebPushNotificationService;
use Illuminate\Support\Facades\Log;

class ExportORDetailExcel {
    private $userId;
    private $data;
    private $code;
    public function __construct($request,$code) {
        $this->data = $request;
        $this->code = $code;
        $this->setData();
    }

    private function setData() {
        $this->userId = Helper::getEmployeeSystemID();;
    }

    public function export() {
        $basePath = CreateExcel::processOpenRequestReport($this->data,$this->code);
        Log::info('Export completed', ['result' => $basePath]);
        $this->sendNotification($basePath);

        if($basePath == '') {
            return ['success' => false , 'message' => 'Unable to export excel'];
        }

        return ['success' => true , 'message' =>  trans('custom.success_export')];
    }




    private function sendNotification($basePath) {
        $webPushData = [
            'title' => "Open Request Detailed Excel has been generated",
            'body' => '',
            'url' => "",
            'path' => $basePath,
        ];
       return WebPushNotificationService::sendNotification($webPushData, 3, [$this->userId]);
    }
}
