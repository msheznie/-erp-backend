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


	public static function getCustomerInvoiceType($id)
	{
		$type = "";

		if($id == 0){
			$type = trans('custom.direct_invoice');
		} else if ($id == 1) {
			$type = trans('custom.proforma_invoice');
		} else if ($id == 2) {
			$type = trans('custom.item_sales_invoice');
		} else if ($id == 3) {
			$type = trans('custom.from_delivery_note');
		} else if ($id == 4) {
			$type = trans('custom.from_sales_order');
		} else if ($id == 5) {
			$type = trans('custom.from_quotation');
		}

		return $type;
	}

	public static function getQuotationType($quotationType, $documentSystemID)
	{
		$type = "";

		if($quotationType == 1 && $documentSystemID == 67){
			$type = trans('custom.rental_quotation');
		} else if ($quotationType == 2 && $documentSystemID == 67) {
			$type = trans('custom.sales_quotation');
		} else if ($quotationType == 1 && $documentSystemID == 68) {
			$type = trans('custom.direct_sales');
		} else if ($quotationType == 2 && $documentSystemID == 68) {
			$type = trans('custom.from_quotation');
		}

		return $type;
	}
}    