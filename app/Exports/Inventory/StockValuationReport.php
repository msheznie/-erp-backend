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
            trans('custom.category'),
            trans('custom.item_code'),
            trans('custom.item_description'),
            trans('custom.uom'),
            trans('custom.part_no_ref_number'),
            trans('custom.min_qty'),
            trans('custom.max_qty'),
            trans('custom.qty'),
            trans('custom.wac_local'),
            trans('custom.local_amount'),
            trans('custom.wac_rep'),
            trans('custom.rep_amount')
        ];
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
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
     * @param mixed $minQty
     */
    public function setMinQty($minQty)
    {
        $this->minQty = $minQty;
    }

    /**
     * @param mixed $maxQty
     */
    public function setMaxQty($maxQty)
    {
        $this->maxQty = $maxQty;
    }

    /**
     * @param mixed $qty
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
    }

    /**
     * @param mixed $wacLocal
     */
    public function setWacLocal($wacLocal)
    {
        $this->wacLocal = $wacLocal;
    }

    /**
     * @param mixed $localAmount
     */
    public function setLocalAmount($localAmount)
    {
        $this->localAmount = $localAmount;
    }

    /**
     * @param mixed $wacRep
     */
    public function setWacRep($wacRep)
    {
        $this->wacRep = $wacRep;
    }

    /**
     * @param mixed $repAmount
     */
    public function setRepAmount($repAmount)
    {
        $this->repAmount = $repAmount;
    }
}
