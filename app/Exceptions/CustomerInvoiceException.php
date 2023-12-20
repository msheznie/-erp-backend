<?php

namespace App\Exceptions;

use Exception;

class CustomerInvoiceException extends Exception
{
    private $excelRow;

    public function __construct($message, $excelRow = null, $code = 0, Exception $previous = null)
    {
        $this->excelRow = $excelRow;
        parent::__construct($message, $code, $previous);
    }

    public function getExcelRow()
    {
        return $this->excelRow;
    }
}
