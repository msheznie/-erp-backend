<?php

namespace App\Services;

/**
 * Computes rounded received qty and "fully received" status for PO/PR detail lines.
 * Used by GRV amend/return/delete flows and any path that recalculates PO receivedQty.
 * All precision logic delegates to DecimalPrecisionService.
 */
class POReceivedQtyUpdateService
{
    /** @var DecimalPrecisionService */
    private $decimalPrecisionService;

    public function __construct(DecimalPrecisionService $decimalPrecisionService)
    {
        $this->decimalPrecisionService = $decimalPrecisionService;
    }

    /**
     * Compute rounded received qty and goodsRecievedYN / GRVSelectedYN for a PO or PR detail.
     *
     * @param object|array $detail Must have noQty and unitOfMeasure (or unitID)
     * @param float $receivedQtySum Raw sum from GRV details (noQty - returnQty etc.)
     * @return array ['receivedQty' => float, 'goodsRecievedYN' => 0|1|2, 'GRVSelectedYN' => 0|1]
     */
    public function computeReceivedQtyAndStatus($detail, float $receivedQtySum): array
    {
        $noQty = is_object($detail) ? (float) $detail->noQty : (float) ($detail['noQty'] ?? 0);
        $unitID = is_object($detail) ? $detail->unitOfMeasure : ($detail['unitOfMeasure'] ?? null);

        $receivedRounded = $this->decimalPrecisionService->roundQuantityToUnitPrecision($receivedQtySum, $unitID);

        if ($this->decimalPrecisionService->aggregateQuantitiesEqual($receivedRounded, 0)) {
            return ['receivedQty' => $receivedRounded, 'goodsRecievedYN' => 0, 'GRVSelectedYN' => 0];
        }
        if ($this->decimalPrecisionService->quantitiesEqualWithinUnitPrecision($noQty, $receivedRounded, $unitID)) {
            return ['receivedQty' => $receivedRounded, 'goodsRecievedYN' => 2, 'GRVSelectedYN' => 1];
        }
        return ['receivedQty' => $receivedRounded, 'goodsRecievedYN' => 1, 'GRVSelectedYN' => 0];
    }

    /**
     * Compare master-level SUM(detail noQty) vs SUM(receivedQty) for "all lines fully received".
     *
     * @param float $detailQtySum
     * @param float $receivedQtySum
     * @param float $epsilon
     * @return bool
     */
    public function isMasterFullyReceived(float $detailQtySum, float $receivedQtySum, float $epsilon = 1e-6): bool
    {
        return $this->decimalPrecisionService->aggregateQuantitiesEqual($detailQtySum, $receivedQtySum, $epsilon);
    }
}
