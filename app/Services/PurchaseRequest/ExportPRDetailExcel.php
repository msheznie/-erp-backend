<?php

namespace App\Services\PurchaseRequest;
use App\helper\CreateExcel;
use App\Services\WebPushNotificationService;
use Illuminate\Support\Facades\Log;

class ExportPRDetailExcel
{
    private $userId;
    private $data;
    private $code;
    public function __construct($request,$userId,$code) {
        $this->data = $request;
        $this->userId = $userId;
        $this->code = $code;
    }

    public function export() {
        $basePath = CreateExcel::processPRDetailExport($this->data,$this->code);
        Log::info('Export completed', ['result' => $basePath]);
        $this->sendNotification($basePath);

        if($basePath == '') {
            return ['success' => false , 'message' => trans('custom.failed_export')];
        }

        return ['success' => true , 'message' =>  trans('custom.success_export')];
    }




    private function sendNotification($basePath) {
        $webPushData = [
            'title' => "purchase_request_detailed_excel_generated",
            'body' => '',
            'url' => "",
            'path' => $basePath,
        ];
       return WebPushNotificationService::sendNotification($webPushData, 3, [$this->userId]);
    }
}