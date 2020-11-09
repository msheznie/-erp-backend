<?php

namespace App\helper;
use Carbon\Carbon;
use App\Models\CustomerMaster;
use App\Models\GeneralLedger;

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
							                        ->whereIn('documentSystemID', [20, 19, 21])
							                        ->where('chartOfAccountSystemID', $customerData->custGLAccountSystemID)
							                        ->sum('documentRptAmount');

				if ($customerOutsanding > 0 && ($customerOutsanding > $customerData->creditLimit)) {
					return ['status' => false, 'message' => "Invoice creation blocked. The selected customer’s current outstanding has exceeded the credit limit. Current outstanding is ".number_format($customerOutsanding)." USD"];
				}
			}
		}

		return ['status' => true];
	}
}