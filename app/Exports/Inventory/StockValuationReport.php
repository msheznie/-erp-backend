<?php

namespace App\Exports\Inventory;

class StockValuationReport
{

    private $category;
    private $itemCode;
    private $itemDescription;
    private $uom;
    private $partNumber;
    private $minQty;
    private $maxQty;
    private $qty;
    private $wacLocal;
    private $localAmount;
    private $wacRep;
    private $repAmount;

    public function getHeader() {
        return [
            'Category',
            'Item Code',
            'Item Description',
            'UOM',
            'Part No / Ref.Number',
            'Min Qty',
            'Max Qty',
            'Qty',
            'WAC Local',
            'Local Amount',
            'WAC Rep',
            'Rep Amount'
        ];
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
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
     * @param mixed $minQty
     */
    public function setMinQty($minQty): void
    {
        $this->minQty = $minQty;
    }

    /**
     * @param mixed $maxQty
     */
    public function setMaxQty($maxQty): void
    {
        $this->maxQty = $maxQty;
    }

    /**
     * @param mixed $qty
     */
    public function setQty($qty): void
    {
        $this->qty = $qty;
    }

    /**
     * @param mixed $wacLocal
     */
    public function setWacLocal($wacLocal): void
    {
        $this->wacLocal = $wacLocal;
    }

    /**
     * @param mixed $localAmount
     */
    public function setLocalAmount($localAmount): void
    {
        $this->localAmount = $localAmount;
    }

    /**
     * @param mixed $wacRep
     */
    public function setWacRep($wacRep): void
    {
        $this->wacRep = $wacRep;
    }

    /**
     * @param mixed $repAmount
     */
    public function setRepAmount($repAmount): void
    {
        $this->repAmount = $repAmount;
    }
}
