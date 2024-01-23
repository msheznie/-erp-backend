<?php

namespace App\Exports\GeneralLedger\VAT;

use App\helper\Helper;

class InputOutputVatReport
{

    public $documentType;
    public $documentCode;
    public $referenceNo;
    public $documentDate;
    public $partyName;
    public $country;
    public $vatIn;
    public $apporvedBy;
    public $documentTotalAmount;
    public $documentVatAmount;
    public $vatMainCategory;
    public $vatType;
    public $isClaimed;
    public $header;
    public $cloumn_format;

    /**
     * @return mixed
     */
    public function getCloumnFormat()
    {
        return [
            'D' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    /**
     * @return mixed
     */
    public function getHeader()
    {
        return  [
            'Document Type',
            'Document Code',
            'Rerfernce No',
            'Document Date',
            'Party Name',
            'Country',
            'Vat IN',
            'Approved By',
            'Document Total Amount',
            'Document VAT Amount',
            'VAT  Main Category',
            'VAT Type',
            'Is Claimed'
        ];
    }

    /**
     * @return mixed
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    /**
     * @param mixed $documentType
     */
    public function setDocumentType($documentType): void
    {
        $this->documentType = $documentType;
    }

    /**
     * @return mixed
     */
    public function getDocumentCode()
    {
        return $this->documentCode;
    }

    /**
     * @param mixed $documentCode
     */
    public function setDocumentCode($documentCode): void
    {
        $this->documentCode = $documentCode;
    }

    /**
     * @return mixed
     */
    public function getReferenceNo()
    {
        return $this->referenceNo;
    }

    /**
     * @param mixed $referenceNo
     */
    public function setReferenceNo($referenceNo): void
    {
        $this->referenceNo = $referenceNo;
    }

    /**
     * @return mixed
     */
    public function getDocumentDate()
    {
        return $this->documentDate;
    }

    /**
     * @param mixed $documentDate
     */
    public function setDocumentDate($documentDate,$isDate): void
    {

        if($isDate) {
            $this->documentDate = ($documentDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($documentDate)) : null;
        }else {
            $this->documentDate = $documentDate;
        }


    }

    /**
     * @return mixed
     */
    public function getPartyName()
    {
        return $this->partyName;
    }

    /**
     * @param mixed $partyName
     */
    public function setPartyName($partyName): void
    {
        $this->partyName = $partyName;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country): void
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getVatIn()
    {
        return $this->vatIn;
    }

    /**
     * @param mixed $vatIn
     */
    public function setVatIn($vatIn): void
    {
        $this->vatIn = $vatIn;
    }

    /**
     * @return mixed
     */
    public function getApporvedBy()
    {
        return $this->apporvedBy;
    }

    /**
     * @param mixed $apporvedBy
     */
    public function setApporvedBy($apporvedBy): void
    {
        $this->apporvedBy = $apporvedBy;
    }

    /**
     * @return mixed
     */
    public function getDocumentTotalAmount()
    {
        return $this->documentTotalAmount;
    }

    /**
     * @param mixed $documentTotalAmount
     */
    public function setDocumentTotalAmount($documentTotalAmount): void
    {
        $this->documentTotalAmount = $documentTotalAmount;
    }

    /**
     * @return mixed
     */
    public function getDocumentVatAmount()
    {
        return $this->documentVatAmount;
    }

    /**
     * @param mixed $documentVatAmount
     */
    public function setDocumentVatAmount($documentVatAmount): void
    {
        $this->documentVatAmount = $documentVatAmount;
    }

    /**
     * @return mixed
     */
    public function getVatMainCategory()
    {
        return $this->vatMainCategory;
    }

    /**
     * @param mixed $vatMainCategory
     */
    public function setVatMainCategory($vatMainCategory): void
    {
        $this->vatMainCategory = $vatMainCategory;
    }

    /**
     * @return mixed
     */
    public function getVatType()
    {
        return $this->vatType;
    }

    /**
     * @param mixed $vatType
     */
    public function setVatType($vatType): void
    {
        $this->vatType = $vatType;
    }

    /**
     * @return mixed
     */
    public function getIsClaimed()
    {
        return $this->isClaimed;
    }

    /**
     * @param mixed $isClaimed
     */
    public function setIsClaimed($isClaimed): void
    {
        $this->isClaimed = $isClaimed;
    }

    public function getVatInputOutputReport() : array
    {
        return $this;
    }


}
