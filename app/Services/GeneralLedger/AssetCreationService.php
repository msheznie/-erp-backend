<?php

namespace App\Services\GeneralLedger;

use App\Exceptions\AssetCostingException;
use App\helper\DocumentCodeGenerate;
use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\DepartmentMaster;
use App\Models\DocumentApproved;
use App\Models\FinanceCategorySerial;
use App\Models\FixedAssetCost;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use App\Models\GeneralLedger;
use App\Models\LogUploadAssetCosting;
use App\Models\SegmentMaster;
use App\Models\TemporaryAssetSerial;
use App\Models\UploadAssetCosting;
use App\Repositories\FixedAssetMasterRepository;
use App\Traits\JsonResponseTrait;
use App\Validations\AssetManagement\ValidateAssetCreation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AssetCreationService extends AppBaseController
{
    use JsonResponseTrait;

    public function __construct(FixedAssetMasterRepository $fixedAssetMasterRepo)
    {
        $this->fixedAssetMasterRepository = $fixedAssetMasterRepo;

    }

    public function assetUploadErrorLog($errorLine, $logMessage, $assetCostingUploadID){

        Log::useFiles(storage_path() . '/logs/asset_costing_bulk_insert.log');
        Log::info('Start error log');

        DB::beginTransaction();
        try {
            $assetLog = [
                'isFailed' => 1,
                'errorLine' => $errorLine,
                'logMessage' => \Helper::handleErrorData($logMessage)
            ];

            Log::info('Start error log1');

            DB::commit();


            LogUploadAssetCosting::where('assetCostingUploadID', $assetCostingUploadID)->update($assetLog);
            UploadAssetCosting::where('id', $assetCostingUploadID)->update(['uploadStatus' => 0]);
            DB::commit();
        } catch (\Exception $e) {
            Log::error('Exception caught: ' . $e->getMessage());
            Log::error('Error Line No: ' . $e->getLine());
            Log::error('Error File: ' . $e->getFile());
            DB::rollBack();
        }
    }

    public function assetDeletion($assetCostingUploadID, $isFailed)
    {
        $createdFAs = FixedAssetMaster::where('assetCostingUploadID', $assetCostingUploadID)->get();
        foreach ($createdFAs as $createdFA){


            if($isFailed == 1) {
                $allRecords = TemporaryAssetSerial::all();

                foreach ($allRecords as $allRecord) {
                    FinanceCategorySerial::where('id', $allRecord->serialID)->update(['lastSerialNo' => $allRecord->lastSerialNo]);
                }

                $financeSerialIds = TemporaryAssetSerial::pluck('serialID');

                if ($financeSerialIds->isNotEmpty() && $allRecords->isNotEmpty()) {
                    FinanceCategorySerial::whereNotIn('id', $financeSerialIds)->delete();
                }
            }


            GeneralLedger::where('documentSystemID', 22)->where('documentSystemCode', $createdFA->faID)->delete();
            DocumentApproved::where('documentSystemID', 22)->where('documentSystemCode', $createdFA->faID)->delete();
            FixedAssetCost::where('faID', $createdFA->faID)->delete();
            $fixedDeps = FixedAssetDepreciationPeriod::where('faID', $createdFA->faID)->get();
            $depMasterAutoIDs = $fixedDeps->pluck('depMasterAutoID');
            GeneralLedger::where('documentSystemID', 23)->whereIn('documentSystemCode', $depMasterAutoIDs)->delete();
            FixedAssetDepreciationMaster::whereIn('depMasterAutoID', $depMasterAutoIDs)->delete();
            FixedAssetDepreciationPeriod::where('faID', $createdFA->faID)->delete();
        }
        TemporaryAssetSerial::truncate();
        FixedAssetMaster::where('assetCostingUploadID', $assetCostingUploadID)->forceDelete();
    }

    public function assetCreation(array $input)
    {
        $itemImgaeArr = $input['itemImage'];
        $itemPicture = $input['itemPicture'];
        $input = array_except($input, 'itemImage');
        $accumulated_amount = $input['accumulated_depreciation_amount_rpt'];

        // if($input['assetType'] == 1  && ($accumulated_amount > 0 && $accumulated_amount != null) )
        // {
        //     $is_pending_job_exist = FixedAssetDepreciationMaster::where('approved','=',0)->where('is_acc_dep','=',0)->where('is_cancel','=',0)->where('companySystemID' ,'=', $input['companySystemID'])->count();
        //     if($is_pending_job_exist > 0)
        //     {
        //         return $this->sendError('There are Monthly Depreciation pending for confirmation and approval, thus this asset creation cannot be processed', 500);

        //     }

        // }
        $input = $this->convertArrayToValue($input);

        $input['COSTUNIT'] = floatval($input['COSTUNIT']);

        $response = ValidateAssetCreation::validationsForAssetCreation($input);

        if ($response['status'] === false) {
            return $this->sendJsonResponse(false, $response['message'], $response['code']);
        }

        DB::beginTransaction();
        try {

            $response = ValidateAssetCreation::validationsForFields($input, $itemImgaeArr);

            if ($response['status'] === false) {
                return $this->sendJsonResponse(false, $response['message'], $response['code']);
            }

            $input['serviceLineSystemID'] = $input["serviceLineSystemID"];
            $segment = SegmentMaster::find($input['serviceLineSystemID']);
            if ($segment) {
                $input['serviceLineCode'] = $segment->ServiceLineCode;
            }

            $company = Company::find($input['companySystemID']);
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            $department = DepartmentMaster::find($input['departmentSystemID']);
            if ($department) {
                $input['departmentID'] = $department->DepartmentID;
            }

            if (isset($input['postToGLYN'])) {
                if ($input['postToGLYN']) {
                    $chartOfAccount = ChartOfAccount::find($input['postToGLCodeSystemID']);
                    if (!empty($chartOfAccount)) {
                        $input['postToGLCode'] = $chartOfAccount->AccountCode;
                    }
                    $input['postToGLYN'] = 1;
                } else {
                    $input['postToGLYN'] = 0;
                }
            } else {
                $input['postToGLYN'] = 0;
            }

            $input["documentSystemID"] = 22;
            $input["documentID"] = 'FA';

            if (isset($input['dateAQ'])) {
                if ($input['dateAQ']) {
                    $input['dateAQ'] = new Carbon($input['dateAQ']);
                }
            }

            if (isset($input['dateDEP'])) {
                if ($input['dateDEP']) {
                    $input['dateDEP'] = new Carbon($input['dateDEP']);
                }
            }

            if (isset($input['accumulated_depreciation_date'])) {
                if ($input['accumulated_depreciation_date']) {
                    $input['accumulated_depreciation_date'] = new Carbon($input['accumulated_depreciation_date']);
                }
            }

            if (isset($input['lastVerifiedDate'])) {
                if ($input['lastVerifiedDate']) {
                    $input['lastVerifiedDate'] = new Carbon($input['lastVerifiedDate']);
                }
            }

            if (isset($input['documentDate'])) {
                if ($input['documentDate']) {
                    $input['documentDate'] = new Carbon($input['documentDate']);
                }
            }

            $lastSerialNumber = 1;
            $lastSerial = FixedAssetMaster::selectRaw('MAX(serialNo) as serialNo')->first();
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            $auditCategory = isset($input['AUDITCATOGARY']) ? $input['AUDITCATOGARY'] : null;
            $documentCodeData = DocumentCodeGenerate::generateAssetCode($auditCategory, $input['companySystemID'], $input['serviceLineSystemID'],$input['faCatID'],$input['faSubCatID']);
            if ($documentCodeData['status']) {
                $documentCode = $documentCodeData['documentCode'];
                $searchDocumentCode = str_replace("\\", "\\\\", $documentCode);
                $checkForDuplicateCode = FixedAssetMaster::where('faCode', $searchDocumentCode)
                    ->first();

                if ($checkForDuplicateCode) {
                    return $this->sendJsonResponse(false,"Asset code is already found.", 500);
                }

                $input["serialNo"] = null;
            } else {
                return $this->sendJsonResponse(false,"Asset code is not configured.", 500);
                // $documentCode = ($input['companyID'] . '\\FA' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));
                // $input["serialNo"] = $lastSerialNumber;
            }

            $input["faCode"] = $documentCode;
            $input["faBarcode"] = $documentCode;

            if(isset($input['isCurrencySame']) && $input['isCurrencySame'] == true) {
                if ($input['costUnitRpt']) {
                    $input['COSTUNIT'] = $input['costUnitRpt'];
                }
                if ($input['salvage_value_rpt']) {
                    $input['salvage_value'] = $input['salvage_value_rpt'];
                }
                if ($input['accumulated_depreciation_amount_rpt']) {
                    $input['accumulated_depreciation_amount_lcl'] = $input['accumulated_depreciation_amount_rpt'];
                }
            }


            $input['createdPcID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $input['createdDateAndTime'] = date('Y-m-d H:i:s');
            unset($input['itemPicture']);



            $fixedAssetMasters = $this->fixedAssetMasterRepository->create($input);

            if ($itemPicture) {
                $decodeFile = base64_decode($itemImgaeArr[0]['file']);
                $extension = $itemImgaeArr[0]['filetype'];
                $data['itemPicture'] = $input['companyID'] . '_' . $input["documentID"] . '_' . $fixedAssetMasters['faID'] . '.' . $extension;

                $disk = Helper::policyWiseDisk($input['companySystemID'], 'public');
                $awsPolicy = Helper::checkPolicy($input['companySystemID'], 50);

                if ($awsPolicy) {
                    $path = $input['companyID']. '/G_ERP/' .$input["documentID"] . '/' . $fixedAssetMasters['faID'] . '/' . $data['itemPicture'];
                } else {
                    $path = $input["documentID"] . '/' . $fixedAssetMasters['faID'] . '/' . $data['itemPicture'];
                }
                $data['itemPath'] = $path;
                Storage::disk($disk)->put($path, $decodeFile);
                $fixedAssetMasters = $this->fixedAssetMasterRepository->update($data, $fixedAssetMasters['faID']);
            }

            DB::commit();
            return $this->sendJsonResponse(true, 'Fixed Asset Master saved successfully', 200, $fixedAssetMasters);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendJsonResponse(false,$exception->getMessage());
        }
    }

    public function assetApproval($logUploadAssetCosting, $uploadedCompany, $db){
        $assetCostings = FixedAssetMaster::where('assetCostingUploadID', $logUploadAssetCosting->assetCostingUploadID)->where('approved', 0)->get();

        foreach ($assetCostings as $assetCost) {
            $params = array('autoID' => $assetCost->faID,
                'company' => $uploadedCompany,
                'document' => 22,
                'segment' => '',
                'category' => '',
                'amount' => '',
                'isAutoCreateDocument' => true
            );

            Log::info("on confirm");



            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {

                return $this->sendJsonResponse(false,$confirm['message']);
            }
            $documentApproveds = DocumentApproved::where('documentSystemCode', $assetCost->faID)->where('documentSystemID', 22)->get();
            foreach ($documentApproveds as $documentApproved) {
                $documentApproved["approvedComments"] = "Approved by System User";
                $documentApproved["db"] = $db;
                $documentApproved["isAutoCreateDocument"] = true;
                $documentApproved["isDocumentUpload"] = true;
                $approve = \Helper::approveDocument($documentApproved);
                if (!$approve["success"]) {

                    return $this->sendJsonResponse(false,$approve['message']);

                }
            }

        }

    }
}
