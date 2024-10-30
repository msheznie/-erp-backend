<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Http\Controllers\API\AccountsReceivableReportAPIController;
use App\Http\Controllers\AppBaseController;
use App\Models\CustomerContactDetails;
use App\Models\CustomerMaster;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SentCustomerLedgerSubJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $input;
    public $db;
    public $receivableController;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $input)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->input = $input;
        $this->db = $db;
        $this->receivableController = app(AccountsReceivableReportAPIController::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->db;
        CommonJobService::db_switch($db);
        Log::info('customer ledger sub job started');

        $input = $this->input;
        $customerCodeSystem = $input['customers'][0]['customerCodeSystem'];

        $customerMaster = CustomerMaster::find($customerCodeSystem);
        $fetchCusEmail = CustomerContactDetails::where('customerID', $customerCodeSystem)->get();
        if (!$fetchCusEmail->isEmpty()) {
            $reportTypeID = $input['reportTypeID'];
            $baseController = app()->make(AppBaseController::class);
            $databaseName = $this->db ?? 'local';
            $path = public_path() . '/uploads/emailAttachment/customer_ledger_' . $databaseName . '_' . $input['companySystemID'] . '_' . $customerCodeSystem;
            if (!file_exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            if ($reportTypeID == 'CLT1') { //customer ledger template 1
                $request = (object)$baseController->convertArrayToSelectedValue($input, array('currencyID'));
                $output = $this->receivableController->getCustomerLedgerTemplate1QRY($request);
                if($output) {
                    $reportCount = 1;
                    $outputChunkData = collect($output)->chunk(300);
                    foreach ($outputChunkData as $recordsChuncked) {
                        $dataArray = array(
                            'input' => $input,
                            'reportCount' => $reportCount,
                            'recordsChuncked' => $recordsChuncked,
                            'fileCount' => count($outputChunkData),
                            'path' => $path,
                            'customerCodeSystem' => $customerCodeSystem,
                            'fetchCusEmail' => $fetchCusEmail->toArray(),
                            'customerName' => $customerMaster->CustomerName
                        );
                        SentCustomerLedgerPdfGeneration::dispatch($db, $dataArray);
                        $reportCount++;
                    }
                } else {
                    Log::error('No details found');
                }
            } else {
                $request = (object)$baseController->convertArrayToSelectedValue($input, array('currencyID'));

                $output = $this->receivableController->getCustomerLedgerTemplate2QRY($request);
                if($output) {
                    $reportCount = 1;
                    $outputChunkData = collect($output)->chunk(300);
                    foreach ($outputChunkData as $recordsChuncked) {
                        $dataArray = array(
                            'input' => $input,
                            'reportCount' => $reportCount,
                            'recordsChuncked' => $recordsChuncked,
                            'fileCount' => count($outputChunkData),
                            'path' => $path,
                            'customerCodeSystem' => $customerCodeSystem,
                            'fetchCusEmail' => $fetchCusEmail->toArray(),
                            'customerName' => $customerMaster->CustomerName
                        );
                        SentCustomerLedgerPdfGeneration::dispatch($db, $dataArray);
                        $reportCount++;
                    }
                } else {
                    Log::error('No details found');
                }
            }
        } else {
            Log::error("Customer email is not updated for " . $customerMaster->CustomerName . ". report is not sent");
        }
    }
}
