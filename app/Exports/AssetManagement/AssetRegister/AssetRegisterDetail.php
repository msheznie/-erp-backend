<?php

namespace App\Exports\AssetManagement\AssetRegister;

use App\helper\Helper;

class AssetRegisterDetail
{

    public $costGL;
    public $accDepGL;
    public $type;
    public $segment;
    public $faCode;
    public $groupedYN;
    public $serialNumber;
    public $assetDescription;
    public $category;
    public $depPercentage;
    public $dateAcquired;
    public $depStartDate;
    public $localAmountUnitCost;
    public $localAmountAccDep;
    public $localAmountNetValue;
    public $rptAmountUnitCost;
    public $rptAmountAccDep;
    public $rptAmountNetValue;

    public function getHeader() :Array {
        return [
            trans('custom.cost_gl'),
            trans('custom.acc_dep_gl'),
            trans('custom.e_type'),
            trans('custom.e_segment'),
            trans('custom.fa_code'),
            trans('custom.grouped_yn'),
            trans('custom.serial_number'),
            trans('custom.asset_description'),
            trans('custom.category'),
            trans('custom.dep_percentage'),
            trans('custom.date_acquired'),
            trans('custom.dep_start_date'),
            '',
            trans('custom.local_amount'),
            '',
            '',
            trans('custom.rpt_amount'),
            ''
        ];
    }

    public function getSubHeader():Array {
        return [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            trans('custom.unit_cost'),
            trans('custom.accdep_amount'),
            trans('custom.net_book_value'),
            trans('custom.unit_cost'),
            trans('custom.accdep_amount'),
            trans('custom.net_book_value'),
        ];
    }

    /**
     * @param mixed $costGL
     */
    public function setCostGL($costGL): void
    {
        $this->costGL = $costGL;
    }

    /**
     * @param mixed $accDepGL
     */
    public function setAccDepGL($accDepGL): void
    {
        $this->accDepGL = $accDepGL;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @param mixed $segment
     */
    public function setSegment($segment): void
    {
        $this->segment = $segment;
    }

    /**
     * @param mixed $faCode
     */
    public function setFaCode($faCode): void
    {
        $this->faCode = $faCode;
    }

    /**
     * @param mixed $groupedYN
     */
    public function setGroupedYN($groupedYN): void
    {
        $this->groupedYN = $groupedYN;
    }

    /**
     * @param mixed $serialNumber
     */
    public function setSerialNumber($serialNumber): void
    {
        $this->serialNumber = $serialNumber;
    }

    /**
     * @param mixed $assetDescription
     */
    public function setAssetDescription($assetDescription): void
    {
        $this->assetDescription = $assetDescription;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @param mixed $depPercentage
     */
    public function setDepPercentage($depPercentage): void
    {
        $this->depPercentage = $depPercentage;
    }

    /**
     * @param mixed $dateAcquired
     */
    public function setDateAcquired($dateAcquired): void
    {
        $this->dateAcquired = ($dateAcquired) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($dateAcquired)) : null;
    }

    /**
     * @param mixed $depStartDate
     */
    public function setDepStartDate($depStartDate): void
    {
        $this->depStartDate = ($depStartDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($depStartDate)) : null;
    }

    /**
     * @param mixed $localAmountUnitCost
     */
    public function setLocalAmountUnitCost($localAmountUnitCost): void
    {
        $this->localAmountUnitCost = $localAmountUnitCost;
    }

    /**
     * @param mixed $localAmountAccDep
     */
    public function setLocalAmountAccDep($localAmountAccDep): void
    {
        $this->localAmountAccDep = $localAmountAccDep;
    }

    /**
     * @param mixed $localAmountNetValue
     */
    public function setLocalAmountNetValue($localAmountNetValue): void
    {
        $this->localAmountNetValue = $localAmountNetValue;
    }

    /**
     * @param mixed $rptAmountUnitCost
     */
    public function setRptAmountUnitCost($rptAmountUnitCost): void
    {
        $this->rptAmountUnitCost = $rptAmountUnitCost;
    }

    /**
     * @param mixed $rptAmountAccDep
     */
    public function setRptAmountAccDep($rptAmountAccDep): void
    {
        $this->rptAmountAccDep = $rptAmountAccDep;
    }

    /**
     * @param mixed $rptAmountNetValue
     */
    public function setRptAmountNetValue($rptAmountNetValue): void
    {
        $this->rptAmountNetValue = $rptAmountNetValue;
    }



}
