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
            'Company ID',
            'Item Code',
            'Item Description',
            'Part No / Ref.Number',
            'Category',
            'Movement Category',
            'UOM',
            'Qty',
            'WAC Local',
            'Local Amount',
            '<=30 (Qty)',
            '<=30 (Value)',
            '31 to 60 (Qty)',
            '31 to 60 (Value)',
            '61 to 90 (Qty)',
            '61 to 90 (Value)',
            '91 to 120 (Qty)',
            '91 to 120 (Value)',
            '121 to 365 (Qty)',
            '121 to 365 (Value)',
            '366 to 730 (Qty)',
            '366 to 730 (Value)',
            'Over 730 (Qty)',
            'Over 730 (Value)'
        ];
    }

    /**
     * @param mixed $companyId
     */
    public function setCompanyId($companyId): void
    {
        $this->companyId = $companyId;
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
     * @param mixed $partNumber
     */
    public function setPartNumber($partNumber): void
    {
        $this->partNumber = $partNumber;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @param mixed $movementCategory
     */
    public function setMovementCategory($movementCategory): void
    {
        $this->movementCategory = $movementCategory;
    }

    /**
     * @param mixed $uom
     */
    public function setUom($uom): void
    {
        $this->uom = $uom;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
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
     * @param mixed $qty30
     */
    public function setQty30($qty30): void
    {
        $this->qty30 = $qty30;
    }

    /**
     * @param mixed $value30
     */
    public function setValue30($value30): void
    {
        $this->value30 = $value30;
    }

    /**
     * @param mixed $qty31to60
     */
    public function setQty31to60($qty31to60): void
    {
        $this->qty31to60 = $qty31to60;
    }

    /**
     * @param mixed $value31to60
     */
    public function setValue31to60($value31to60): void
    {
        $this->value31to60 = $value31to60;
    }

    /**
     * @param mixed $qty61to90
     */
    public function setQty61to90($qty61to90): void
    {
        $this->qty61to90 = $qty61to90;
    }

    /**
     * @param mixed $value61to90
     */
    public function setValue61to90($value61to90): void
    {
        $this->value61to90 = $value61to90;
    }

    /**
     * @param mixed $qty91to120
     */
    public function setQty91to120($qty91to120): void
    {
        $this->qty91to120 = $qty91to120;
    }

    /**
     * @param mixed $value91to120
     */
    public function setValue91to120($value91to120): void
    {
        $this->value91to120 = $value91to120;
    }

    /**
     * @param mixed $qty121to365
     */
    public function setQty121to365($qty121to365): void
    {
        $this->qty121to365 = $qty121to365;
    }

    /**
     * @param mixed $value121to365
     */
    public function setValue121to365($value121to365): void
    {
        $this->value121to365 = $value121to365;
    }

    /**
     * @param mixed $qty366to730
     */
    public function setQty366to730($qty366to730): void
    {
        $this->qty366to730 = $qty366to730;
    }

    /**
     * @param mixed $value366to730
     */
    public function setValue366to730($value366to730): void
    {
        $this->value366to730 = $value366to730;
    }

    /**
     * @param mixed $qtyOver730
     */
    public function setQtyOver730($qtyOver730): void
    {
        $this->qtyOver730 = $qtyOver730;
    }

    /**
     * @param mixed $valueOver730
     */
    public function setValueOver730($valueOver730): void
    {
        $this->valueOver730 = $valueOver730;
    }
}
