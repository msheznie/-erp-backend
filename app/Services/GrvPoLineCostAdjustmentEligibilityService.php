<?php

namespace App\Services;

class GrvPoLineCostAdjustmentEligibilityService
{
    private const INVENTORY_FINANCE_CATEGORY_ID = 1;

    public function isEligibleFinanceCategory(int $itemFinanceCategoryId): bool
    {
        return $itemFinanceCategoryId !== self::INVENTORY_FINANCE_CATEGORY_ID;
    }

    public function shouldApplyAmountFirstAdjustment(
        array $line,
        bool $isAmountBasedEntry,
        float $rawQty,
        float $qtyRounded,
        $unitId,
        DecimalPrecisionService $decimalPrecisionService
    ): bool {
        if (!$this->isEligibleFinanceCategory((int) ($line['itemFinanceCategoryID'] ?? 0))) {
            return false;
        }
        if (!isset($line['poQty']) || !$decimalPrecisionService->quantitiesEqualWithinUnitPrecision((float) $line['poQty'], 1.0, $unitId)) {
            return false;
        }
        if (!$isAmountBasedEntry) {
            return false;
        }
        return true;
    }

    public function computeRawQtyFromGrvAmount(array $line, float $grvAmount): float
    {
        $poLineNet = (float) ($line['netAmount'] ?? 0);
        $poQty = (float) ($line['poQty'] ?? 0);
        if (abs($poLineNet) < 1e-12 || abs($poQty) < 1e-12) {
            return 0.0;
        }
        return ($grvAmount / $poLineNet) * $poQty;
    }
}
