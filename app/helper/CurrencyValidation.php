<?php

namespace App\helper;
use Carbon\Carbon;

class CurrencyValidation
{
	public static function validateCurrency($documentSystemID, $masterRecord)
	{
		$docInforArr = self::setDocumentInfo($documentSystemID);
		
		$namespacedModel = 'App\Models\\' . $docInforArr["detailModelName"]; // Model name
        $details = $namespacedModel::where($docInforArr["detailMasterColumnName"], $masterRecord[$docInforArr["masterPrimaryKey"]])
        							 ->get();

        if (sizeof($details) > 0) {
        	$details = $details->toArray();
        }

        $erroMsg = "";
        $headerValidation = self::validateHeaderCurrency($masterRecord, $docInforArr);

        if (!empty($headerValidation)) {
        	$erroMsg = implode(', ', $headerValidation);

        	$erroMsg = $erroMsg.' of header data';
        }
        
        $detailValidation = self::validateDetailCurrency($details, $docInforArr);
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

	public static function validateDetailCurrency($details, $docInforArr)
	{
		$erroMsg = [];

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
		if ((isset($docInforArr['detailTransactionCurrencyID']) && $docInforArr['detailTransactionCurrencyID'] != "") && (is_null($masterRecord[$docInforArr['masterTransactionCurrencyID']]) || $masterRecord[$docInforArr['masterTransactionCurrencyID']] == 0)) {
			array_push($erroMsg, "Transaction Currency");
		}

		if ((isset($docInforArr['detailTransactionER']) && $docInforArr['detailTransactionER'] != "") && (is_null($masterRecord[$docInforArr['masterTransactionER']]) || $masterRecord[$docInforArr['masterTransactionER']] == 0)) {
			array_push($erroMsg, "Transaction Currency Exchange Rate");
		}

		if ((isset($docInforArr['detailLocalCurrencyID']) && $docInforArr['detailLocalCurrencyID'] != "") && (is_null($masterRecord[$docInforArr['masterLocalCurrencyID']]) || $masterRecord[$docInforArr['masterLocalCurrencyID']] == 0)) {
			array_push($erroMsg, "Local Currency");
		}

		if ((isset($docInforArr['detailLocalCurrencyER']) && $docInforArr['detailLocalCurrencyER'] != "") && (is_null($masterRecord[$docInforArr['masterLocalCurrencyER']]) || $masterRecord[$docInforArr['masterLocalCurrencyER']] == 0)) {
			array_push($erroMsg, "Local Currency Exchange Rate");
		}

		if ((isset($docInforArr['detailReportingCurrencyID']) && $docInforArr['detailReportingCurrencyID'] != "") && (is_null($masterRecord[$docInforArr['masterReportingCurrencyID']]) || $masterRecord[$docInforArr['masterReportingCurrencyID']] == 0)) {
			array_push($erroMsg, "Reporting Currency");
		}

		if ((isset($docInforArr['detailReportingCurrencyER']) && $docInforArr['detailReportingCurrencyER'] != "") && (is_null($masterRecord[$docInforArr['masterReportingCurrencyER']]) || $masterRecord[$docInforArr['masterReportingCurrencyER']] == 0)) {
			array_push($erroMsg, "Reporting Currency Exchange Rate");
		}

		return $erroMsg;
	}


	public static function setDocumentInfo($documentSystemID)
	{
		$docInforArr = [
			'detailModelName' => '',
			'masterPrimaryKey' => '',
			'detailMasterColumnName' => '', 
			'masterTransactionCurrencyID' => '', 
			'masterTransactionER' => '', 
			'masterLocalCurrencyID' => '', 
			'masterLocalCurrencyER' => '', 
			'masterReportingCurrencyID' => '', 
			'masterReportingCurrencyER' => '',
			'detailTransactionCurrencyID' => '', 
			'detailTransactionER' => '', 
			'detailLocalCurrencyID' => '', 
			'detailLocalCurrencyER' => '', 
			'detailReportingCurrencyID' => '', 
			'detailReportingCurrencyER' => ''
		];
		switch ($documentSystemID) {
            case 11: //supplier invoice
                $docInforArr = [
					'detailModelName' => 'BookInvSuppDet',
					'masterPrimaryKey' => 'bookingSuppMasInvAutoID',
					'detailMasterColumnName' => 'bookingSuppMasInvAutoID', 
					'masterTransactionCurrencyID' => 'supplierTransactionCurrencyID', 
					'masterTransactionER' => 'supplierTransactionCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyReportingCurrencyID', 
					'masterReportingCurrencyER' => 'companyReportingER',
					'detailTransactionCurrencyID' => 'supplierTransactionCurrencyID', 
					'detailTransactionER' => 'supplierTransactionCurrencyER', 
					'detailLocalCurrencyID' => 'localCurrencyID', 
					'detailLocalCurrencyER' => 'localCurrencyER', 
					'detailReportingCurrencyID' => 'companyReportingCurrencyID', 
					'detailReportingCurrencyER' => 'companyReportingER'
				];
                break;
            case 15: //Debit Note
                $docInforArr = [
					'detailModelName' => 'DebitNoteDetails',
					'masterPrimaryKey' => 'debitNoteAutoID',
					'detailMasterColumnName' => 'debitNoteAutoID', 
					'masterTransactionCurrencyID' => 'supplierTransactionCurrencyID', 
					'masterTransactionER' => 'supplierTransactionCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyReportingCurrencyID', 
					'masterReportingCurrencyER' => 'companyReportingER',
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
					'detailModelName' => 'PaySupplierInvoiceDetail',
					'masterPrimaryKey' => 'PayMasterAutoId',
					'detailMasterColumnName' => 'PayMasterAutoId', 
					'masterTransactionCurrencyID' => 'supplierTransCurrencyID', 
					'masterTransactionER' => 'supplierTransCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyRptCurrencyID', 
					'masterReportingCurrencyER' => 'companyRptCurrencyER',
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
					'detailModelName' => 'PaySupplierInvoiceDetail',
					'masterPrimaryKey' => 'matchDocumentMasterAutoID',
					'detailMasterColumnName' => 'matchingDocID', 
					'masterTransactionCurrencyID' => 'supplierTransCurrencyID', 
					'masterTransactionER' => 'supplierTransCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyRptCurrencyID', 
					'masterReportingCurrencyER' => 'companyRptCurrencyER',
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
					'detailModelName' => 'CustomerInvoiceDirectDetail',
					'masterPrimaryKey' => 'custInvoiceDirectAutoID',
					'detailMasterColumnName' => 'custInvoiceDirectID', 
					'masterTransactionCurrencyID' => 'custTransactionCurrencyID', 
					'masterTransactionER' => 'custTransactionCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyReportingCurrencyID', 
					'masterReportingCurrencyER' => 'companyReportingER',
					'detailTransactionCurrencyID' => 'invoiceAmountCurrency', 
					'detailTransactionER' => 'invoiceAmountCurrencyER', 
					'detailLocalCurrencyID' => 'localCurrency', 
					'detailLocalCurrencyER' => 'localCurrencyER', 
					'detailReportingCurrencyID' => 'comRptCurrency', 
					'detailReportingCurrencyER' => 'comRptCurrencyER'
				];
                break;
            case 19: //Credit Note
                $docInforArr = [
					'detailModelName' => 'CreditNoteDetails',
					'masterPrimaryKey' => 'creditNoteAutoID',
					'detailMasterColumnName' => 'creditNoteAutoID', 
					'masterTransactionCurrencyID' => 'customerCurrencyID', 
					'masterTransactionER' => 'customerCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyReportingCurrencyID', 
					'masterReportingCurrencyER' => 'companyReportingER',
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
					'detailModelName' => 'CustomerReceivePaymentDetail',
					'masterPrimaryKey' => 'custReceivePaymentAutoID',
					'detailMasterColumnName' => 'custReceivePaymentAutoID', 
					'masterTransactionCurrencyID' => 'custTransactionCurrencyID', 
					'masterTransactionER' => 'custTransactionCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyRptCurrencyID', 
					'masterReportingCurrencyER' => 'companyRptCurrencyER',
					'detailTransactionCurrencyID' => 'custTransactionCurrencyID', 
					'detailTransactionER' => 'custTransactionCurrencyER', 
					'detailLocalCurrencyID' => 'localCurrencyID', 
					'detailLocalCurrencyER' => 'localCurrencyER', 
					'detailReportingCurrencyID' => 'companyReportingCurrencyID', 
					'detailReportingCurrencyER' => 'companyReportingER'
				];
                break;
            case 'receipt_matching': //Receipt Matching
                $docInforArr = [
					'detailModelName' => 'CustomerReceivePaymentDetail',
					'masterPrimaryKey' => 'matchDocumentMasterAutoID',
					'detailMasterColumnName' => 'matchingDocID', 
					'masterTransactionCurrencyID' => 'supplierTransCurrencyID', 
					'masterTransactionER' => 'supplierTransCurrencyER', 
					'masterLocalCurrencyID' => 'localCurrencyID', 
					'masterLocalCurrencyER' => 'localCurrencyER', 
					'masterReportingCurrencyID' => 'companyRptCurrencyID', 
					'masterReportingCurrencyER' => 'companyRptCurrencyER',
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
}