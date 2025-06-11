<?php

namespace App\Exports\Inventory;

use App\helper\Helper;

class MaterialIssueRegister
{
    public $issueCode;
    public $issueDate;
    public $requestNo;
    public $itemCode;
    public $itemDescription;
    public $uom;
    public $issuedQty;
    public $empID;
    public $empName;
    public $qty;
    public $cost;
    public $amount;

    public function getHeader($reportType) {

        if($reportType == 1)
        {
            return [
                'Issue Code',
                'Issue Date',
                'Request No',
                'Item Code',
                'Item Description',
                'UOM',
                'Issued Qty',
                'Issued to - Emp ID',
                'Issued to - Emp Name',
                'Qty',
                'Cost',
                'Amount'
            ];
        }else {
            return [
                'Issue Code',
                'Issue Date',
                'Request No',
                'Item Code',
                'Item Description',
                'UOM',
                'Issued Qty',
                'Issued to - Asset Code',
                'Issued to - Asset Description',
                'Qty',
                'Cost',
                'Amount'
            ];
        }

    }
    /**
     * @param mixed $issueCode
     */
    public function setIssueCode($issueCode): void
    {
        $this->issueCode = $issueCode;
    }

    /**
     * @param mixed $issueDate
     */
    public function setIssueDate($issueDate): void
    {
        $this->issueDate = $issueDate;
    }

    /**
     * @param mixed $requestNo
     */
    public function setRequestNo($requestNo): void
    {
        $this->requestNo = $requestNo;
    }

    /**
     * @param mixed $itemCode
     */
    public function setItemCode($itemCode): void
    {
        $this->itemCode = $itemCode;
    }

    /**
     * @param mixed $itemDescription
     */
    public function setItemDescription($itemDescription): void
    {
        $this->itemDescription = $itemDescription;
    }

    /**
     * @param mixed $UOM
     */
    public function setUom($uom): void
    {
        $this->uom = $uom;
    }

    /**
     * @param mixed $issuedQty
     */
    public function setIssuedQty($issuedQty): void
    {
        $this->issuedQty = $issuedQty;
    }

        /**
     * @param mixed $empID
     */
    public function setEmpID($empID): void
    {
        $this->empID = $empID;
    }

            /**
     * @param mixed $empName
     */
    public function setEmpName($empName): void
    {
        $this->empName = $empName;
    }

    /**
     * @param mixed $qty
     */
    public function setQty($qty): void
    {
        $this->qty = $qty;
    }

    /**
     * @param mixed $cost
     */
    public function setCost($cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

 

}
