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
            'Item Code',
            'Item Description',
            'UOM',
            'Part No / Ref.Number',
            'Sub Category',
            'Stock Qty',
            'Total Value (USD)',
            'Last Receipt Date',
            'Last Receipt Qty',
            'Last Issued Date',
            'Last Issued Qty'
        ];
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
     * @param mixed $uom
     */
    public function setUom($uom): void
    {
        $this->uom = $uom;
    }

    /**
     * @param mixed $partNumber
     */
    public function setPartNumber($partNumber): void
    {
        $this->partNumber = $partNumber;
    }

    /**
     * @param mixed $subCategory
     */
    public function setSubCategory($subCategory): void
    {
        $this->subCategory = $subCategory;
    }

    /**
     * @param mixed $stockQty
     */
    public function setStockQty($stockQty): void
    {
        $this->stockQty = $stockQty;
    }

    /**
     * @param mixed $totalValueUSD
     */
    public function setTotalValueUSD($totalValueUSD): void
    {
        $this->totalValueUSD = $totalValueUSD;
    }

    /**
     * @param mixed $lastReceiptDate
     */
    public function setLastReceiptDate($lastReceiptDate): void
    {
        $this->lastReceiptDate = $lastReceiptDate;
    }

    /**
     * @param mixed $lastReceiptQty
     */
    public function setLastReceiptQty($lastReceiptQty): void
    {
        $this->lastReceiptQty = $lastReceiptQty;
    }

    /**
     * @param mixed $lastIssuedDate
     */
    public function setLastIssuedDate($lastIssuedDate): void
    {
        $this->lastIssuedDate = $lastIssuedDate;
    }

    /**
     * @param mixed $lastIssuedQty
     */
    public function setLastIssuedQty($lastIssuedQty): void
    {
        $this->lastIssuedQty = $lastIssuedQty;
    }

}
