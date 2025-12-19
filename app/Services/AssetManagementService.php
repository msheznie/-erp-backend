<?php

namespace App\Services;

use App\Exports\AssetManagement\AssetRegister\AssetRegisterDetail;
use App\Services\Currency\CurrencyService;

class AssetManagementService
{

    public function generateDataToExport($request,$output) {
        $data = [];
        $companyCurrency = \Helper::companyCurrency($request->companySystemID);
        $localDecimalPlace = isset($companyCurrency->localcurrency->DecimalPlaces) ? $companyCurrency->localcurrency->DecimalPlaces: 3;
        $rptDecimalPlace = isset($companyCurrency->reportingcurrency->DecimalPlaces) ? $companyCurrency->reportingcurrency->DecimalPlaces: 2;

        if (!empty($output)) {
            if(empty($data)) {
                $ObjAgingDetailReportHeader =  new AssetRegisterDetail();
                array_push($data,collect($ObjAgingDetailReportHeader->getHeader())->toArray());
                array_push($data,collect($ObjAgingDetailReportHeader->getSubHeader())->toArray());
            }

            $TotalCOSTUNIT = 0;
            $TotaldepAmountLocal = 0;
            $Totallocalnbv = 0;
            $TotalcostUnitRpt = 0;
            $TotaldepAmountRpt = 0;
            $Totalrptnbv = 0;


            foreach ($output as $key => $value) {

                $assetRegisterDetailObj = new AssetRegisterDetail();
                $TotalCOSTUNIT += $value->COSTUNIT;
                $TotaldepAmountLocal += $value->depAmountLocal;
                $Totallocalnbv += $value->localnbv;
                $TotalcostUnitRpt += $value->costUnitRpt;
                $TotaldepAmountRpt += $value->depAmountRpt;
                $Totalrptnbv += $value->rptnbv;

                $assetRegisterDetailObj->setCostGL($value->COSTGLCODE);
                $assetRegisterDetailObj->setAccDepGL($value->ACCDEPGLCODE);
                $assetRegisterDetailObj->setType($value->typeDes);
                $assetRegisterDetailObj->setSegment($value->ServiceLineDes);
                $assetRegisterDetailObj->setFaCode($value->faCode);
                $assetRegisterDetailObj->setGroupedYN($value->groupbydesc);
                $assetRegisterDetailObj->setSerialNumber($value->faUnitSerialNo);
                $assetRegisterDetailObj->setAssetDescription($value->assetDescription);
                $assetRegisterDetailObj->setCategory($value->financeCatDescription);
                $assetRegisterDetailObj->setDepPercentage(round($value->DEPpercentage, 2));
                $assetRegisterDetailObj->setDateAcquired($value->postedDate);
                $assetRegisterDetailObj->setDepStartDate($value->dateDEP);
                $assetRegisterDetailObj->setLocalAmountUnitCost(CurrencyService::convertNumberFormatToNumber(round($value->COSTUNIT, $localDecimalPlace)));
                $assetRegisterDetailObj->setLocalAmountAccDep(CurrencyService::convertNumberFormatToNumber(round($value->depAmountLocal, $localDecimalPlace)));
                $assetRegisterDetailObj->setLocalAmountNetValue(CurrencyService::convertNumberFormatToNumber(round($value->localnbv, $localDecimalPlace)));
                $assetRegisterDetailObj->setRptAmountUnitCost(CurrencyService::convertNumberFormatToNumber(round($value->costUnitRpt, $rptDecimalPlace)));
                $assetRegisterDetailObj->setRptAmountAccDep(CurrencyService::convertNumberFormatToNumber(round($value->depAmountRpt, $rptDecimalPlace)));
                $assetRegisterDetailObj->setRptAmountNetValue(CurrencyService::convertNumberFormatToNumber(round($value->rptnbv, $rptDecimalPlace)));
                array_push($data,collect($assetRegisterDetailObj)->toArray());

            }


            $assetRegisterDetailFooterObj = new AssetRegisterDetail();
            $assetRegisterDetailFooterObj->setLocalAmountUnitCost(CurrencyService::convertNumberFormatToNumber(round($TotalCOSTUNIT, $localDecimalPlace)));
            $assetRegisterDetailFooterObj->setLocalAmountAccDep(CurrencyService::convertNumberFormatToNumber(round($TotaldepAmountLocal, $localDecimalPlace)));
            $assetRegisterDetailFooterObj->setLocalAmountNetValue(CurrencyService::convertNumberFormatToNumber(round($Totallocalnbv, $localDecimalPlace)));
            $assetRegisterDetailFooterObj->setRptAmountUnitCost(CurrencyService::convertNumberFormatToNumber(round($TotalcostUnitRpt,$rptDecimalPlace)));
            $assetRegisterDetailFooterObj->setRptAmountAccDep(CurrencyService::convertNumberFormatToNumber(round($TotaldepAmountRpt,$rptDecimalPlace)));
            $assetRegisterDetailFooterObj->setRptAmountNetValue( CurrencyService::convertNumberFormatToNumber(round($Totalrptnbv, $rptDecimalPlace)));
            array_push($data,collect($assetRegisterDetailFooterObj)->toArray());
//                        $data[$x]['Dep Start Date'] = 'Total';

        }

        return $data;
    }
}
