<?php

namespace App\Services\Excel;

use App\Exports\GeneralLedger\VAT\DetailsOfInwardSupplyReport;
use App\Exports\GeneralLedger\VAT\VatDetailReport;

class ExportVatDetailReportService extends ExportReportToExcelService
{
    public $company_vat_registration_number;
    public $excelFormat;


    public function  getExcelCloumnFormat($reportId) {
        if($reportId == 3) {
            $obj = new VatDetailReport();
            return $obj->getCloumnFormat();
        }else if ($reportId == 4 || $reportId == 5) {
            $obj = new DetailsOfInwardSupplyReport();
            return $obj->getCloumnFormat();
        }

    }

    /**
     * @param mixed $excelFormat
     */
    public function setExcelFormat($excelFormat): ExportReportToExcelService
    {
        $this->excelFormat = $excelFormat;
        return $this;
    }


    /**
     * @param mixed $company_vat_registration_number
     */
    public function setCompanyVatRegistrationNumber($company_vat_registration_number): ExportReportToExcelService
    {
        $this->company_vat_registration_number = $company_vat_registration_number;
        return $this;
    }

    public function setDetails(): ExportReportToExcelService
    {
        $detail_array = array(  'type' => 6,
            'from_date'=>$this->fromDate,
            'to_date'=>$this->toDate,
            'company_name'=>$this->companyName,
            'company_code'=>$this->companyCode,
            'cur'=>$this->currency,
            'title'=>$this->title,
            'company_vat_registration_number' => $this->company_vat_registration_number,
            'dataType' => $this->dataType,
            'excelFormat' => $this->excelFormat
        );

        $this->details = $detail_array;

        return $this;
    }



}
