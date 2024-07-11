<?php

namespace App\Services\GeneralLedger;

use App\helper\DocumentCodeGenerate;
use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\DepartmentMaster;
use App\Models\FixedAssetMaster;
use App\Models\SegmentMaster;
use App\Repositories\FixedAssetMasterRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssetCreationService extends AppBaseController
{

    public function __construct(FixedAssetMasterRepository $fixedAssetMasterRepo)
    {
        $this->fixedAssetMasterRepository = $fixedAssetMasterRepo;
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

        if(isset($input['isCurrencySame']) && $input['isCurrencySame']==true){

            if(isset($input['costUnitRpt'])){
                if($input['costUnitRpt'] > 0){
                    $input['COSTUNIT'] = $input['costUnitRpt'];
                }
            }

            if(isset($input['accumulated_depreciation_amount_rpt'])){
                if($input['accumulated_depreciation_amount_rpt'] > 0){
                    $input['accumulated_depreciation_amount_lcl'] = $input['accumulated_depreciation_amount_rpt'];
                }
            }

            if(isset($input['salvage_value_rpt'])){
                if($input['salvage_value_rpt']> 0){
                    $input['salvage_value'] = $input['salvage_value_rpt'];
                }
            }
        }

        if(doubleval($input['salvage_value_rpt']) >  (doubleval($input['costUnitRpt']))) {
            return $this->sendError("Salvage Value Cannot be greater than Unit Price", 500);
        }

        if(doubleval($input['salvage_value_rpt']) < 0) {
            return $this->sendError("Salvage value cannot be less than Zero", 500);
        }

        if($input['assetType'] == 1){
            if(empty($input['depMonth']) || $input['depMonth'] == 0){
                return $this->sendError("Life time in Years cannot be Blank or Zero, update the lifetime of the asset to proceed", 500);
            }
        } else {
            if(isset($input['depMonth']) && $input['depMonth'] == ''){
                $input['depMonth'] = 0;
            }
        }

        if(isset($input['COSTUNIT']) && $input['COSTUNIT'] > 0 ){
            if(isset($input['costUnitRpt']) && $input['costUnitRpt'] <= 0 ){
                return $this->sendError('Unit Price(Rpt) can’t be Zero when Unit Price(Local) has a value',500);
            }
        }

        if(isset($input['accumulated_depreciation_amount_lcl']) && $input['accumulated_depreciation_amount_lcl'] > 0){
            if(isset($input['accumulated_depreciation_amount_rpt']) && $input['accumulated_depreciation_amount_rpt'] <= 0 ){
                return $this->sendError('Acc. Depreciation(Rpt) can’t be Zero when Acc. Depreciation (Local) has a value',500);
            }
        }

        if(isset($input['salvage_value']) && $input['salvage_value'] > 0){
            if(isset($input['salvage_value_rpt']) && $input['salvage_value_rpt'] <= 0 ){
                return $this->sendError('Residual Value(Rpt) can’t be Zero when Residual Value(Local) has a value',500);
            }
        }


        if(isset($input['costUnitRpt']) && $input['costUnitRpt'] > 0 ){
            if(isset($input['COSTUNIT']) && $input['COSTUNIT'] <= 0 ){
                return $this->sendError('Unit Price(Local) can’t be Zero when Unit Price(Rpt) has a value',500);
            }
        }

        if(isset($input['accumulated_depreciation_amount_rpt']) && $input['accumulated_depreciation_amount_rpt'] > 0){
            if(isset($input['accumulated_depreciation_amount_lcl']) && $input['accumulated_depreciation_amount_lcl'] <= 0 ){
                return $this->sendError('Acc. Depreciation(Local) can’t be Zero when Acc. Depreciation (Rpt) has a value',500);
            }
        }

        if(isset($input['salvage_value_rpt']) && $input['salvage_value_rpt'] > 0){
            if(isset($input['salvage_value']) && $input['salvage_value'] <= 0 ){
                return $this->sendError('Residual Value(Local) can’t be Zero when Residual Value(Rpt) has a value',500);
            }
        }


        DB::beginTransaction();
        try {
            $messages = [
                'dateDEP.after_or_equal' => 'Depreciation Date cannot be less than Date aquired',
                'documentDate.before_or_equal' => 'Document Date cannot be greater than DEP Date',
                'faUnitSerialNo.unique' => 'The FA Serial-No has already been taken',
                'AUDITCATOGARY.required' => 'Audit Category is required',
            ];
            $validator = \Validator::make($input, [
                'dateAQ' => 'required|date',
                'AUDITCATOGARY' => 'required',
                'dateDEP' => 'required|date|after_or_equal:dateAQ',
                'documentDate' => 'required|date|before_or_equal:dateDEP',
                'faUnitSerialNo' => 'required|unique:erp_fa_asset_master',
            ], $messages);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            if (isset($input['itemPicture'])) {
                if ($itemImgaeArr[0]['size'] > env('ATTACH_UPLOAD_SIZE_LIMIT')) {
                    return $this->sendError("Maximum allowed file size is exceeded. Please upload lesser than".\Helper::bytesToHuman(env('ATTACH_UPLOAD_SIZE_LIMIT')), 500);
                }
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
                    return $this->sendError("Asset code is already found.", 500);
                }

                $input["serialNo"] = null;
            } else {
                return $this->sendError("Asset code is not configured.", 500);
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
            return $this->sendResponse($fixedAssetMasters, 'Fixed Asset Master saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }
}
