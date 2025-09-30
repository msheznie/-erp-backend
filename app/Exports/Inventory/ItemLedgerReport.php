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
            trans('custom.item_code'),
            trans('custom.item_description'),
            trans('custom.part_no_ref_number'),
            trans('custom.tran_type'),
            trans('custom.document_code'),
            trans('custom.warehouse'),
            trans('custom.processed_by'),
            trans('custom.transaction_date'),
            trans('custom.uom'),
            trans('custom.qty'),
            trans('custom.wac_local'),
            trans('custom.local_amount'),
            trans('custom.wac_rep'),
            trans('custom.rep_amount')
        ];
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
     * @param mixed $tranType
     */
    public function setTranType($tranType)    {
        $this->tranType = $tranType;
    }

    /**
     * @param mixed $documentCode
     */
    public function setDocumentCode($documentCode)    {
        $this->documentCode = $documentCode;
    }

    /**
     * @param mixed $warehouse
     */
    public function setWarehouse($warehouse)    {
        $this->warehouse = $warehouse;
    }

    /**
     * @param mixed $processedBy
     */
    public function setProcessedBy($processedBy)    {
        $this->processedBy = $processedBy;
    }

    /**
     * @param mixed $transactionDate
     */
    public function setTransactionDate($transactionDate)    {
        $this->transactionDate = ($transactionDate != '1970-01-01') ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($transactionDate)) : null;
    }

    /**
     * @param mixed $uom
     */
    public function setUom($uom)    {
        $this->uom = $uom;
    }

    /**
     * @param mixed $qty
     */
    public function setQty($qty)    {
        $this->qty = $qty;
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
     * @param mixed $wacRep
     */
    public function setWacRep($wacRep)    {
        $this->wacRep = $wacRep;
    }

    /**
     * @param mixed $repAmount
     */
    public function setRepAmount($repAmount)    {
        $this->repAmount = $repAmount;
    }

}
