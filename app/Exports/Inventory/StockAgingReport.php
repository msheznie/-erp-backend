<?php

namespace App\Exports\Inventory;

class StockAgingReport
{
    public $companyId;
    public $itemCode;
    public $itemDescription;
    public $partNumber;
    public $category;
    public $movementCategory;
    public $uom;
    public $quantity;
    public $wacLocal;
    public $localAmount;
    public $qty30;
    public $value30;
    public $qty31to60;
    public $value31to60;
    public $qty61to90;
    public $value61to90;
    public $qty91to120;
    public $value91to120;
    public $qty121to365;
    public $value121to365;
    public $qty366to730;
    public $value366to730;
    public $qtyOver730;
    public $valueOver730;

    public function getHeader() {
        return [
            trans('custom.company_id'),
            trans('custom.item_code'),
            trans('custom.item_description'),
            trans('custom.part_no_ref_number'),
            trans('custom.category'),
            trans('custom.movement_category'),
            trans('custom.uom'),
            trans('custom.qty'),
            trans('custom.wac_local'),
            trans('custom.local_amount'),
            trans('custom.aging_30_qty'),
            trans('custom.aging_30_value'),
            trans('custom.aging_31_to_60_qty'),
            trans('custom.aging_31_to_60_value'),
            trans('custom.aging_61_to_90_qty'),
            trans('custom.aging_61_to_90_value'),
            trans('custom.aging_91_to_120_qty'),
            trans('custom.aging_91_to_120_value'),
            trans('custom.aging_121_to_365_qty'),
            trans('custom.aging_121_to_365_value'),
            trans('custom.aging_366_to_730_qty'),
            trans('custom.aging_366_to_730_value'),
            trans('custom.aging_over_730_qty'),
            trans('custom.aging_over_730_value')
        ];
    }

    /**
     * @param mixed $companyId
     */
    public function setCompanyId($companyId)    {
        $this->companyId = $companyId;
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
     * @param mixed $partNumber
     */
    public function setPartNumber($partNumber)    {
        $this->partNumber = $partNumber;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)    {
        $this->category = $category;
    }

    /**
     * @param mixed $movementCategory
     */
    public function setMovementCategory($movementCategory)    {
        $this->movementCategory = $movementCategory;
    }

    /**
     * @param mixed $uom
     */
    public function setUom($uom)    {
        $this->uom = $uom;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)    {
        $this->quantity = $quantity;
    }

    /**
     * @param mixed $wacLocal
     */
    public function setWacLocal($wacLocal)    {
        $this->wacLocal = $wacLocal;
    }

    /**
     * @param mixed $localAmount
     */
    public function setLocalAmount($localAmount)    {
        $this->localAmount = $localAmount;
    }

    /**
     * @param mixed $qty30
     */
    public function setQty30($qty30)    {
        $this->qty30 = $qty30;
    }

    /**
     * @param mixed $value30
     */
    public function setValue30($value30)    {
        $this->value30 = $value30;
    }

    /**
     * @param mixed $qty31to60
     */
    public function setQty31to60($qty31to60)    {
        $this->qty31to60 = $qty31to60;
    }

    /**
     * @param mixed $value31to60
     */
    public function setValue31to60($value31to60)    {
        $this->value31to60 = $value31to60;
    }

    /**
     * @param mixed $qty61to90
     */
    public function setQty61to90($qty61to90)    {
        $this->qty61to90 = $qty61to90;
    }

    /**
     * @param mixed $value61to90
     */
    public function setValue61to90($value61to90)    {
        $this->value61to90 = $value61to90;
    }

    /**
     * @param mixed $qty91to120
     */
    public function setQty91to120($qty91to120)    {
        $this->qty91to120 = $qty91to120;
    }

    /**
     * @param mixed $value91to120
     */
    public function setValue91to120($value91to120)    {
        $this->value91to120 = $value91to120;
    }

    /**
     * @param mixed $qty121to365
     */
    public function setQty121to365($qty121to365)    {
        $this->qty121to365 = $qty121to365;
    }

    /**
     * @param mixed $value121to365
     */
    public function setValue121to365($value121to365)    {
        $this->value121to365 = $value121to365;
    }

    /**
     * @param mixed $qty366to730
     */
    public function setQty366to730($qty366to730)    {
        $this->qty366to730 = $qty366to730;
    }

    /**
     * @param mixed $value366to730
     */
    public function setValue366to730($value366to730)    {
        $this->value366to730 = $value366to730;
    }

    /**
     * @param mixed $qtyOver730
     */
    public function setQtyOver730($qtyOver730)    {
        $this->qtyOver730 = $qtyOver730;
    }

    /**
     * @param mixed $valueOver730
     */
    public function setValueOver730($valueOver730)    {
        $this->valueOver730 = $valueOver730;
    }
}
