<?php
namespace App\Services\Excel;

interface ExportToExcelInterface {

    public function setTitle($title);

    public function  setFileName($file_name);

    public  function setPath($path);

    public  function setCurrency($currency);

    public function setCompanyName($companyName);

    public function setCompanyCode($companyCode);

    public function setToDate($date);

    public function setFromDate($date);

    public function setData($data);

    // 1 - From query
    // 2 - From Obj
    public function setDateType($dataType);

    // template from s3 - 1
    // create excel and download to local - 2
    public function setExcelType($type);

    public function setColumnAutoSize($setColumnAutoSize);

    public function generateExcel():Array;


}
