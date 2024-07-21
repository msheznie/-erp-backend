<?php

namespace App\Jobs\AssetCostingUpload;

use App\Exceptions\AssetCostingException;
use App\helper\CommonJobService;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\DepartmentMaster;
use App\Models\DocumentApproved;
use App\Models\FixedAssetCategory;
use App\Models\FixedAssetCategorySub;
use App\Models\FixedAssetMaster;
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

class AssetCostingUploadSubJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $db;
    protected $uploadData;
    protected $data;
    protected $jobData;
    protected $index;
    public function __construct($db, $data, $uploadData, $jobData)
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
        $this->data = $data;
        $this->jobData = $jobData;
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
        Log::useFiles(storage_path() . '/logs/asset_costing_bulk_insert.log');

        Log::info("Starting Sub Job");

        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        $uploadData = $this->uploadData;
        $data = $this->data;
        $jobData = $this->jobData;
        $index = $this->index;
        $startRow = $jobData['startRow'];
        $logUploadAssetCosting = $jobData['logUploadAssetCosting'];
        $assetFinanceCategory = $jobData['assetFinanceCategory'];
        $totalRecords = $jobData['totalRecords'];
        $auditCategory = $uploadData['auditCategory'];
        $uploadedCompany = $uploadData['uploadedCompany'];
        $postToGL = $uploadData['postToGL'];
        $postToGLCodeSystemID = $uploadData['postToGLCodeSystemID'];

        DB::beginTransaction();
        try {

            $uploadBudgetCounter = UploadAssetCosting::find($logUploadAssetCosting->assetCostingUploadID);

            $uploadCount = $uploadBudgetCounter->counter;
            if($uploadBudgetCounter->isCancelled == 1){
                throw new AssetCostingException("Asset Uploading Cancelled by User", $logUploadAssetCosting->assetCostingUploadID,($uploadCount + $startRow));
            }
            $assetCostingValue = $data[0];

            $department = DepartmentMaster::where('DepartmentDescription', $assetCostingValue[0])->first();
            if (empty($department)) {
                throw new AssetCostingException("Department not found", $logUploadAssetCosting->assetCostingUploadID,($uploadCount + $startRow));
            }

            $segment = SegmentMaster::where('ServiceLineDes', $assetCostingValue[1])->first();
            if (empty($segment)) {
                throw new AssetCostingException("Segment not found", $logUploadAssetCosting->assetCostingUploadID,($uploadCount + $startRow));
            }

            $company = Company::find($uploadedCompany);

            if($company->localCurrencyID != $company->reportingCurrency) {
                $unitPriceLocal = $assetCostingValue[10];
                $unitPriceRpt = $assetCostingValue[11];
                $lclAmountLocal = $assetCostingValue[12];
                $lclAmountRpt = $assetCostingValue[13];
                $accumulatedDate = $assetCostingValue[15];
                $residualLocal = $assetCostingValue[16];
                $residualRpt = $assetCostingValue[17];
                $depPercentage = $assetCostingValue[9];
                $comments = null;
                $location = null;
                $lastPhyDate = null;
            } else {
                $unitPriceLocal = $assetCostingValue[14];
                $unitPriceRpt = $assetCostingValue[14];
                $lclAmountLocal = $assetCostingValue[15];
                $lclAmountRpt = $assetCostingValue[15];
                $accumulatedDate = $assetCostingValue[11];
                $depPercentage = $assetCostingValue[13];
                $residualLocal = $assetCostingValue[16];
                $residualRpt = $assetCostingValue[16];
                $comments = $assetCostingValue[8];
                $location = $assetCostingValue[9];
                $lastPhyDate = $assetCostingValue[10];

                $validateDate = ValidateAssetCreation::validateDateFormat($lastPhyDate);
                if($validateDate['status'] == true){
                    $lastPhyDate = $validateDate['data'];
                } else{
                    throw new AssetCostingException($validateDate['message'] . 'K', $logUploadAssetCosting->assetCostingUploadID,($uploadCount + $startRow));

                }
            }

            $mainCategory = $assetCostingValue[6];
            $mainCategoryData = FixedAssetCategory::where('catDescription', $mainCategory)->first();
            if (empty($mainCategoryData)) {
                throw new AssetCostingException("Main category not found", $logUploadAssetCosting->assetCostingUploadID,($uploadCount + $startRow));
            }

            $subCategory = $assetCostingValue[7];

            $subCategoryData = FixedAssetCategorySub::where('catDescription', $subCategory)->first();
            if (empty($subCategoryData)) {
                throw new AssetCostingException("Sub category not found", $logUploadAssetCosting->assetCostingUploadID,($uploadCount + $startRow));
            }

            Log::info($uploadedCompany);
            Log::info($assetCostingValue[5]);
            $validateFY = ValidateAssetCreation::validateCompanyFinanceYearPeriod($uploadedCompany, $assetCostingValue[5]);
            if ($validateFY['status'] === false) {
                throw new AssetCostingException($validateFY['message'], $logUploadAssetCosting->assetCostingUploadID,($uploadCount + $startRow));
            }

            $validateFY = ValidateAssetCreation::validateCompanyFinanceYearPeriod($uploadedCompany, $accumulatedDate);
            if ($validateFY['status'] === false) {
                throw new AssetCostingException($validateFY['message'], $logUploadAssetCosting->assetCostingUploadID,($uploadCount + $startRow));
            }



                $assetUpload = [
                "departmentSystemID" => $department->departmentSystemID,
                "serviceLineSystemID" => $segment->serviceLineSystemID,
                "assetDescription" => $assetCostingValue[3],
                "MANUFACTURE" => $assetCostingValue[4],
                "dateAQ" => $assetCostingValue[5],
                "dateDEP" => $assetCostingValue[5],
                "documentDate" => $assetCostingValue[5],
                "depMonth" => $assetFinanceCategory->lifeTimeInYears,
                "DEPpercentage" => $depPercentage,
                "costUnitRpt" => $unitPriceRpt,
                "COSTUNIT" => $unitPriceLocal,
                "accumulated_depreciation_amount_lcl" => $lclAmountLocal,
                "accumulated_depreciation_amount_rpt" => $lclAmountRpt,
                "accumulated_depreciation_date" => $accumulatedDate,
                "COMMENTS" => $comments,
                "LOCATION" => $location,
                "lastVerifiedDate" => $lastPhyDate,
                "assetType" => 1,
                "supplierIDRentedAsset" => null,
                "AUDITCATOGARY" => $auditCategory,
                "faCatID" => $mainCategoryData->faCatID,
                "faSubCatID" => $subCategoryData->faCatSubID,
                "faSubCatID2" => null,
                "faSubCatID3" => null,
                "costglCodeSystemID" => $assetFinanceCategory->COSTGLCODESystemID,
                "COSTGLCODE" => $assetFinanceCategory->COSTGLCODE,
                "COSTGLCODEdes" => $assetFinanceCategory->costaccount->AccountDescription,
                "accdepglCodeSystemID" => $assetFinanceCategory->ACCDEPGLCODESystemID,
                "ACCDEPGLCODE" => $assetFinanceCategory->ACCDEPGLCODE,
                "ACCDEPGLCODEdes" => $assetFinanceCategory->accdepaccount->AccountDescription,
                "depglCodeSystemID" => $assetFinanceCategory->DEPGLCODESystemID,
                "DEPGLCODE" => $assetFinanceCategory->DEPGLCODE,
                "DEPGLCODEdes" => $assetFinanceCategory->depaccount->AccountDescription,
                "dispglCodeSystemID" => $assetFinanceCategory->DISPOGLCODESystemID,
                "DISPOGLCODE" => $assetFinanceCategory->DISPOGLCODE,
                "DISPOGLCODEdes" => $assetFinanceCategory->disaccount->AccountDescription,
                "itemPicture" => null,
                "itemImage" => null,
                "faUnitSerialNo" => $assetCostingValue[2],
                "confirmedYN" => null,
                "groupTO" => null,
                "postToGLYN" => $postToGL,
                "postToGLCodeSystemID" => $postToGLCodeSystemID,
                "salvage_value_rpt" => $residualRpt,
                "salvage_value" => $residualLocal,
                "companySystemID" => $uploadedCompany,
                "documentSystemID" => 22,
                "isCurrencySame" => false,
                "assetCostingUploadID" => $logUploadAssetCosting->assetCostingUploadID
            ];

            $assetCreate = app(AssetCreationService::class)->assetCreation($assetUpload);

            if ($assetCreate['status'] === false) {
                throw new AssetCostingException($assetCreate['message'], $logUploadAssetCosting->assetCostingUploadID,($uploadCount + $startRow));
            }

            Log::info("Index: ". $index);

            $assetApproval = app(AssetCreationService::class)->assetApproval($logUploadAssetCosting, $uploadedCompany, $db);

            if ($assetApproval['status'] === false) {
                throw new AssetCostingException($assetApproval['message'], $logUploadAssetCosting->assetCostingUploadID,($uploadCount + $startRow));
            }

            $uploadBudgetCounter->increment('counter');

            $uploadBudgetCounter->save();

            $newCounterValue = $uploadBudgetCounter->counter;

            Log::info($newCounterValue);
            Log::info('tot'. $totalRecords);

            if ($newCounterValue == $totalRecords) {
                UploadAssetCosting::where('id', $logUploadAssetCosting->assetCostingUploadID)->update(['uploadStatus' => 1]);
            }

            DB::commit();
        } catch(AssetCostingException $e) {
            DB::rollback();

            $errorMessage = $e->getMessage();
            $assetCostingUploadID = $e->getAssetCostingUploadID();
            $excelRow = $e->getExcelRow();

            Log::info('on catch');

            app(AssetCreationService::class)->assetUploadErrorLog($excelRow, $errorMessage, $assetCostingUploadID);

            Log::error('Error Message' . $errorMessage);
            Log::error('Asset Costing Upload ID: ' . $assetCostingUploadID);
            Log::error('Excel Row: ' . $excelRow);

        }
    }
}
