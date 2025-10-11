<?php

namespace App\Exports\Procument;

use App\helper\Helper;

class ItemwisePoAnalysisReport
{

    public $companyID;
    public $postingYear;
    public $approvedDate;
    public $createdDate;
    public $poCode;
    public $status;
    public $location;
    public $supplierCode;
    public $supplierName;
    public $supplierCountry;
    public $lcc;
    public $sme;
    public $icvCategory;
    public $icvSubCategory;
    public $creditPeriod;
    public $deliveryTerms;
    public $paymentTerms;
    public $expectedDeliveryDate;
    public $narration;
    public $segment;
    public $itemCode;
    public $itemDescription;
    public $isLocallyMade;
    public $unit;
    public $partNoRefNumber;
    public $financeCategory;
    public $financeCategorySub;
    public $accountCode;
    public $accountDescription;
    public $poQty;
    public $unitCostWithoutDiscount;
    public $unitCostWithDiscount;
    public $discountPercentage;
    public $discountAmount;
    public $total;
    public $qtyReceived;
    public $qtyToReceive;
    public $poStatus;
    public $receivedStatus;
    public $receiptDate;

    public function getHeader() {
        return $keys = [
            __('custom.company_id'),
            __('custom.posting_year'),
            __('custom.approved_date'),
            __('custom.created_date'),
            __('custom.po_code'),
            __('custom.status'),
            __('custom.location'),
            __('custom.supplier_code'),
            __('custom.supplier_name'),
            __('custom.supplier_country'),
            __('custom.lcc'),
            __('custom.sme'),
            __('custom.icv_category'),
            __('custom.icv_sub_category'),
            __('custom.credit_period'),
            __('custom.delivery_terms'),
            __('custom.payment_terms'),
            __('custom.expected_delivery_date'),
            __('custom.narration'),
            __('custom.segment'),
            __('custom.item_code'),
            __('custom.item_description'),
            __('custom.is_locally_made'),
            __('custom.unit'),
            __('custom.part_no_ref_number'),
            __('custom.finance_category'),
            __('custom.finance_category_sub'),
            __('custom.account_code'),
            __('custom.account_description'),
            __('custom.po_qty'),
            __('custom.unit_cost_without_discount'),
            __('custom.unit_cost_with_discount'),
            __('custom.discount_percentage'),
            __('custom.discount_amount'),
            __('custom.total'),
            __('custom.qty_received'),
            __('custom.qty_to_receive'),
            __('custom.po_status'),
            __('custom.received_status'),
            __('custom.receipt_date'),
        ];
    }

    public function getColumnFormat() {
        return [
            'C' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'D' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'R' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AN' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AE' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AF' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AH' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AI' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
    /**
     * @param mixed $companyID
     */
    public function setCompanyID($companyID): void
    {
        $this->companyID = $companyID;
    }

    /**
     * @param mixed $postingYear
     */
    public function setPostingYear($postingYear): void
    {
        $this->postingYear = $postingYear;
    }

    /**
     * @param mixed $approvedDate
     */
    public function setApprovedDate($approvedDate): void
    {
        $this->approvedDate = ($approvedDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($approvedDate)) : null;
    }

    /**
     * @param mixed $createdDate
     */
    public function setCreatedDate($createdDate): void
    {
        $this->createdDate = ($createdDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($createdDate)) : null;
    }

    /**
     * @param mixed $poCode
     */
    public function setPoCode($poCode): void
    {
        $this->poCode = $poCode;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location): void
    {
        $this->location = $location;
    }

    /**
     * @param mixed $supplierCode
     */
    public function setSupplierCode($supplierCode): void
    {
        $this->supplierCode = $supplierCode;
    }

    /**
     * @param mixed $supplierName
     */
    public function setSupplierName($supplierName): void
    {
        $this->supplierName = $supplierName;
    }

    /**
     * @param mixed $supplierCountry
     */
    public function setSupplierCountry($supplierCountry): void
    {
        $this->supplierCountry = $supplierCountry;
    }

    /**
     * @param mixed $lcc
     */
    public function setLcc($lcc): void
    {
        $this->lcc = $lcc;
    }

    /**
     * @param mixed $sme
     */
    public function setSme($sme): void
    {
        $this->sme = $sme;
    }

    /**
     * @param mixed $icvCategory
     */
    public function setIcvCategory($icvCategory): void
    {
        $this->icvCategory = $icvCategory;
    }

    /**
     * @param mixed $icvSubCategory
     */
    public function setIcvSubCategory($icvSubCategory): void
    {
        $this->icvSubCategory = $icvSubCategory;
    }

    /**
     * @param mixed $creditPeriod
     */
    public function setCreditPeriod($creditPeriod): void
    {
        $this->creditPeriod = $creditPeriod;
    }

    /**
     * @param mixed $deliveryTerms
     */
    public function setDeliveryTerms($deliveryTerms): void
    {
        $this->deliveryTerms = $deliveryTerms;
    }

    /**
     * @param mixed $paymentTerms
     */
    public function setPaymentTerms($paymentTerms): void
    {
        $this->paymentTerms = $paymentTerms;
    }

    /**
     * @param mixed $expectedDeliveryDate
     */
    public function setExpectedDeliveryDate($expectedDeliveryDate): void
    {
        $this->expectedDeliveryDate = ($expectedDeliveryDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($expectedDeliveryDate)) : null;
    }

    /**
     * @param mixed $narration
     */
    public function setNarration($narration): void
    {
        $this->narration = $narration;
    }

    /**
     * @param mixed $segment
     */
    public function setSegment($segment): void
    {
        $this->segment = $segment;
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
     * @param mixed $isLocallyMade
     */
    public function setIsLocallyMade($isLocallyMade): void
    {
        $this->isLocallyMade = $isLocallyMade;
    }

    /**
     * @param mixed $unit
     */
    public function setUnit($unit): void
    {
        $this->unit = $unit;
    }

    /**
     * @param mixed $partNoRefNumber
     */
    public function setPartNoRefNumber($partNoRefNumber): void
    {
        $this->partNoRefNumber = $partNoRefNumber;
    }

    /**
     * @param mixed $financeCategory
     */
    public function setFinanceCategory($financeCategory): void
    {
        $this->financeCategory = $financeCategory;
    }

    /**
     * @param mixed $financeCategorySub
     */
    public function setFinanceCategorySub($financeCategorySub): void
    {
        $this->financeCategorySub = $financeCategorySub;
    }

    /**
     * @param mixed $accountCode
     */
    public function setAccountCode($accountCode): void
    {
        $this->accountCode = $accountCode;
    }

    /**
     * @param mixed $accountDescription
     */
    public function setAccountDescription($accountDescription): void
    {
        $this->accountDescription = $accountDescription;
    }

    /**
     * @param mixed $poQty
     */
    public function setPoQty($poQty): void
    {
        $this->poQty = $poQty;
    }

    /**
     * @param mixed $unitCostWithoutDiscount
     */
    public function setUnitCostWithoutDiscount($unitCostWithoutDiscount): void
    {
        $this->unitCostWithoutDiscount = $unitCostWithoutDiscount;
    }

    /**
     * @param mixed $unitCostWithDiscount
     */
    public function setUnitCostWithDiscount($unitCostWithDiscount): void
    {
        $this->unitCostWithDiscount = $unitCostWithDiscount;
    }

    /**
     * @param mixed $discountPercentage
     */
    public function setDiscountPercentage($discountPercentage): void
    {
        $this->discountPercentage = $discountPercentage;
    }

    /**
     * @param mixed $discountAmount
     */
    public function setDiscountAmount($discountAmount): void
    {
        $this->discountAmount = $discountAmount;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total): void
    {
        $this->total = $total;
    }

    /**
     * @param mixed $qtyReceived
     */
    public function setQtyReceived($qtyReceived): void
    {
        $this->qtyReceived = $qtyReceived;
    }

    /**
     * @param mixed $qtyToReceive
     */
    public function setQtyToReceive($qtyToReceive): void
    {
        $this->qtyToReceive = $qtyToReceive;
    }

    /**
     * @param mixed $poStatus
     */
    public function setPoStatus($poStatus): void
    {
        $this->poStatus = $poStatus;
    }

    /**
     * @param mixed $receivedStatus
     */
    public function setReceivedStatus($receivedStatus): void
    {
        $this->receivedStatus = $receivedStatus;
    }

    /**
     * @param mixed $receiptDate
     */
    public function setReceiptDate($receiptDate): void
    {
        $this->receiptDate = ($receiptDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($receiptDate)) : null;
    }

}
