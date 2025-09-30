<?php

namespace App\Exports\AssetManagement\AssetRegister;

use App\helper\Helper;
use Carbon\Carbon;

class AssetRegisterDetail2
{

    public $glCode;
    public $category;
    public $faCode;
    public $groupedFaCode;
    public $postingDateOfFA;
    public $depStartDate;
    public $depPercentage;
    public $serviceLine;
    public $grvDate;
    public $grvNumber;
    public $supplierName;
    public $openingCost;
    public $additionCost;
    public $disposalCost;
    public $closingCost;
    public $openingDep;
    public $chargeDuringTheYear;
    public $chargeOnDisposal;
    public $closingDep;
    public $nbv;
    public $jan;
    public $feb;
    public $mar;
    public $apr;
    public $may;
    public $jun;
    public $jul;
    public $aug;
    public $sep;
    public $oct;
    public $nov;
    public $dec;

    public function getHeader():Array {
       return [
            trans('custom.gl_code'),
            trans('custom.category'),
            trans('custom.fa_code'),
            trans('custom.grouped_fa_code'),
            trans('custom.posting_date_of_fa'),
            trans('custom.dep_start_date'),
            trans('custom.dep_percentage'),
            trans('custom.service_line'),
            trans('custom.grv_date'),
            trans('custom.grv_number'),
            trans('custom.supplier_name'),
            trans('custom.opening_cost'),
            trans('custom.addition_cost'),
            trans('custom.disposal_cost'),
            trans('custom.closing_cost'),
            trans('custom.opening_dep'),
            trans('custom.charge_during_the_year'),
            trans('custom.charge_on_disposal'),
            trans('custom.closing_dep'),
            trans('custom.nbv'),
            trans('custom.jan'),
            trans('custom.feb'),
            trans('custom.mar'),
            trans('custom.apr'),
            trans('custom.may'),
            trans('custom.jun'),
            trans('custom.jul'),
            trans('custom.aug'),
            trans('custom.sep'),
            trans('custom.oct'),
            trans('custom.nov'),
            trans('custom.dec'),
        ];

    }

    /**
     * @param mixed $glCode
     */
    public function setGlCode($glCode): void
    {
        $this->glCode = $glCode;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @param mixed $faCode
     */
    public function setFaCode($faCode): void
    {
        $this->faCode = $faCode;
    }

    /**
     * @param mixed $groupedFaCode
     */
    public function setGroupedFaCode($groupedFaCode): void
    {
        $this->groupedFaCode = $groupedFaCode;
    }

    /**
     * @param mixed $postingDateOfFA
     */
    public function setPostingDateOfFA($postingDateOfFA): void
    {
        $this->postingDateOfFA = ($postingDateOfFA) ? $postingDateOfFA : null;
    }

    /**
     * @param mixed $depStartDate
     */
    public function setDepStartDate($depStartDate): void
    {
        $this->depStartDate = $depStartDate;
    }

    /**
     * @param mixed $depPercentage
     */
    public function setDepPercentage($depPercentage): void
    {
        $this->depPercentage = $depPercentage;
    }

    /**
     * @param mixed $serviceLine
     */
    public function setServiceLine($serviceLine): void
    {
        $this->serviceLine = $serviceLine;
    }

    /**
     * @param mixed $grvDate
     */
    public function setGrvDate($grvDate): void
    {
        $this->grvDate = $grvDate;
    }

    /**
     * @param mixed $grvNumber
     */
    public function setGrvNumber($grvNumber): void
    {
        $this->grvNumber = $grvNumber;
    }

    /**
     * @param mixed $supplierName
     */
    public function setSupplierName($supplierName): void
    {
        $this->supplierName = $supplierName;
    }

    /**
     * @param mixed $openingCost
     */
    public function setOpeningCost($openingCost): void
    {
        $this->openingCost = $openingCost;
    }

    /**
     * @param mixed $additionCost
     */
    public function setAdditionCost($additionCost): void
    {
        $this->additionCost = $additionCost;
    }

    /**
     * @param mixed $disposalCost
     */
    public function setDisposalCost($disposalCost): void
    {
        $this->disposalCost = $disposalCost;
    }

    /**
     * @param mixed $closingCost
     */
    public function setClosingCost($closingCost): void
    {
        $this->closingCost = $closingCost;
    }

    /**
     * @param mixed $openingDep
     */
    public function setOpeningDep($openingDep): void
    {
        $this->openingDep = $openingDep;
    }

    /**
     * @param mixed $chargeDuringTheYear
     */
    public function setChargeDuringTheYear($chargeDuringTheYear): void
    {
        $this->chargeDuringTheYear = $chargeDuringTheYear;
    }

    /**
     * @param mixed $chargeOnDisposal
     */
    public function setChargeOnDisposal($chargeOnDisposal): void
    {
        $this->chargeOnDisposal = $chargeOnDisposal;
    }

    /**
     * @param mixed $closingDep
     */
    public function setClosingDep($closingDep): void
    {
        $this->closingDep = $closingDep;
    }

    /**
     * @param mixed $nbv
     */
    public function setNbv($nbv): void
    {
        $this->nbv = $nbv;
    }


    /**
     * @param mixed $jan
     */
    public function setJan($jan): void
    {
        $this->jan = $jan;
    }

    /**
     * @param mixed $feb
     */
    public function setFeb($feb): void
    {
        $this->feb = $feb;
    }

    /**
     * @param mixed $mar
     */
    public function setMar($mar): void
    {
        $this->mar = $mar;
    }

    /**
     * @param mixed $apr
     */
    public function setApr($apr): void
    {
        $this->apr = $apr;
    }

    /**
     * @param mixed $may
     */
    public function setMay($may): void
    {
        $this->may = $may;
    }

    /**
     * @param mixed $jun
     */
    public function setJun($jun): void
    {
        $this->jun = $jun;
    }

    /**
     * @param mixed $jul
     */
    public function setJul($jul): void
    {
        $this->jul = $jul;
    }

    /**
     * @param mixed $aug
     */
    public function setAug($aug): void
    {
        $this->aug = $aug;
    }

    /**
     * @param mixed $sep
     */
    public function setSep($sep): void
    {
        $this->sep = $sep;
    }

    /**
     * @param mixed $oct
     */
    public function setOct($oct): void
    {
        $this->oct = $oct;
    }

    /**
     * @param mixed $nov
     */
    public function setNov($nov): void
    {
        $this->nov = $nov;
    }

    /**
     * @param mixed $dec
     */
    public function setDec($dec): void
    {
        $this->dec = $dec;
    }


}
