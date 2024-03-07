<?php

namespace App\Exports\Inventory;

use App\helper\Helper;

class ItemLedgerReport
{
    public $itemCode;
    public $itemDescription;
    public $partNumber;
    public $tranType;
    public $documentCode;
    public $warehouse;
    public $processedBy;
    public $transactionDate;
    public $uom;
    public $qty;
    public $wacLocal;
    public $localAmount;
    public $wacRep;
    public $repAmount;

    public function getHeader() {
        return [
            'Item Code',
            'Item Description',
            'Part No / Ref.Number',
            'Tran Type',
            'Document Code',
            'Warehouse',
            'Processed By',
            'Transaction Date',
            'UOM',
            'Qty',
            'WAC Local',
            'Local Amount',
            'WAC Rep',
            'Rep Amount'
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
     * @param mixed $partNumber
     */
    public function setPartNumber($partNumber): void
    {
        $this->partNumber = $partNumber;
    }

    /**
     * @param mixed $tranType
     */
    public function setTranType($tranType): void
    {
        $this->tranType = $tranType;
    }

    /**
     * @param mixed $documentCode
     */
    public function setDocumentCode($documentCode): void
    {
        $this->documentCode = $documentCode;
    }

    /**
     * @param mixed $warehouse
     */
    public function setWarehouse($warehouse): void
    {
        $this->warehouse = $warehouse;
    }

    /**
     * @param mixed $processedBy
     */
    public function setProcessedBy($processedBy): void
    {
        $this->processedBy = $processedBy;
    }

    /**
     * @param mixed $transactionDate
     */
    public function setTransactionDate($transactionDate): void
    {
        $this->transactionDate = ($transactionDate != '1970-01-01') ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($transactionDate)) : null;
    }

    /**
     * @param mixed $uom
     */
    public function setUom($uom): void
    {
        $this->uom = $uom;
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
