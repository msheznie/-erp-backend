<?php

namespace App\Validations\AssetManagement;

use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\UploadAssetCosting;
use App\Services\GeneralLedger\AssetCreationService;
use App\Traits\JsonResponseTrait;
use Carbon\Carbon;
use DateTime;


class ValidateAssetCreation
{
    use JsonResponseTrait;

    public static function validationsForAssetCreation($input){
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
            return self::sendJsonResponse(false,"Salvage Value Cannot be greater than Unit Price", 500);
        }

        if(doubleval($input['salvage_value_rpt']) < 0) {
            return self::sendJsonResponse(false,"Salvage value cannot be less than Zero", 500);
        }

        if($input['assetType'] == 1){
            if(empty($input['depMonth']) || $input['depMonth'] == 0){
                return self::sendJsonResponse(false,"Life time in Years cannot be Blank or Zero, update the lifetime of the asset to proceed", 500);
            }
        } else {
            if(isset($input['depMonth']) && $input['depMonth'] == ''){
                $input['depMonth'] = 0;
            }
        }

        if(isset($input['COSTUNIT']) && $input['COSTUNIT'] > 0 ){
            if(isset($input['costUnitRpt']) && $input['costUnitRpt'] <= 0 ){
                return self::sendJsonResponse(false,'Unit Price(Rpt) can’t be Zero when Unit Price(Local) has a value',500);
            }
        }

        if(isset($input['accumulated_depreciation_amount_lcl']) && $input['accumulated_depreciation_amount_lcl'] > 0){
            if(isset($input['accumulated_depreciation_amount_rpt']) && $input['accumulated_depreciation_amount_rpt'] <= 0 ){
                return self::sendJsonResponse(false,'Acc. Depreciation(Rpt) can’t be Zero when Acc. Depreciation (Local) has a value',500);
            }
        }

        if(isset($input['salvage_value']) && $input['salvage_value'] > 0){
            if(isset($input['salvage_value_rpt']) && $input['salvage_value_rpt'] <= 0 ){
                return self::sendJsonResponse(false,'Residual Value(Rpt) can’t be Zero when Residual Value(Local) has a value',500);
            }
        }


        if(isset($input['costUnitRpt']) && $input['costUnitRpt'] > 0 ){
            if(isset($input['COSTUNIT']) && $input['COSTUNIT'] <= 0 ){
                return self::sendJsonResponse(false,'Unit Price(Local) can’t be Zero when Unit Price(Rpt) has a value',500);
            }
        }

        if(isset($input['accumulated_depreciation_amount_rpt']) && $input['accumulated_depreciation_amount_rpt'] > 0){
            if(isset($input['accumulated_depreciation_amount_lcl']) && $input['accumulated_depreciation_amount_lcl'] <= 0 ){
                return self::sendJsonResponse(false,'Acc. Depreciation(Local) can’t be Zero when Acc. Depreciation (Rpt) has a value',500);
            }
        }

        if(isset($input['salvage_value_rpt']) && $input['salvage_value_rpt'] > 0){
            if(isset($input['salvage_value']) && $input['salvage_value'] <= 0 ){
                return self::sendJsonResponse(false,'Residual Value(Local) can’t be Zero when Residual Value(Rpt) has a value',500);
            }
        }

        return self::sendJsonResponse(true,'All validations are passed',200);
    }

    public static function validationsForFields($input, $itemImgaeArr){
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
            return self::sendJsonResponse(false,$validator->messages(), 422);
        }

        if (isset($input['itemPicture'])) {
            if ($itemImgaeArr[0]['size'] > env('ATTACH_UPLOAD_SIZE_LIMIT')) {
                return self::sendJsonResponse(false,"Maximum allowed file size is exceeded. Please upload lesser than".\Helper::bytesToHuman(env('ATTACH_UPLOAD_SIZE_LIMIT')), 500);
            }
        }
    }

    public static function uploadValidation(){
        $uploadCondition = UploadAssetCosting::where('uploadStatus', -1)->first();

        if(!empty($uploadCondition)){
            return self::sendJsonResponse(false,"Asset costing upload InProgress", 500);
        }
    }

    public static function validateCompanyFinanceYearPeriod($companySystemID, $date){
        try {
            $date = Carbon::createFromFormat('m/d/Y', $date);

            $date = $date->format('Y-m-d');

            $financeYear = CompanyFinanceYear::where('companySystemID', $companySystemID)->where('bigginingDate', "<=", $date)->where('endingDate', ">=", $date)->where('isActive', -1)->first();
            $financePeriod = CompanyFinancePeriod::where('companySystemID', $companySystemID)->where('departmentSystemID', 9)->where('dateFrom', "<=", $date)->where('dateTo', ">=", $date)->where('isActive', -1)->first();

            if(empty($financeYear)){
                return self::sendJsonResponse(false,"Finance year not activated", 500);
            }

            if(empty($financePeriod)){
                return self::sendJsonResponse(false,"Finance period not activated", 500);
            }
        } catch(\Exception $e){
            return self::sendJsonResponse(false,$e->getMessage(), 500);
        }
    }

    public static function validateDateFormat($date){

        if($date == null){
            return self::sendJsonResponse(false,"Please check date of column", 500);
        }
        $datetime = \DateTime::createFromFormat('d-m-Y', $date);
        if ($datetime && $datetime->format('d-m-Y') === $date) {
            $date = $datetime->format('m/d/Y');

            return self::sendJsonResponse(true,'Date format validation passed',200, $date);
        } else {
            return self::sendJsonResponse(false,"Please check date format of column", 500);
        }
    }


    public static function validationsForAssetUpload($lifeTime, $depPercentage, $unitPriceLocal, $unitPriceRpt,$lclAmountLocal, $lclAmountRpt, $residualLocal, $residualRpt, $accumulatedDate, $depDate){
        if ($lifeTime < 0) {
            return self::sendJsonResponse(false,'The Lifetime in a years should be in positive value',500);
        }

        if ($depPercentage < 0) {
            return self::sendJsonResponse(false,'The Dep% should be in positive value',500);
        }

        if ($unitPriceLocal < 0) {
            return self::sendJsonResponse(false,'The Unit Price (Local) should be in positive value',500);
        }

        if ($unitPriceRpt < 0) {
            return self::sendJsonResponse(false,'The Unit Price (Rpt) should be in positive value',500);
        }

        if ($lclAmountLocal != null && $lclAmountLocal < 0) {
            return self::sendJsonResponse(false,'The Accumulated Depreciation Amount (Local) should be in positive value',500);
        }

        if ($lclAmountRpt != null && $lclAmountRpt < 0) {
            return self::sendJsonResponse(false,'The Accumulated Depreciation Amount (Rpt) should be in positive value',500);
        }

        if ($residualLocal != null && $residualLocal < 0) {
            return self::sendJsonResponse(false,'The Residual Value (Local) should be in positive value',500);
        }

        if ($residualRpt != null && $residualRpt < 0) {
            return self::sendJsonResponse(false,'The Residual Value (Rpt) should be in positive value',500);
        }



        if ($residualLocal != null && $lclAmountLocal != null && $residualLocal > $lclAmountLocal) {
            return self::sendJsonResponse(false,'The Residual Value (Local) cannot be greater than the Accumulated Depreciation Amount (Local)',500);
        }

        if ($residualRpt != null && $lclAmountRpt != null && $residualRpt > $lclAmountRpt) {
            return self::sendJsonResponse(false,'The Residual Value (Local) cannot be greater than the Accumulated Depreciation Amount (Local)',500);
        }

        if ($lclAmountLocal != null && $lclAmountLocal > $unitPriceLocal) {
            return self::sendJsonResponse(false,'The Accumulated Depreciation Amount (Local) cannot be greater than Unit Cost (Local)',500);
        }

        if ($lclAmountRpt != null && $lclAmountRpt > $unitPriceRpt) {
            return self::sendJsonResponse(false,'The Accumulated Depreciation Amount (Rpt) cannot be greater than Unit Cost (Rpt)',500);
        }

        if ($lclAmountRpt != null && ($unitPriceRpt - $lclAmountRpt) < $residualRpt) {
            return self::sendJsonResponse(false,'The Residual value (Rpt) is greater than Net book value',500);
        }

        if ($lclAmountLocal != null && ($unitPriceLocal - $lclAmountLocal) < $residualLocal) {
            return self::sendJsonResponse(false,'The Residual value (Local) is greater than Net book value',500);
        }

        if ($unitPriceRpt < $residualRpt) {
            return self::sendJsonResponse(false,'The Residual value (Rpt) is greater than Unit Price (Rpt)',500);
        }

        if ($unitPriceLocal < $residualLocal) {
            return self::sendJsonResponse(false,'The Residual value (Local) is greater than Unit Price (Local)',500);
        }

        if (($lclAmountRpt != null || $lclAmountLocal != null) && $accumulatedDate == null) {
            return self::sendJsonResponse(false,'The Accumulated Depreciation Date is mandatory',500);
        }

        if ($accumulatedDate !== null) {
            $accumulatedDateObj = DateTime::createFromFormat('m/d/Y', $accumulatedDate);
            $depDateObj = DateTime::createFromFormat('m/d/Y', $depDate);

            if ($accumulatedDateObj && $depDateObj) {
                if ($depDateObj > $accumulatedDateObj) {
                    return self::sendJsonResponse(false,'Accumulated Depreciation Date should be greater than Dep Start Date',500);
                }
            } else {
                return self::sendJsonResponse(false, 'Invalid date format provided.', 500);
            }
        }

    }

}
