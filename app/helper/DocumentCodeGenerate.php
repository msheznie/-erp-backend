<?php

namespace App\helper;
use Carbon\Carbon;
use App\Models\CompanyPolicyMaster;
use App\Models\Company;
use App\Models\AssetFinanceCategory;
use App\Models\FinanceCategorySerial;
use App\Models\ReportTemplateDetails;
use App\Models\SegmentMaster;

class DocumentCodeGenerate
{
	public static function generateAssetCode($auditCategory, $companySystemID, $serviceLineSystemID)
	{
		$financeCategoryData = AssetFinanceCategory::find($auditCategory);

		if ($financeCategoryData && !is_null($financeCategoryData->formula)) {
			$assetCode = "";
			$formula = explode('~', $financeCategoryData->formula);

			$operand = ["-","/"];
			foreach ($formula as $key => $value) {
				$firstChar = substr($value, 0, 1);
				$formulaValue = substr($value,1);
				
				if ($firstChar == "#") {
					$assetCode = $assetCode.$formulaValue;
				}

				if ($firstChar == "|") {
					if (in_array($formulaValue, $operand)) {
						$assetCode = $assetCode.$formulaValue;
					}
				}

				if ($firstChar == "_") {
					$currentSerialNo = 1;
					$checkLastSerail = FinanceCategorySerial::where('faFinanceCatID', $auditCategory)
															->where('companySystemID', $companySystemID)
															->first();

					if ($checkLastSerail) {
						$currentSerialNo = $checkLastSerail->lastSerialNo + 1;

						$checkLastSerail->lastSerialNo = $currentSerialNo;
						$checkLastSerail->save();
					} else {
						$serialData = [
							'faFinanceCatID' => $auditCategory,
							'companySystemID' => $companySystemID,
							'lastSerialNo' => $currentSerialNo
						];	

						FinanceCategorySerial::insert($serialData);
					}

					$assetCode = $assetCode.str_pad($currentSerialNo, $formulaValue, '0', STR_PAD_LEFT);
				}


				if ($firstChar == "$") {
					$code = "";
					if ($formulaValue == 1) {
						$company = Company::find($companySystemID);

						if ($company) {
							$code = $company->CompanyID;
						}
					} else if ($formulaValue == 2) {
						$segment = SegmentMaster::find($serviceLineSystemID);

						if ($segment) {
							$code = $segment->ServiceLineCode;
						}
					}
					$assetCode = $assetCode.$code;
				}
			}

			return ['status' => true, 'documentCode' => $assetCode];
		} else {
			return ['status' => false];
		}
	}

	public static function generateAccountCode($detID)
	{
		$reportCategoryDetail = ReportTemplateDetails::where('detID', $detID)->first();

        if (!$reportCategoryDetail) {
        	return ['status' => false, 'message' => "Category not found", 'data' => ""];
        }

        $code = "";
        if ($reportCategoryDetail->prefix != "") {
            $code = $reportCategoryDetail->prefix.str_pad($reportCategoryDetail->lastSerialNo + 1, $reportCategoryDetail->serialLength, '0', STR_PAD_LEFT);
        }


        return ['status' => true, 'data' => $code];
	}

	public static function updateChartOfAccountSerailNumber($detID)
	{
		$reportCategoryDetail = ReportTemplateDetails::where('detID', $detID)->first();

		$newSerial = $reportCategoryDetail->lastSerialNo + 1;

		$reportCategoryDetail->lastSerialNo = $newSerial;

		$reportCategoryDetail->save();
	}

}