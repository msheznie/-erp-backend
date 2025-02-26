<?php

namespace App\Exports\B2B\VendorFile;

use App\Validations\B2B\VendorFile\Detail;
use App\Validations\B2B\VendorFile\Header;

class VendorFile
{

    public $headerData;
    public $detailsData;
    public $footerData;

    public $headerErrors;
    public $detailsDataErros;

    /**
     * @param mixed $headerData
     */
    public function setHeaderData($headerData): void
    {
        $this->headerData = $headerData;
        $this->validateHeaderData();
    }

    /**
     * @param mixed $detailsData
     */
    public function setDetailsData($detailsData): void
    {
        $this->detailsData = $detailsData;
        $this->validateDetails();
    }

    /**
     * @param mixed $footerData
     */
    public function setFooterData($footerData): void
    {
        $this->footerData = $footerData;
    }

    public function header() : array
    {
        return [
           'title' => ['Section Index', 'CompanyCr', 'Debit Account No', 'Transfer Method', 'DebitMode', 'Debit Narrative', 'RequestDate', 'BatchReference'],
            'data' => $this->headerData
        ];
    }


    public function detail() : array
    {
        return [
            'title' => ['Section Index', 'Transfer Method', 'Credit Amount', 'Credit Currency', 'Exchange Rate', 'DealReferNo', 'ValueDate', 'Debit Account No', 'Credit Account No', 'TransactionReference', 'Debit Narrative', 'Debit Narrative 2', 'Credit Narrative', 'Payment Details 1', 'Payment Details 2', 'Payment Details 3', 'Payment Details 4', 'Beneficiary Name', 'Beneficiary Address 1', 'Beneficiary Address 2', 'Institution Name Address 1', 'Institution Name Address 2', 'Institution Name Address 3', 'Institution Name Address 4', 'Swift', 'Intermediary Account', 'Intermediary Swift', 'Intermediary Name', 'Intermediary Address 1', 'Intermediary Address 2', 'Intermediary Address 3', 'Charges Type', 'Sort Code of the beneficiary bank', 'IFSC', 'Fedwire', 'Email', 'Dispatch Mode', 'Transactor Code', 'Supporting Document Name'],
            'data' => $this->detailsData
        ];
    }

    public function footer() : array
    {
        return [
            'title' => ['Section Index', 'Num Of Records', 'Total Amount'],
            'data' => $this->footerData
        ];
    }

    private function validateDetails()
    {
        $detailValidaiton = new Detail($this->detailsData);
        $this->detailsDataErros = $detailValidaiton->validaitons;
    }

    private function validateHeaderData()
    {
        $header = new Header($this->headerData);
        $this->headerErrors = $header->validaitons;
    }
}
