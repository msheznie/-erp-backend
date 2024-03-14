<?php

namespace App\Services\Excel;

use App\helper\CreateExcel;
use App\helper\GenerateExcel;
use App\helper\Helper;
use App\Models\CurrencyMaster;

class ExportReportToExcelService implements ExportToExcelInterface
{

    public $title;
    public $fileName;
    public $path;
    public $companyName;
    public $companyCode;
    public $toDate;
    public $fromDate;
    public $currency;
    public $data;
    public $type;
    public $currencyObj;
    public $details;
    public $reportType;
    public $excelFormat;
    public $dataType = 1;
    public $excelType;

    /**
     * @param mixed $excelType
     */
    public function setExcelType($excelType = 1): ExportReportToExcelService
    {
        $this->excelType = $excelType;
        return $this;
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
     * @param mixed $reportType
     */
    public function setReportType($reportType): ExportReportToExcelService
    {
        $this->reportType = $reportType;
        return $this;
    }


    /**
     * @param mixed $type
     */
    public function setType($type): ExportReportToExcelService
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency,$isString = false): ExportReportToExcelService
    {
        if($isString)
        {
            $this->currency = $currency;

        }else {
            if($currency) {
                $currencyObj = CurrencyMaster::where('currencyID',$currency)->select(['CurrencyCode','DecimalPlaces'])->first();
                $this->currencyObj = $currencyObj;
                $this->currency = $this->currencyObj->CurrencyCode;
            }
        }


        return $this;
    }
    /**
     * @param mixed $title
     */
    public function setTitle($title): ExportReportToExcelService
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName): ExportReportToExcelService
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path): ExportReportToExcelService
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param mixed $companyName
     */
    public function setCompanyName($companyName): ExportReportToExcelService
    {
        $this->companyName = $companyName;
        return $this;
    }

    /**
     * @param mixed $companyCode
     */
    public function setCompanyCode($companyCode): ExportReportToExcelService
    {
        $this->companyCode = $companyCode;
        return $this;
    }

    /**
     * @param mixed $toDate
     */
    public function setToDate($toDate): ExportReportToExcelService
    {
        $this->toDate = ($toDate) ? Helper::dateFormat($toDate) : null;
        return $this;
    }

    /**
     * @param mixed $fromDate
     */
    public function setFromDate($fromDate): ExportReportToExcelService
    {
        $this->fromDate = ($fromDate) ? Helper::dateFormat($fromDate) : null;
        return $this;
    }

    public function setData($data):ExportReportToExcelService{
        $this->data = $data;
        return $this;
    }

    public function setDateType($dataType=1):ExportReportToExcelService
    {
        $this->dataType = $dataType;

        return $this;
    }


    /**
     * @param mixed $details
     */
    public function setDetails(): ExportReportToExcelService
    {

        $detail_array = array(  'type' => $this->reportType,
            'from_date'=>$this->fromDate,
            'to_date'=>$this->toDate,
            'company_name'=>$this->companyName,
            'company_code'=>$this->companyCode,
            'cur'=>$this->currency,
            'title'=>$this->title,
            'excelFormat' => $this->excelFormat,
            'dataType' => $this->dataType
        );


        $this->details = $detail_array;
        return $this;
    }


    public function generateExcel(): Array {
        $generate = (!isset($this->excelType) || $this->excelType == 1) ? CreateExcel::process($this->data,$this->type,$this->fileName,$this->path,$this->details) : GenerateExcel::process($this->data,$this->type,$this->fileName,$this->path,$this->details);

        if($generate == '')
            return ['success' => false , 'message' => 'Unable to export excel'];

        return ['success' => true , 'message' =>  trans('custom.success_export') ,'data' => $generate];

    }


}
