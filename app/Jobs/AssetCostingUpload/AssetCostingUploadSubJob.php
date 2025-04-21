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
use App\Models\Location;
use App\Models\SegmentMaster;
use App\Models\UploadAssetCosting;
use App\Services\GeneralLedger\AssetCreationService;
use App\Validations\AssetManagement\ValidateAssetCreation;
use DateTime;
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

        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        $uploadData = $this->uploadData;
        $data = $this->data;
        $jobData = $this->jobData;
        $index = $this->index;
        $startRow = $jobData['startRow'];
        $allSerialRecords = $jobData['allSerialRecords'];
        $logUploadAssetCosting = $jobData['logUploadAssetCosting'];
        $assetFinanceCategory = $jobData['assetFinanceCategory'];
        $totalRecords = $jobData['totalRecords'];
        $auditCategory = $uploadData['auditCategory'];
        $assetCostingTypeID = $uploadData['assetCostingTypeID'];
        $uploadedCompany = $uploadData['uploadedCompany'];
        $postToGL = isset($uploadData['postToGL']) ? $uploadData['postToGL']: null;
        $postToGLCodeSystemID = isset($uploadData['postToGLCodeSystemID']) ? $uploadData['postToGLCodeSystemID']: null;
        $uploadBudgetCounter = UploadAssetCosting::find($logUploadAssetCosting->assetCostingUploadID);

        DB::beginTransaction();
        try {


            $uploadCount = isset($uploadBudgetCounter->counter) ? $uploadBudgetCounter->counter : null;
            $cancelledStatus = isset($uploadBudgetCounter->isCancelled) ? $uploadBudgetCounter->isCancelled : null;


            if ($cancelledStatus == 1) {
                throw new AssetCostingException("Cancelled by User", $logUploadAssetCosting->assetCostingUploadID, 0);
            }

            if($uploadCount !== null) {

                $assetCostingValue = $data;

                $department = DepartmentMaster::where('DepartmentDescription', $assetCostingValue[0])->first();
                if (isset($assetCostingValue[0]) && $assetCostingValue[0] && empty($department)) {
                    throw new AssetCostingException("Department not found", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                $segment = SegmentMaster::where('ServiceLineCode', $assetCostingValue[1])->where('companySystemID', $uploadedCompany)->first();
                if (empty($segment)) {
                    throw new AssetCostingException("Segment not found", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                if ($assetCostingValue[3] == null) {
                    throw new AssetCostingException("Description is required", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                if ($assetCostingValue[4] == null) {
                    throw new AssetCostingException("Manufacture is required", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                $company = Company::find($uploadedCompany);

                if ($company->localCurrencyID != $company->reportingCurrency) {
                    $unitPriceLocal = $assetCostingValue[14];
                    $unitPriceRpt = $assetCostingValue[15];
                    $lclAmountLocal = $assetCostingValue[16];
                    $lclAmountRpt = $assetCostingValue[17];
                    $accumulatedDate = $assetCostingValue[18];
                    $residualLocal = isset($assetCostingValue[19]) ? $assetCostingValue[19] : null;
                    $residualRpt = isset($assetCostingValue[20]) ? $assetCostingValue[20]: null;
                    $isCurrencySame = false;
                } else {
                    $unitPriceLocal = $assetCostingValue[14];
                    $unitPriceRpt = $assetCostingValue[14];
                    $lclAmountLocal = $assetCostingValue[15];
                    $lclAmountRpt = $assetCostingValue[15];
                    $accumulatedDate = $assetCostingValue[16];
                    $residualLocal = $assetCostingValue[17];
                    $residualRpt = $assetCostingValue[17];
                    $isCurrencySame = true;
                }

                $depPercentage = $assetCostingValue[13];
                $comments = $assetCostingValue[8];
                $location = $assetCostingValue[9];
                $lastPhyDate = $assetCostingValue[10];

                if ($accumulatedDate != null) {
                    $validateDate = ValidateAssetCreation::validateDateFormat($accumulatedDate);
                    if ($validateDate['status'] == true) {
                        $accumulatedDate = $validateDate['data'];
                    } else {
                        throw new AssetCostingException($validateDate['message'] . ' ' . 'Accumulated Depreciation Date', $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));

                    }
                }


                if ($location != null) {
                    $location = Location::where('locationName', $location)->where('companySystemID', $uploadedCompany)->first();
                    if (empty($location)) {
                        throw new AssetCostingException("Location not found", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                    }
                }

                $lclAmountLocal = $lclAmountLocal ?? 0;
                $lclAmountRpt = $lclAmountRpt ?? 0;
                $residualLocal = $residualLocal ?? 0;
                $residualRpt = $residualRpt ?? 0;

                $mainCategory = $assetCostingValue[6];
                $mainCategoryData = FixedAssetCategory::where('catCode', $mainCategory)->where('companySystemID', $uploadedCompany)->first();
                if (empty($mainCategoryData)) {
                    throw new AssetCostingException("Main category not found", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                $subCategory = $assetCostingValue[7];

                $subCategoryData = FixedAssetCategorySub::where('suCatCode', $subCategory)->where('companySystemID', $uploadedCompany)->first();
                if (empty($subCategoryData)) {
                    throw new AssetCostingException("Sub category not found", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                $subCategoryData = FixedAssetCategorySub::where('suCatCode', $subCategory)->where('faCatID', $mainCategoryData->faCatID)->where('companySystemID', $uploadedCompany)->first();
                if (empty($subCategoryData)) {
                    throw new AssetCostingException("The subcategory code is not associated with the main category", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }


                if ($assetCostingValue[5] == null) {
                    throw new AssetCostingException("Date Acquired is required", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                if ($assetCostingValue[11] == null) {
                    throw new AssetCostingException("Dep Start Date is required", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }


                $dateAcquired = DateTime::createFromFormat('m/d/Y', $assetCostingValue[5]);
                $depStartDate = DateTime::createFromFormat('m/d/Y', $assetCostingValue[11]);

                if (!$dateAcquired || !$depStartDate) {
                    $invalidDate = !$dateAcquired ? 'Date Acquired' : 'Dep Start Date';
                    throw new AssetCostingException("$invalidDate is invalid or not in the expected format.",
                        $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                if($depStartDate < $dateAcquired){
                    throw new AssetCostingException("Date Acquired cannot be greater than Dep Start Date ", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                if ($depPercentage == null) {
                    throw new AssetCostingException("Dep Percentage is required", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                if ($assetCostingValue[12] == null) {
                    $assetCostingValue[12] = $assetFinanceCategory->lifeTimeInYears;
                }

                if ($unitPriceLocal === null || $unitPriceLocal === '') {
                    throw new AssetCostingException("Unit Price (Local) is required", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                if ($unitPriceRpt === null || $unitPriceRpt === '') {
                    throw new AssetCostingException("Unit Price (Rpt) is required", $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                $validatePositives = ValidateAssetCreation::validationsForAssetUpload($assetCostingValue[12], $depPercentage, $unitPriceLocal, $unitPriceRpt, $lclAmountLocal, $lclAmountRpt, $residualLocal, $residualRpt, $accumulatedDate, $assetCostingValue[11]);
                if ($validatePositives['status'] === false) {
                    throw new AssetCostingException($validatePositives['message'], $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }
                $validateFY = ValidateAssetCreation::validateCompanyFinanceYearPeriod($uploadedCompany, $assetCostingValue[5]);
                if ($validateFY['status'] === false) {
                    throw new AssetCostingException($validateFY['message'], $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                if ($accumulatedDate != null) {
                    $validateFY = ValidateAssetCreation::validateCompanyFinanceYearPeriod($uploadedCompany, $accumulatedDate);
                    if ($validateFY['status'] === false) {
                        throw new AssetCostingException($validateFY['message'], $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                    }
                }


                $assetUpload = [
                    "departmentSystemID" => isset($department->departmentSystemID) ? $department->departmentSystemID : null,
                    "serviceLineSystemID" => $segment->serviceLineSystemID,
                    "assetDescription" => $assetCostingValue[3],
                    "MANUFACTURE" => $assetCostingValue[4],
                    "dateAQ" => $assetCostingValue[5],
                    "dateDEP" => $assetCostingValue[11],
                    "documentDate" => $assetCostingValue[5],
                    "depMonth" => $assetCostingValue[12],
                    "DEPpercentage" => $depPercentage,
                    "costUnitRpt" => $unitPriceRpt,
                    "COSTUNIT" => $unitPriceLocal,
                    "accumulated_depreciation_amount_lcl" => $lclAmountLocal,
                    "accumulated_depreciation_amount_rpt" => $lclAmountRpt,
                    "accumulated_depreciation_date" => $accumulatedDate,
                    "COMMENTS" => $comments,
                    "LOCATION" => isset($location->locationID) ? $location->locationID : null,
                    "lastVerifiedDate" => $lastPhyDate,
                    "assetType" => $assetCostingTypeID,
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
                    "faUnitSerialNo" => (string) $assetCostingValue[2],
                    "confirmedYN" => null,
                    "groupTO" => null,
                    "postToGLYN" => $postToGL,
                    "postToGLCodeSystemID" => $postToGLCodeSystemID,
                    "salvage_value_rpt" => $residualRpt,
                    "salvage_value" => $residualLocal,
                    "companySystemID" => $uploadedCompany,
                    "documentSystemID" => 22,
                    "isCurrencySame" => $isCurrencySame,
                    "assetCostingUploadID" => $logUploadAssetCosting->assetCostingUploadID
                ];

                $assetCreate = app(AssetCreationService::class)->assetCreation($assetUpload);

                if ($assetCreate['status'] === false) {
                    throw new AssetCostingException($assetCreate['message'], $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }


                $assetApproval = app(AssetCreationService::class)->assetApproval($logUploadAssetCosting, $uploadedCompany, $db);

                if ($assetApproval['status'] === false) {
                    throw new AssetCostingException($assetApproval['message'], $logUploadAssetCosting->assetCostingUploadID, ($uploadCount + $startRow));
                }

                $uploadBudgetCounter->increment('counter');

                $uploadBudgetCounter->save();

                $newCounterValue = $uploadBudgetCounter->counter;

                if ($newCounterValue == $totalRecords) {
                    UploadAssetCosting::where('id', $logUploadAssetCosting->assetCostingUploadID)->update(['uploadStatus' => 1]);
                }
                DB::commit();
            }

        } catch(AssetCostingException $e) {
            DB::rollback();

            $errorMessage = $e->getMessage();
            $assetCostingUploadID = $e->getAssetCostingUploadID();
            $excelRow = $e->getExcelRow();

            app(AssetCreationService::class)->assetUploadErrorLog($excelRow, $errorMessage, $assetCostingUploadID);
            app(AssetCreationService::class)->assetDeletion($assetCostingUploadID, 1);

            Log::error('Error Message' . $errorMessage);
            Log::error('Asset Costing Upload ID: ' . $assetCostingUploadID);
            Log::error('Excel Row: ' . $excelRow);

        }

        $uploadStatus = isset($uploadBudgetCounter->uploadStatus) ? $uploadBudgetCounter->uploadStatus : null;

        if($uploadStatus === 0){
            app(AssetCreationService::class)->assetDeletion($logUploadAssetCosting->assetCostingUploadID, 1);
        }
    }
}
