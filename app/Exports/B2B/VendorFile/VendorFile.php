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
        $this->processHeaderData();
    }

    /**
     * @param mixed $detailsData
     */
    public function setDetailsData($detailsData): void
    {
        $this->detailsData = $detailsData;
        $this->validateDetails();
        $this->processDetailsData();
    }

    /**
     * @param mixed $footerData
     */
    public function setFooterData($footerData): void
    {
        $this->footerData = $footerData;
        $this->processFooterData();
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

    private function processDetailsData()
    {
        // Process detailsData to remove special characters from strings
        $processedDetailsData = [];
        
        // Get field indices for email and amount fields
        $detailTitles = ['Section Index', 'Transfer Method', 'Credit Amount', 'Credit Currency', 'Exchange Rate', 'DealReferNo', 'ValueDate', 'Debit Account No', 'Credit Account No', 'TransactionReference', 'Debit Narrative', 'Debit Narrative 2', 'Credit Narrative', 'Payment Details 1', 'Payment Details 2', 'Payment Details 3', 'Payment Details 4', 'Beneficiary Name', 'Beneficiary Address 1', 'Beneficiary Address 2', 'Institution Name Address 1', 'Institution Name Address 2', 'Institution Name Address 3', 'Institution Name Address 4', 'Swift', 'Intermediary Account', 'Intermediary Swift', 'Intermediary Name', 'Intermediary Address 1', 'Intermediary Address 2', 'Intermediary Address 3', 'Charges Type', 'Sort Code of the beneficiary bank', 'IFSC', 'Fedwire', 'Email', 'Dispatch Mode', 'Transactor Code', 'Supporting Document Name'];
        $emailFieldIndex = array_search('Email', $detailTitles);
        $creditAmountIndex = array_search('Credit Amount', $detailTitles);
        $exchangeRateIndex = array_search('Exchange Rate', $detailTitles);
        
        foreach ($this->detailsData as $rowIndex => $row) {
            $processedRow = [];
            foreach ($row as $columnIndex => $value) {
                if (is_string($value)) {
                    // Skip special character removal for email and amount fields
                    if ($columnIndex == $emailFieldIndex || $columnIndex == $creditAmountIndex || $columnIndex == $exchangeRateIndex) {
                        // Keep email addresses and amounts as they are
                        $processedRow[$columnIndex] = $value;
                    } else {
                        // Remove special characters, keeping only alphanumeric characters and spaces
                        $processedRow[$columnIndex] = preg_replace('/[^a-zA-Z0-9\s]/', '', $value);
                    }
                } else {
                    // Keep non-string values as they are
                    $processedRow[$columnIndex] = $value;
                }
            }
            $processedDetailsData[$rowIndex] = $processedRow;
        }
        $this->detailsData = $processedDetailsData;
    }

    private function processFooterData()
    {
        // Process footerData to remove special characters from strings
        $processedFooterData = [];
        
        // Get field indices for amount fields
        $footerTitles = ['Section Index', 'Num Of Records', 'Total Amount'];
        $totalAmountIndex = array_search('Total Amount', $footerTitles);
        
        foreach ($this->footerData as $rowIndex => $row) {
            $processedRow = [];
            foreach ($row as $columnIndex => $value) {
                if (is_string($value)) {
                    // Skip special character removal for amount fields
                    if ($columnIndex == $totalAmountIndex) {
                        // Keep amounts as they are
                        $processedRow[$columnIndex] = $value;
                    } else {
                        // Remove special characters, keeping only alphanumeric characters and spaces
                        $processedRow[$columnIndex] = preg_replace('/[^a-zA-Z0-9\s]/', '', $value);
                    }
                } else {
                    // Keep non-string values as they are
                    $processedRow[$columnIndex] = $value;
                }
            }
            $processedFooterData[$rowIndex] = $processedRow;
        }
        
        $this->footerData = $processedFooterData;
    }

    private function processHeaderData()
    {
        // Process headerData to remove special characters from strings
        $processedHeaderData = [];
        
        foreach ($this->headerData as $rowIndex => $row) {
            $processedRow = [];
            foreach ($row as $columnIndex => $value) {
                if (is_string($value)) {
                    // Remove special characters, keeping only alphanumeric characters and spaces
                    $processedRow[$columnIndex] = preg_replace('/[^a-zA-Z0-9\s]/', '', $value);
                } else {
                    // Keep non-string values as they are
                    $processedRow[$columnIndex] = $value;
                }
            }
            $processedHeaderData[$rowIndex] = $processedRow;
        }
        
        $this->headerData = $processedHeaderData;
    }

    private function validateHeaderData()
    {
        $header = new Header($this->headerData);
        $this->headerErrors = $header->validaitons;
    }
}
