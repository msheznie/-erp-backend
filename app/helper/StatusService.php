<?php

namespace App\helper;

class StatusService
{
	public static function getStatus($cancel = NULL, $manualClose = NULL, $confirm = NULL, $approve = NULL, $timesReferred = NULL)
	{
		$status = "";

		if ($cancel == -1) {
			$status = trans('custom.cancelled');
		} else if ($confirm  == 0 && $approve == 0) {
			$status = trans('custom.not_confirmed');
		}
		else if ($confirm  == 1 && $approve == 0 && $timesReferred == 0) {
			$status = trans('custom.pending_approval');
		} 
		else if ($confirm  == 1 && $approve == 0 && $timesReferred == -1) {
			$status = trans('custom.referred_back');
		}
		else if ($confirm  == 1 && ($approve == -1 || $approve == 1 )) {
			$status = trans('custom.fully_approved');
		}

		return $status;
	}

	public static function getInvoiceType($id)
	{
		$type = "";

		if ($id == 2) {
			$type = trans('custom.supplier_po_payment');
		} else if ($id == 3) {
			$type = trans('custom.direct_payment');
		} else if ($id == 5) {
			$type = trans('custom.supplier_advance_payment');
		}

		return $type;
	}
}