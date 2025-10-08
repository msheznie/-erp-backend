<?php

namespace App\Exports\Inventory;

class StockDetailReport
{
    public $itemCode;
    public $itemDescription;
    public $uom;
    public $partNumber;
    public $subCategory;
    public $stockQty;
    public $totalValueUSD;
    public $lastReceiptDate;
    public $lastReceiptQty;
    public $lastIssuedDate;
    public $lastIssuedQty;

    public function getHeader() {
        return [
            trans('custom.item_code'),
            trans('custom.item_description'),
            trans('custom.uom'),
            trans('custom.part_no_ref_number'),
            trans('custom.sub_category'),
            trans('custom.stock_qty'),
            trans('custom.total_value_usd'),
            trans('custom.last_receipt_date'),
            trans('custom.last_receipt_qty'),
            trans('custom.last_issued_date'),
            trans('custom.last_issued_qty')
        ];
    }

    /**
     * @param mixed $itemCode
     */
    public function setItemCode($itemCode)
    {
        $this->itemCode = $itemCode;
    }

    /**
     * @param mixed $itemDescription
     */
    public function setItemDescription($itemDescription)
    {
        $this->itemDescription = $itemDescription;
    }

    /**
     * @param mixed $uom
     */
    public function setUom($uom)
    {
        $this->uom = $uom;
    }

    /**
     * @param mixed $partNumber
     */
    public function setPartNumber($partNumber)
    {
        $this->partNumber = $partNumber;
    }

    /**
     * @param mixed $subCategory
     */
    public function setSubCategory($subCategory)
    {
        $this->subCategory = $subCategory;
    }

    /**
     * @param mixed $stockQty
     */
    public function setStockQty($stockQty)
    {
        $this->stockQty = $stockQty;
    }

    /**
     * @param mixed $totalValueUSD
     */
    public function setTotalValueUSD($totalValueUSD)
    {
        $this->totalValueUSD = $totalValueUSD;
    }

    /**
     * @param mixed $lastReceiptDate
     */
    public function setLastReceiptDate($lastReceiptDate)
    {
        $this->lastReceiptDate = $lastReceiptDate;
    }

    /**
     * @param mixed $lastReceiptQty
     */
    public function setLastReceiptQty($lastReceiptQty)
    {
        $this->lastReceiptQty = $lastReceiptQty;
    }

    /**
     * @param mixed $lastIssuedDate
     */
    public function setLastIssuedDate($lastIssuedDate)
    {
        $this->lastIssuedDate = $lastIssuedDate;
    }

    /**
     * @param mixed $lastIssuedQty
     */
    public function setLastIssuedQty($lastIssuedQty)
    {
        $this->lastIssuedQty = $lastIssuedQty;
    }

}
