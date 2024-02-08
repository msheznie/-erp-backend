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
		} else if ($id == 6) {
            $type = trans('custom.employee_payment');
        }else if ($id == 7) {
            $type = trans('custom.employee_advance_payment');
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

	public static function getDeliveryOrderType($orderType)
	{
		$type = "";

		if ($orderType == 1) {
			$type = trans('custom.proforma_invoice');
		} else if ($orderType == 2) {
			$type = trans('custom.item_sales_invoice');
		} else if ($orderType == 3) {
			$type = trans('custom.from_delivery_note');
		}

		return $type;
	}

	public static function getSalesReturnType($returnType)
	{
		$type = "";

		if ($returnType == 1) {
			$type = trans('custom.from_delivery_order');
		} else if ($returnType == 2) {
			$type = trans('custom.from_sales_invoice');
		}

		return $type;
	}

	public static function getjvType($jvType)
	{
		$type = "";

		if($jvType == 0){
			$type = "Standard JV";
		} else if ($jvType == 1) {
			$type = "Revenue Accrual JV";
		} else if ($jvType == 2) {
			$type = "Recurring JV";
		} else if ($jvType == 3) {
			$type = "Salary JV";
		} else if ($jvType == 4) {
			$type = "Allocation JV";
		} else if ($jvType == 5) {
			$type = "PO Accrual JV";
		} else if ($jvType == 6) {
			$type = "Gratuity Accrual JV";
		} else if ($jvType == 7) {
			$type = "Final Settlement Accrual JV";
		} else if ($jvType == 8) {
			$type = "13th Month Accrual JV";
		}

		return $type;
	}

    public static function getrrvType($rrvType)
    {
        $type = "";
        if($rrvType == 0){
            $type = "Recurring JV";
        } else if ($rrvType == 1) {
            $type = "Recurring Customer invoice";
        } else if ($rrvType == 2) {
            $type = "Recurring Supplier invoice";
        }
        return $type;
    }

    public static function getrrvSchedule($rrvSchedule)
    {
        $type = "";
        if($rrvSchedule == 0){
            $type = "Day";
        } else if ($rrvSchedule == 1) {
            $type = "Month";
        } else if ($rrvSchedule == 2) {
            $type = "Year";
        }
        return $type;
    }

    public static function getrrvStatus($rrvStatus)
    {
        $type = "";
        if($rrvStatus == 0){
            $type = "Draft";
        } else if ($rrvStatus == 1) {
            $type = "Confirm";
        } else if ($rrvStatus == 2) {
            $type = "Approved";
        }
        return $type;
    }
}
