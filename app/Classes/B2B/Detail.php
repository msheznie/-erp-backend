<?php

namespace App\Classes\B2B;

use App\Models\CurrencyMaster;
use Carbon\Carbon;

class Detail
{
    public $section_index;
    public $transfer_method;
    public $credit_amount;
    public $credit_currency;
    public $exchange_rate;
    public $deal_ref_no;
    public $value_date;
    public $debit_account_no;
    public $credit_account_no;
    public $transaction_reference;
    public $debit_narrative;
    public $debit_narrative2;
    public $credit_narrative;
    public $payment_details_1;
    public $payment_details_2;
    public $payment_details_3;
    public $payment_details_4;
    public $beneficiary_name;
    public $beneficiary_address1;
    public $beneficiary_address2;
    public $institution_name_address_1;
    public $institution_name_address_2;
    public $institution_name_address_3;
    public $institution_name_address_4;
    public $swift;
    public $intermediary_account;
    public $intermediary_swift;
    public $intrmediary_name;
    public $intermediary_address1;
    public $intermediary_address2;
    public $intermediary_address3;
    public $charges_type;
    public $sort_code_beneficiary_bank;
    public $IFSC;
    public $fedwire;
    public $email;
    public $dispatch_mode;
    public $transactor_code;
    public $supporting_document_name;

    public $payment_voucher_code;

    /**
     * @param mixed $payment_voucher_code
     */
    public function setPaymentVoucherCode($payment_voucher_code): void
    {
        $this->payment_voucher_code = $payment_voucher_code;
    }
    // Setter methods
    public function setSectionIndex($value) { $this->section_index = $value; }
    public function setTransferMethod($value) { $this->transfer_method = $value; }
    public function setCreditAmount($value,$bankCurrency) { $this->credit_amount = round($value,CurrencyMaster::find($bankCurrency)->DecimalPlaces); }
    public function setCreditCurrency($value) { $this->credit_currency = $this->getCurrencyCode($value); }
    public function setExchangeRate($value) { $this->exchange_rate = $value; }
    public function setDealRefNo($value) { $this->deal_ref_no = $value; }
    public function setValueDate($value) { $this->value_date = Carbon::parse($value)->format('d/m/Y'); }
    public function setDebitAccountNo($value) { $this->debit_account_no = $value; }
    public function setCreditAccountNo($value) { $this->credit_account_no = $value; }
    public function setTransactionReference($value) { $this->transaction_reference = $value; }
    public function setDebitNarrative($value) { $this->debit_narrative = $value; }
    public function setDebitNarrative2($value) { $this->debit_narrative2 = $value; }
    public function setCreditNarrative($value) { $this->credit_narrative = $value; }
    public function setPaymentDetails1($value) { $this->payment_details_1 = $value; }
    public function setPaymentDetails2($value) { $this->payment_details_2 = $value; }
    public function setPaymentDetails3($value) { $this->payment_details_3 = $value; }
    public function setPaymentDetails4($value) { $this->payment_details_4 = $value; }
    public function setBeneficiaryName($value) { $this->beneficiary_name = $value; }
    public function setBeneficiaryAddress1($value) { $this->beneficiary_address1 = $value; }
    public function setBeneficiaryAddress2($value) { $this->beneficiary_address2 = $value; }
    public function setInstitutionNameAddress1($value) { $this->institution_name_address_1 = $value; }
    public function setInstitutionNameAddress2($value) { $this->institution_name_address_2 = $value; }
    public function setInstitutionNameAddress3($value) { $this->institution_name_address_3 = $value; }
    public function setInstitutionNameAddress4($value) { $this->institution_name_address_4 = $value; }
    public function setSwift($value) { $this->swift = $value; }
    public function setIntermediaryAccount($value) { $this->intermediary_account = $value; }
    public function setIntermediarySwift($value) { $this->intermediary_swift = $value; }
    public function setIntermediaryName($value) { $this->intrmediary_name = $value; }
    public function setIntermediaryAddress1($value) { $this->intermediary_address1 = $value; }
    public function setIntermediaryAddress2($value) { $this->intermediary_address2 = $value; }
    public function setIntermediaryAddress3($value) { $this->intermediary_address3 = $value; }
    public function setChargesType($value) { $this->charges_type = $value; }
    public function setSortCodeBeneficiaryBank($value) { $this->sort_code_beneficiary_bank = $value; }
    public function setIFSC($value) { $this->IFSC = $value; }
    public function setFedwire($value) { $this->fedwire = $value; }
    public function setEmail($value) { $this->email = $value; }
    public function setDispatchMode($value) { $this->dispatch_mode = $value; }
    public function setTransactorCode($value) { $this->transactor_code = $value; }
    public function setSupportingDocumentName($value) { $this->supporting_document_name = $value; }


    private function getCurrencyCode($currencyID)
    {
        return CurrencyMaster::find($currencyID,['CurrencyCode'])->CurrencyCode ?? null;
    }
}
