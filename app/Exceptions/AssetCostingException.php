<?php

namespace App\Exceptions;
use Exception;

class AssetCostingException extends Exception
{
    private $excelRow;
    private $assetCostingUploadID;

    public function __construct($message, $assetCostingUploadID, $excelRow = null,$code = 0, Exception $previous = null)
    {
        $this->excelRow = $excelRow;
        $this->assetCostingUploadID = $assetCostingUploadID;
        parent::__construct($message, $code, $previous);
    }

    public function getExcelRow()
    {
        return $this->excelRow;
    }

    public function getAssetCostingUploadID()
    {
        return $this->assetCostingUploadID;
    }
}

