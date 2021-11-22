<?php

namespace App\helper;
use Carbon\Carbon;
use App\Models\CustomerMaster;
use App\Models\GeneralLedger;
use App\Models\Company;
use App\Models\CurrencyMaster;

class BlockInvoice
{
	public static function blockCustomerInvoiceByCreditLimit($documentSystemID, $masterRecord)
	{
		
		if (isset($masterRecord->customerID) && $masterRecord->customerID > 0 && $masterRecord->isPerforma != 1) {
			$customerData = CustomerMaster::find($masterRecord->customerID);

			if (!$customerData) {
				return ['status' => false, 'message' => "Customer not found"];
			}

			if ($customerData->creditLimit > 0 && $customerData->custGLAccountSystemID != null) {
				$customerOutsanding = GeneralLedger::where('companySystemID', $masterRecord->companySystemID)
							                        ->whereIn('documentSystemID', [20, 19, 21, 71])
							                        ->where('supplierCodeSystem', $masterRecord->customerID)
							                        ->where('chartOfAccountSystemID', $customerData->custGLAccountSystemID)
							                        ->sum('documentRptAmount');

				$customerNewOutsanding = floatval($masterRecord->bookingAmountRpt) +  $customerOutsanding;

				if ($customerNewOutsanding > 0 && ($customerNewOutsanding > $customerData->creditLimit)) {
					$reportCurrencyDecimalPlace = 2;
					$currencyCode = "USD";
					$comanyMasterData = Company::find($masterRecord->companySystemID);
					if ($comanyMasterData) {
						$currencyData = CurrencyMaster::find($comanyMasterData->reportingCurrency);
						if ($currencyData) {
							$reportCurrencyDecimalPlace = $currencyData->DecimalPlaces;
							$currencyCode = $currencyData->CurrencyCode;
						}
					}
					$customerOutsandingFormated = number_format($customerNewOutsanding, $reportCurrencyDecimalPlace);

					return ['status' => false, 'message' => "Invoice creation blocked. The selected customer’s current outstanding has exceeded the credit limit. Current outstanding is ".$customerOutsandingFormated." ".$currencyCode];
				}
			}
		}

		return ['status' => true];
	}
}