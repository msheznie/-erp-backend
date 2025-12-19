<?php

namespace App\helper;
use App\Models\Company;
use Carbon\Carbon;

class CurrencyValidation
{
	public static function validateCurrency($documentSystemID, $masterRecord)
	{
		$docInforArr = self::setDocumentInfo($documentSystemID);

		$detailValidation = [];
		foreach ($docInforArr['detailData'] as $key => $value) {
			
			$namespacedModel = 'App\Models\\' . $value["detailModelName"]; // Model name
	        $details = $namespacedModel::where($value["detailMasterColumnName"], $masterRecord[$docInforArr["masterPrimaryKey"]])
	        							 ->get();

	        if (sizeof($details) > 0) {
	        	$details = $details->toArray();
	        }

        	$detailValidation = self::validateDetailCurrency($details, $value, $detailValidation);
		}
		

        $erroMsg = "";
        $headerValidation = self::validateHeaderCurrency($masterRecord, $docInforArr);

        if (!empty($headerValidation)) {
        	$erroMsg = implode(', ', $headerValidation);

        	$erroMsg = $erroMsg.' of header data';
        }
        
        if (!empty($detailValidation)) {

        	$detailErrMsg = implode(', ', $detailValidation);

        	if ($erroMsg != "") {
        		$erroMsg = $erroMsg.', '.$detailErrMsg.' of detail data';
        	} else {
        		$erroMsg = $detailErrMsg.' of detail data';
        	}
        }

        if (!empty($headerValidation) || !empty($detailValidation)) {
        	if (!empty($headerValidation) && !empty($detailValidation)) {
        		$erroMsg = $erroMsg.' are empty, you cannot confirm this document.';
        	} else {
        		if (!empty($headerValidation)) {
        			if (sizeof($headerValidation) == 1) {
        				$erroMsg = $erroMsg.' is empty, you cannot confirm this document.';
        			} else {
        				$erroMsg = $erroMsg.' are empty, you cannot confirm this document.';
        			}
        		}

        		if (!empty($detailValidation)) {
        			if (sizeof($detailValidation) == 1) {
        				$erroMsg = $erroMsg.' is empty, you cannot confirm this document.';
        			} else {
        				$erroMsg = $erroMsg.' are empty, you cannot confirm this document.';
        			}
        		}
        	}

        	return ['status' => false, 'message' => $erroMsg];
        }

		return ['status' => true];		
	}

	public static function validateDetailCurrency($details, $docInforArr, $erroMsg)
	{
		foreach ($details as $key => $value) {
			if ((isset($docInforArr['detailTransactionCurrencyID']) && $docInforArr['detailTransactionCurrencyID'] != "") && (is_null($value[$docInforArr['detailTransactionCurrencyID']]) || $value[$docInforArr['detailTransactionCurrencyID']] == 0)) {
				if (!in_array("Transaction Currency", $erroMsg)) {
					array_push($erroMsg, "Transaction Currency");
				}
			}

			if ((isset($docInforArr['detailTransactionER']) && $docInforArr['detailTransactionER'] != "") && (is_null($value[$docInforArr['detailTransactionER']]) || $value[$docInforArr['detailTransactionER']] == 0)) {
				if (!in_array("Transaction Currency Exchange Rate", $erroMsg)) {
					array_push($erroMsg, "Transaction Currency Exchange Rate");
				}
			}

			if ((isset($docInforArr['detailLocalCurrencyID']) && $docInforArr['detailLocalCurrencyID'] != "") && (is_null($value[$docInforArr['detailLocalCurrencyID']]) || $value[$docInforArr['detailLocalCurrencyID']] == 0)) {
				if (!in_array("Local Currency", $erroMsg)) {
					array_push($erroMsg, "Local Currency");
				}
			}

			if ((isset($docInforArr['detailLocalCurrencyER']) && $docInforArr['detailLocalCurrencyER'] != "") && (is_null($value[$docInforArr['detailLocalCurrencyER']]) || $value[$docInforArr['detailLocalCurrencyER']] == 0)) {
				if (!in_array("Local Currency Exchange Rate", $erroMsg)) {
					array_push($erroMsg, "Local Currency Exchange Rate");
				}
			}

			if ((isset($docInforArr['detailReportingCurrencyID']) && $docInforArr['detailReportingCurrencyID'] != "") && (is_null($value[$docInforArr['detailReportingCurrencyID']]) || $value[$docInforArr['detailReportingCurrencyID']] == 0)) {
				if (!in_array("Reporting Currency", $erroMsg)) {
					array_push($erroMsg, "Reporting Currency");
				}
			}

			if ((isset($docInforArr['detailReportingCurrencyER']) && $docInforArr['detailReportingCurrencyER'] != "") && (is_null($value[$docInforArr['detailReportingCurrencyER']]) || $value[$docInforArr['detailReportingCurrencyER']] == 0)) {
				if (!in_array("Reporting Currency Exchange Rate", $erroMsg)) {
					array_push($erroMsg, "Reporting Currency Exchange Rate");
				}
			}
		}

		return $erroMsg;
	}


	public static function validateHeaderCurrency($masterRecord, $docInforArr)
	{
		$erroMsg = [];
		if ((isset($docInforArr['masterTransactionCurrencyID']) && $docInforArr['masterTransactionCurrencyID'] != "") && (is_null($masterRecord[$docInforArr['masterTransactionCurrencyID']]) || $masterRecord[$docInforArr['masterTransactionCurrencyID']] == 0)) {
			array_push($erroMsg, "Transaction Currency");
		}

		if ((isset($docInforArr['masterTransactionER']) && $docInforArr['masterTransactionER'] != "") && (is_null($masterRecord[$docInforArr['masterTransactionER']]) || $masterRecord[$docInforArr['masterTransactionER']] == 0)) {
			array_push($erroMsg, "Transaction Currency Exchange Rate");
		}

		if ((isset($docInforArr['masterLocalCurrencyID']) && $docInforArr['masterLocalCurrencyID'] != "") && (is_null($masterRecord[$docInforArr['masterLocalCurrencyID']]) || $masterRecord[$docInforArr['masterLocalCurrencyID']] == 0)) {
			array_push($erroMsg, "Local Currency");
		}

		if ((isset($docInforArr['masterLocalCurrencyER']) && $docInforArr['masterLocalCurrencyER'] != "") && (is_null($masterRecord[$docInforArr['masterLocalCurrencyER']]) || $masterRecord[$docInforArr['masterLocalCurrencyER']] == 0)) {
			array_push($erroMsg, "Local Currency Exchange Rate");
		}

		if ((isset($docInforArr['masterReportingCurrencyID']) && $docInforArr['masterReportingCurrencyID'] != "") && (is_null($masterRecord[$docInforArr['masterReportingCurrencyID']]) || $masterRecord[$docInforArr['masterReportingCurrencyID']] == 0)) {
			array_push($erroMsg, "Reporting Currency");
		}

		if ((isset($docInforArr['masterReportingCurrencyER']) && $docInforArr['masterReportingCurrencyER'] != "") && (is_null($masterRecord[$docInforArr['masterReportingCurrencyER']]) || $masterRecord[$docInforArr['masterReportingCurrencyER']] == 0)) {
			array_push($erroMsg, "Reporting Currency Exchange Rate");
		}

		return $erroMsg;
	}


	public static function setDocumentInfo($documentSystemID)
	{
		$docInforArr = [
			'masterPrimaryKey' => '',
			'masterTransactionCurrencyID' => '', 
			'masterTransactionER' => '', 
			'masterLocalCurrencyID' => '', 
			'masterLocalCurrencyER' => '', 
			'masterReportingCurrencyID' => '', 
			'masterReportingCurrencyER' => ''
		];
		switch ($documentSystemID) {
            case 11: //supplier invoice
                $docInforArr = [
					'masterPrimaryKey' => 'bookingSuppMasInvAutoID',
					'masterTransactionCurrencyID' => 'supplierTransactionCurrencyID', 
					'masterTransactionER' => 'supplierTransactionCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyReportingCurrencyID', 
					'masterReportingCurrencyER' => 'companyReportingER'
				];

				$docInforArr['detailData'][] = [
													'detailMasterColumnName' => 'bookingSuppMasInvAutoID', 
													'detailModelName' => 'BookInvSuppDet',
													'detailTransactionCurrencyID' => 'supplierTransactionCurrencyID', 
													'detailTransactionER' => 'supplierTransactionCurrencyER', 
													'detailLocalCurrencyID' => 'localCurrencyID', 
													'detailLocalCurrencyER' => 'localCurrencyER', 
													'detailReportingCurrencyID' => 'companyReportingCurrencyID', 
													'detailReportingCurrencyER' => 'companyReportingER'
												];

				$docInforArr['detailData'][] = [
													'detailMasterColumnName' => 'directInvoiceAutoID', 
													'detailModelName' => 'DirectInvoiceDetails',
													'detailTransactionCurrencyID' => 'DIAmountCurrency', 
													'detailTransactionER' => 'DIAmountCurrencyER', 
													'detailLocalCurrencyID' => 'localCurrency', 
													'detailLocalCurrencyER' => 'localCurrencyER', 
													'detailReportingCurrencyID' => 'comRptCurrency', 
													'detailReportingCurrencyER' => 'comRptCurrencyER'
												];
                break;
            case 15: //Debit Note
                $docInforArr = [
					'masterPrimaryKey' => 'debitNoteAutoID',
					'masterTransactionCurrencyID' => 'supplierTransactionCurrencyID', 
					'masterTransactionER' => 'supplierTransactionCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyReportingCurrencyID', 
					'masterReportingCurrencyER' => 'companyReportingER'
				];

				$docInforArr['detailData'][] = [
													'detailMasterColumnName' => 'debitNoteAutoID', 
													'detailModelName' => 'DebitNoteDetails',
													'detailTransactionCurrencyID' => 'debitAmountCurrency', 
													'detailTransactionER' => 'debitAmountCurrencyER', 
													'detailLocalCurrencyID' => 'localCurrency', 
													'detailLocalCurrencyER' => 'localCurrencyER', 
													'detailReportingCurrencyID' => 'comRptCurrency', 
													'detailReportingCurrencyER' => 'comRptCurrencyER'
												];
                break;
            case 4: //Payment Voucher
                $docInforArr = [
					'masterPrimaryKey' => 'PayMasterAutoId',
					'masterTransactionCurrencyID' => 'supplierTransCurrencyID', 
					'masterTransactionER' => 'supplierTransCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyRptCurrencyID', 
					'masterReportingCurrencyER' => 'companyRptCurrencyER'
				];

				$docInforArr['detailData'][] = [
													'detailMasterColumnName' => 'PayMasterAutoId', 
													'detailModelName' => 'PaySupplierInvoiceDetail',
													'detailTransactionCurrencyID' => 'supplierTransCurrencyID', 
													'detailTransactionER' => 'supplierTransER', 
													'detailLocalCurrencyID' => 'localCurrencyID', 
													'detailLocalCurrencyER' => 'localER', 
													'detailReportingCurrencyID' => 'comRptCurrencyID', 
													'detailReportingCurrencyER' => 'comRptER'
												];

				$docInforArr['detailData'][] = [
													'detailMasterColumnName' => 'directPaymentAutoID', 
													'detailModelName' => 'DirectPaymentDetails',
													'detailTransactionCurrencyID' => 'supplierTransCurrencyID', 
													'detailTransactionER' => 'supplierTransER', 
													'detailLocalCurrencyID' => 'localCurrency', 
													'detailLocalCurrencyER' => 'localCurrencyER', 
													'detailReportingCurrencyID' => 'comRptCurrency', 
													'detailReportingCurrencyER' => 'comRptCurrencyER'
												];

				$docInforArr['detailData'][] = [
													'detailMasterColumnName' => 'PayMasterAutoId', 
													'detailModelName' => 'AdvancePaymentDetails',
													'detailTransactionCurrencyID' => 'supplierTransCurrencyID', 
													'detailTransactionER' => 'supplierTransER', 
													'detailLocalCurrencyID' => 'localCurrencyID', 
													'detailLocalCurrencyER' => 'localER', 
													'detailReportingCurrencyID' => 'comRptCurrencyID', 
													'detailReportingCurrencyER' => 'comRptER'
												];
                break;
            case 'payment_matching': //Payment Matching
                $docInforArr = [
					'masterPrimaryKey' => 'matchDocumentMasterAutoID',
					'masterTransactionCurrencyID' => 'supplierTransCurrencyID', 
					'masterTransactionER' => 'supplierTransCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyRptCurrencyID', 
					'masterReportingCurrencyER' => 'companyRptCurrencyER'
				];

				$docInforArr['detailData'][] = [
													'detailMasterColumnName' => 'matchingDocID', 
													'detailModelName' => 'PaySupplierInvoiceDetail',
													'detailTransactionCurrencyID' => 'supplierTransCurrencyID', 
													'detailTransactionER' => 'supplierTransER', 
													'detailLocalCurrencyID' => 'localCurrencyID', 
													'detailLocalCurrencyER' => 'localER', 
													'detailReportingCurrencyID' => 'comRptCurrencyID', 
													'detailReportingCurrencyER' => 'comRptER'
												];
                break;
            case 20: //Customer Invoice
                $docInforArr = [
					'masterPrimaryKey' => 'custInvoiceDirectAutoID',
					'masterTransactionCurrencyID' => 'custTransactionCurrencyID', 
					'masterTransactionER' => 'custTransactionCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyReportingCurrencyID', 
					'masterReportingCurrencyER' => 'companyReportingER'
				];

				$docInforArr['detailData'][] = [
													'detailMasterColumnName' => 'custInvoiceDirectID', 
													'detailModelName' => 'CustomerInvoiceDirectDetail',
													'detailTransactionCurrencyID' => 'invoiceAmountCurrency', 
													'detailTransactionER' => 'invoiceAmountCurrencyER', 
													'detailLocalCurrencyID' => 'localCurrency', 
													'detailLocalCurrencyER' => 'localCurrencyER', 
													'detailReportingCurrencyID' => 'comRptCurrency', 
													'detailReportingCurrencyER' => 'comRptCurrencyER'
												];

				$docInforArr['detailData'][] = [
													'detailMasterColumnName' => 'custInvoiceDirectAutoID', 
													'detailModelName' => 'CustomerInvoiceItemDetails',
													'detailTransactionCurrencyID' => 'sellingCurrencyID', 
													'detailTransactionER' => 'sellingCurrencyER', 
													'detailLocalCurrencyID' => 'localCurrencyID', 
													'detailLocalCurrencyER' => 'localCurrencyER', 
													'detailReportingCurrencyID' => 'reportingCurrencyID', 
													'detailReportingCurrencyER' => 'reportingCurrencyER'
												];
                break;
            case 19: //Credit Note
                $docInforArr = [
					'masterPrimaryKey' => 'creditNoteAutoID',
					'masterTransactionCurrencyID' => 'customerCurrencyID', 
					'masterTransactionER' => 'customerCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyReportingCurrencyID', 
					'masterReportingCurrencyER' => 'companyReportingER'
				];

				$docInforArr['detailData'][] = [
													'detailMasterColumnName' => 'creditNoteAutoID', 
													'detailModelName' => 'CreditNoteDetails',
													'detailTransactionCurrencyID' => 'creditAmountCurrency', 
													'detailTransactionER' => 'creditAmountCurrencyER', 
													'detailLocalCurrencyID' => 'localCurrency', 
													'detailLocalCurrencyER' => 'localCurrencyER', 
													'detailReportingCurrencyID' => 'comRptCurrency', 
													'detailReportingCurrencyER' => 'comRptCurrencyER'
												];
                break;
            case 21: //Customer Reciept
                $docInforArr = [
					'masterPrimaryKey' => 'custReceivePaymentAutoID',
					'masterTransactionCurrencyID' => 'custTransactionCurrencyID', 
					'masterTransactionER' => 'custTransactionCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyRptCurrencyID', 
					'masterReportingCurrencyER' => 'companyRptCurrencyER'
				];

				$docInforArr['detailData'][] = [
													'detailMasterColumnName' => 'custReceivePaymentAutoID', 
													'detailModelName' => 'CustomerReceivePaymentDetail',
													'detailTransactionCurrencyID' => 'custTransactionCurrencyID', 
													'detailTransactionER' => 'custTransactionCurrencyER', 
													'detailLocalCurrencyID' => 'localCurrencyID', 
													'detailLocalCurrencyER' => 'localCurrencyER', 
													'detailReportingCurrencyID' => 'companyReportingCurrencyID', 
													'detailReportingCurrencyER' => 'companyReportingER'
												];

				$docInforArr['detailData'][] = [
													'detailMasterColumnName' => 'directReceiptAutoID', 
													'detailModelName' => 'DirectReceiptDetail',
													'detailTransactionCurrencyID' => 'DRAmountCurrency', 
													'detailTransactionER' => 'DDRAmountCurrencyER', 
													'detailLocalCurrencyID' => 'localCurrency', 
													'detailLocalCurrencyER' => 'localCurrencyER', 
													'detailReportingCurrencyID' => 'comRptCurrency', 
													'detailReportingCurrencyER' => 'comRptCurrencyER'
												];
                break;
            case 'receipt_matching': //Receipt Matching
                $docInforArr = [
					'masterPrimaryKey' => 'matchDocumentMasterAutoID',
					'masterTransactionCurrencyID' => 'supplierTransCurrencyID', 
					'masterTransactionER' => 'supplierTransCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyRptCurrencyID', 
					'masterReportingCurrencyER' => 'companyRptCurrencyER'
				];

				$docInforArr['detailData'][] = [
													'detailMasterColumnName' => 'matchingDocID', 
													'detailModelName' => 'CustomerReceivePaymentDetail',
													'detailTransactionCurrencyID' => 'custTransactionCurrencyID', 
													'detailTransactionER' => 'custTransactionCurrencyER', 
													'detailLocalCurrencyID' => 'localCurrencyID', 
													'detailLocalCurrencyER' => 'localCurrencyER', 
													'detailReportingCurrencyID' => 'companyReportingCurrencyID', 
													'detailReportingCurrencyER' => 'companyReportingER'
												];
                break;
			default:
				# code...
				break;
		}

		return $docInforArr;
	}

	public static function convertToLocalCurrencyDecimal($companyId, $amount)
    {
        $dPlace = 2;
        $local_currency_id = self::companyLocalCurrency($companyId);

        if($local_currency_id){
            $dPlace = Helper::getCurrencyDecimalPlace($local_currency_id);
        }

        return number_format($amount, $dPlace);
    }

    public static function convertToRptCurrencyDecimal($companyId, $amount)
    {
        $dPlace = 2;
        $rpt_currency_id = self::companyRptCurrency($companyId);

        if($rpt_currency_id){
            $dPlace = Helper::getCurrencyDecimalPlace($rpt_currency_id);
        }

        return number_format($amount, $dPlace);
    }

	public static function companyLocalCurrency($companyId){
	    return Company::find($companyId)->localCurrencyID;
    }

    public static function companyRptCurrency($companyId){
        return Company::find($companyId)->reportingCurrency;
    }
}
