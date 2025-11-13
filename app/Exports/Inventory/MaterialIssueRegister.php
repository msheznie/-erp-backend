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
                trans('custom.issue_code'),
                trans('custom.issue_date'),
                trans('custom.request_no'),
                trans('custom.item_code'),
                trans('custom.item_description'),
                trans('custom.uom'),
                trans('custom.issued_qty'),
                trans('custom.issued_to_emp_id'),
                trans('custom.issued_to_emp_name'),
                trans('custom.qty'),
                trans('custom.cost'),
                trans('custom.amount')
            ];
        }else if ($reportType == 2){
            return [
                trans('custom.issue_code'),
                trans('custom.issue_date'),
                trans('custom.request_no'),
                trans('custom.item_code'),
                trans('custom.item_description'),
                trans('custom.uom'),
                trans('custom.issued_qty'),
                trans('custom.issued_to_asset_code'),
                trans('custom.issued_to_asset_description'),
                trans('custom.qty'),
                trans('custom.cost'),
                trans('custom.amount')
            ];
        }else {
            return [
                trans('custom.issue_code'),
                trans('custom.issue_date'),
                trans('custom.request_no'),
                trans('custom.item_code'),
                trans('custom.item_description'),
                trans('custom.uom'),
                trans('custom.issued_qty'),
                trans('custom.issued_to_segment_code'),
                trans('custom.issued_to_segment_description'),
                trans('custom.qty'),
                trans('custom.cost'),
                trans('custom.amount')
            ];
        }

    }
    /**
     * @param mixed $issueCode
     */
    public function setIssueCode($issueCode)    {
        $this->issueCode = $issueCode;
    }

    /**
     * @param mixed $issueDate
     */
    public function setIssueDate($issueDate)    {
        $this->issueDate = $issueDate;
    }

    /**
     * @param mixed $requestNo
     */
    public function setRequestNo($requestNo)    {
        $this->requestNo = $requestNo;
    }

    /**
     * @param mixed $itemCode
     */
    public function setItemCode($itemCode)    {
        $this->itemCode = $itemCode;
    }

    /**
     * @param mixed $itemDescription
     */
    public function setItemDescription($itemDescription)    {
        $this->itemDescription = $itemDescription;
    }

    /**
     * @param mixed $UOM
     */
    public function setUom($uom)    {
        $this->uom = $uom;
    }

    /**
     * @param mixed $issuedQty
     */
    public function setIssuedQty($issuedQty)    {
        $this->issuedQty = $issuedQty;
    }

        /**
     * @param mixed $empID
     */
    public function setEmpID($empID)    {
        $this->empID = $empID;
    }

            /**
     * @param mixed $empName
     */
    public function setEmpName($empName)    {
        $this->empName = $empName;
    }

    /**
     * @param mixed $qty
     */
    public function setQty($qty)    {
        $this->qty = $qty;
    }

    /**
     * @param mixed $cost
     */
    public function setCost($cost)    {
        $this->cost = $cost;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)    {
        $this->amount = $amount;
    }

 

}
