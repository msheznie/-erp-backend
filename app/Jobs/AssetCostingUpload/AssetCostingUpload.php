<?php

namespace App\Jobs\AssetCostingUpload;

use App\helper\CommonJobService;
use App\Jobs\CustomerInvoiceUpload\CustomerInvoiceUploadSubJob;
use App\Models\AssetFinanceCategory;
use App\Models\DepartmentMaster;
use App\Models\DocumentApproved;
use App\Models\FixedAssetCategory;
use App\Models\FixedAssetCategorySub;
use App\Models\FixedAssetMaster;
use App\Models\LogUploadAssetCosting;
use App\Models\SegmentMaster;
use App\Models\UploadAssetCosting;
use App\Services\GeneralLedger\AssetCreationService;
use App\Validations\AssetManagement\ValidateAssetCreation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;

class AssetCostingUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $db;
    protected $uploadData;
    protected $employee;
    protected $decodeFile;
    protected $assetCreationService;

    public function __construct($db, $uploadData)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
        $this->db = $db;
        $this->uploadData = $uploadData;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        $uploadData = $this->uploadData;
        $db = $this->db;
        CommonJobService::db_switch($db);

        Log::useFiles(storage_path().'/logs/asset_costing_bulk_insert.log');

        Log::info('Asset costing bulk upload started');

        $logUploadAssetCosting = $uploadData['logUploadAssetCosting'];

        $objPHPExcel = $uploadData['objPHPExcel'];
        $auditCategory = $uploadData['auditCategory'];



        $assetFinanceCategory = AssetFinanceCategory::with(['costaccount', 'accdepaccount', 'depaccount', 'disaccount'])->find($auditCategory);

        $sheet  = $objPHPExcel->getActiveSheet();
        $startRow = 13;
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $detailRows = [];
        $rowNumber = 13;


        for ($row = $startRow; $row <= $highestRow; ++$row) {

            $rowData = [];
            for ($col = 'A'; $col <= $highestColumn; ++$col) {
                $cellValue = $sheet->getCell($col . $row)->getValue();

                if ($col == 'F' || $col == 'L' || $col == 'K') {

                    if($col != 'K' && $cellValue != null) {
                        $validateDate = ValidateAssetCreation::validateDateFormat($cellValue);
                        if ($validateDate['status'] == true) {
                            $cellValue = $validateDate['data'];
                        } else {
                            app(AssetCreationService::class)->assetUploadErrorLog(($row + $startRow), $validateDate['message'] . ' ' . $col, $logUploadAssetCosting->assetCostingUploadID);

                        }
                    }


                }

                $rowData[] = $cellValue;

            }

            $rowData[] = $rowNumber;
            $detailRows[] = $rowData;
            $rowNumber ++;

        }

        $totalRecords = $rowNumber - 13;

        if($totalRecords == 0){
            app(AssetCreationService::class)->assetUploadErrorLog(($row + $startRow), "No records found", $logUploadAssetCosting->assetCostingUploadID);
        }


        $detailRows = collect($detailRows)->chunk(100);

        $jobData = ['logUploadAssetCosting' => $logUploadAssetCosting, 'assetFinanceCategory' => $assetFinanceCategory, 'startRow' => $startRow, 'totalRecords' => $totalRecords];

        foreach($detailRows as  $data) {
            foreach($data as  $assetData) {
                $uploadBudget = UploadAssetCosting::find($logUploadAssetCosting->assetCostingUploadID);

                if($uploadBudget->isCancelled != 1){
                AssetCostingUploadSubJob::dispatch($db, $assetData, $uploadData, $jobData)->onQueue('single');
                } else {
                    app(AssetCreationService::class)->assetUploadErrorLog(($row + $startRow), "Asset costing upload canceled by user", $logUploadAssetCosting->assetCostingUploadID);
                }
            }

        }

        DB::commit();

    }
}
