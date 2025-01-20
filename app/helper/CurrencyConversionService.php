<?php

namespace App\helper;
use App\helper\Helper;
use Carbon\Carbon;
use App\Models\Company;
use App\Models\CurrencyConversion;
use App\Models\CurrencyConversionDetail;
use App\Models\CurrencyConversionHistory;

class CurrencyConversionService
{
	public static function setConversion($input)
	{
		$conversiondetails = CurrencyConversionDetail::where('currencyConversioMasterID', $input['id'])
													 ->get();

		foreach ($conversiondetails as $key => $value) {
			$currencyConversion = CurrencyConversion::where('masterCurrencyID', $value->masterCurrencyID)
													->where('subCurrencyID', $value->subCurrencyID)
													->first();

			if ($currencyConversion) {
				if ($currencyConversion->conversion != $value->conversion) {
		            $serialNo = CurrencyConversionHistory::max('serialNo') + 1;
		            $temData = array(
		                'serialNo' => $serialNo,
		                'masterCurrencyID' => $currencyConversion->masterCurrencyID,
		                'subCurrencyID' => $currencyConversion->subCurrencyID,
		                'conversion' => $currencyConversion->conversion,
		                'createdBy' => $input['createdByEmp'],
		                'createdUserID' => $input['createdByEmpID'],
		                'createdpc' => gethostname()
		            );
		            CurrencyConversionHistory::create($temData);
		        }

				$updateRes = CurrencyConversion::where('currencyConversionAutoID', $currencyConversion->currencyConversionAutoID)
												->update(['conversion' => $value->conversion]);

			}
		}

		return ['status' => true];
	}


	public static function localAndReportingConversionByER($transactionCurrencyID, $documentCurrencyID, $transactionAmount, $transER)
    {
        $trasToSuppER = 1;
        $transactionAmount = \Helper::stringToFloat($transactionAmount);
        $documentAmount = 0;
        if ($documentCurrencyID) {
            $transToDocER = $transER;

            if ($transactionCurrencyID == $documentCurrencyID) {
                $documentAmount = $transactionAmount;
            } else {
                $documentAmount = $transactionAmount / $transToDocER;
            }
        }

        return $documentAmount;
    }
}