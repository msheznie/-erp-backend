<?php

namespace App\Report;

class PdfReport
{
    public $dataArray;
    public $viewName;
    public $rootPath;
    public $fileName;
    public $count;
    public $companyId;
    public $fromDate;

    public $toDate;
    public $reportCount;
    public $isZip;
    public $directoryName;

    /**
     * @param mixed $directoryName
     */
    public function setDirectoryName($directoryName): void
    {
        $this->directoryName = $directoryName;
    }

    /**
     * @param mixed $reportCount
     */
    public function setReportCount($reportCount): void
    {
        $this->reportCount = $reportCount;
    }

    /**
     * @param mixed $isZip
     */
    public function setIsZip($isZip = false): void
    {
        $this->isZip = $isZip;
    }

    /**
     * @param mixed $companyId
     */
    public function setCompanyId($companyId): void
    {
        $this->companyId = $companyId;
    }


    /**
     * @param mixed $fromDate
     */
    public function setFromDate($fromDate): void
    {
        $this->fromDate = $fromDate;
    }

    /**
     * @param mixed $toDate
     */
    public function setToDate($toDate): void
    {
        $this->toDate = $toDate;
    }

    /**
     * @param mixed $dataArray
     */
    public function setDataArray($dataArray): void
    {
        $this->dataArray = $dataArray;
    }

    /**
     * @param mixed $viewName
     */
    public function setViewName($viewName): void
    {
        $this->viewName = $viewName;
    }

    /**
     * @param mixed $rootPath
     */
    public function setRootPath($rootPath): void
    {
        $this->rootPath = $this->directoryName.'/'.$rootPath;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count): void
    {
        $this->count = $count;
    }




}
