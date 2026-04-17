<?php

namespace App\Services;

use App\helper\Helper;
use App\Models\GRVDetails;
use App\Models\PurchaseOrderDetails;
use Illuminate\Support\Facades\DB;

class GrvPoDetailCostAdjustmentService
{
    /** @var DecimalPrecisionService */
    private $decimalPrecisionService;

    public function __construct(DecimalPrecisionService $decimalPrecisionService)
    {
        $this->decimalPrecisionService = $decimalPrecisionService;
    }

    public function validateCumulativeGrvNetAmountForPoLine(
        int $purchaseOrderDetailsId,
        float $additionalNetAmountRounded,
        ?int $excludeGrvDetailsId,
        ?int $currencyId
    ): ?string {
        $po = PurchaseOrderDetails::find($purchaseOrderDetailsId);
        if (!$po) {
            return trans('custom.error_occurred');
        }
        $cap = (float) $po->netAmount;
        $q = GRVDetails::where('purchaseOrderDetailsID', $purchaseOrderDetailsId)
            ->whereHas('grv_master', function ($q) {
                $q->where('grvCancelledYN', '!=', -1);
            });
        if ($excludeGrvDetailsId) {
            $q->where('grvDetailsID', '!=', $excludeGrvDetailsId);
        }
        $sum = (float) $q->sum(DB::raw('COALESCE(netAmount,0)'));
        $sumRounded = $this->decimalPrecisionService->roundAmountToCurrencyPrecision($sum, $currencyId);
        $totalRounded = $this->decimalPrecisionService->roundAmountToCurrencyPrecision(
            $sumRounded + $additionalNetAmountRounded,
            $currencyId
        );
        $capRounded = $this->decimalPrecisionService->roundAmountToCurrencyPrecision($cap, $currencyId);
        $precision = $currencyId !== null ? (int) Helper::getCurrencyDecimalPlace($currencyId) : 2;
        $eps = pow(10, -$precision - 2);
        if ($totalRounded > $capRounded + $eps) {
            return trans('custom.cumulative_grv_amount_exceeds_po_line');
        }
        return null;
    }

    public function applyToNewLine(
        array &$new,
        float $noQtyRounded,
        ?int $currencyId,
        int $companySystemId
    ): void {
        $grvAmountRaw = isset($new['grvAmount']) ? (float) $new['grvAmount'] : 0.0;
        $enteredLineTrans = $this->decimalPrecisionService->roundAmountToCurrencyPrecision($grvAmountRaw, $currencyId);

        $poUnitTrans = (float) ($new['GRVcostPerUnitSupTransCur'] ?? 0);
        $effectiveOrderUnitTrans = $noQtyRounded > 1e-12
            ? $this->decimalPrecisionService->roundAmountToCurrencyPrecision($enteredLineTrans / $noQtyRounded, $currencyId)
            : 0.0;

        $scale = $poUnitTrans > 1e-12 ? ($effectiveOrderUnitTrans / $poUnitTrans) : 1.0;

        $new['poDisplayUnitCostSupTransCur'] = $this->decimalPrecisionService->roundAmountToCurrencyPrecision($poUnitTrans, $currencyId);
        $new['effectiveUnitCostSupTransCur'] = $effectiveOrderUnitTrans;
        $new['costAdjustmentAppliedYN'] = 1;

        $this->scaleCostFieldsFromPoNewPayload($new, $scale, $effectiveOrderUnitTrans, $currencyId, $companySystemId, $enteredLineTrans);
    }

    public function rebuildFromPoBaselineForUpdate(
        array &$input,
        PurchaseOrderDetails $poDet,
        GRVDetails $existing,
        float $noQtyRounded,
        float $netAmountRounded,
        ?int $currencyId,
        int $companySystemId
    ): void {
        $poUnitTrans = (float) $poDet->GRVcostPerUnitSupTransCur;
        $effectiveOrderUnitTrans = $noQtyRounded > 1e-12
            ? $this->decimalPrecisionService->roundAmountToCurrencyPrecision($netAmountRounded / $noQtyRounded, $currencyId)
            : 0.0;
        $scale = $poUnitTrans > 1e-12 ? ($effectiveOrderUnitTrans / $poUnitTrans) : 1.0;

        $input['poDisplayUnitCostSupTransCur'] = $this->decimalPrecisionService->roundAmountToCurrencyPrecision($poUnitTrans, $currencyId);
        $input['effectiveUnitCostSupTransCur'] = $effectiveOrderUnitTrans;
        $input['costAdjustmentAppliedYN'] = 1;
        $input['noQty'] = $noQtyRounded;
        $input['netAmount'] = $netAmountRounded;
        $input['unitCost'] = $effectiveOrderUnitTrans;

        $input['GRVcostPerUnitSupTransCur'] = $effectiveOrderUnitTrans;
        $input['GRVcostPerUnitLocalCur'] = $this->decimalPrecisionService->roundAmountToCurrencyPrecision(
            (float) $poDet->GRVcostPerUnitLocalCur * $scale,
            (int) $poDet->localCurrencyID
        );
        $input['GRVcostPerUnitSupDefaultCur'] = $this->decimalPrecisionService->roundAmountToCurrencyPrecision(
            (float) $poDet->GRVcostPerUnitSupDefaultCur * $scale,
            (int) $poDet->supplierDefaultCurrencyID
        );
        $input['GRVcostPerUnitComRptCur'] = $this->decimalPrecisionService->roundAmountToCurrencyPrecision(
            (float) $poDet->GRVcostPerUnitComRptCur * $scale,
            (int) $poDet->companyReportingCurrencyID
        );

        $input['landingCost_TransCur'] = $input['GRVcostPerUnitSupTransCur'];
        $input['landingCost_LocalCur'] = $input['GRVcostPerUnitLocalCur'];
        $input['landingCost_RptCur'] = $input['GRVcostPerUnitComRptCur'];

        $oldEff = (float) ($existing->effectiveUnitCostSupTransCur ?: $existing->GRVcostPerUnitSupTransCur);
        $ratio = ($oldEff > 1e-12) ? ($effectiveOrderUnitTrans / $oldEff) : 1.0;

        $input['discountAmount'] = $this->decimalPrecisionService->roundAmountToCurrencyPrecision(
            (float) ($input['discountAmount'] ?? 0) * $ratio,
            $currencyId
        );

        $vatTrans = Helper::roundValue((float) ($input['VATAmount'] ?? 0) * $ratio);
        $input['VATAmount'] = $vatTrans;
        $transCur = (int) $poDet->supplierItemCurrencyID;
        $cc = Helper::currencyConversion($companySystemId, $transCur, $transCur, $input['VATAmount']);
        $input['VATAmountLocal'] = Helper::roundValue($cc['localAmount'] ?? 0);
        $input['VATAmountRpt'] = Helper::roundValue($cc['reportingAmount'] ?? 0);
    }

    private function scaleCostFieldsFromPoNewPayload(
        array &$row,
        float $scale,
        float $effectiveOrderUnitTrans,
        ?int $currencyId,
        int $companySystemId,
        float $enteredLineTrans
    ): void {
        $row['unitCost'] = $effectiveOrderUnitTrans;
        $row['GRVcostPerUnitSupTransCur'] = $effectiveOrderUnitTrans;
        $row['GRVcostPerUnitLocalCur'] = $this->decimalPrecisionService->roundAmountToCurrencyPrecision(
            (float) ($row['GRVcostPerUnitLocalCur'] ?? 0) * $scale,
            isset($row['localCurrencyID']) ? (int) $row['localCurrencyID'] : null
        );
        $row['GRVcostPerUnitSupDefaultCur'] = $this->decimalPrecisionService->roundAmountToCurrencyPrecision(
            (float) ($row['GRVcostPerUnitSupDefaultCur'] ?? 0) * $scale,
            isset($row['supplierDefaultCurrencyID']) ? (int) $row['supplierDefaultCurrencyID'] : null
        );
        $row['GRVcostPerUnitComRptCur'] = $this->decimalPrecisionService->roundAmountToCurrencyPrecision(
            (float) ($row['GRVcostPerUnitComRptCur'] ?? 0) * $scale,
            isset($row['companyReportingCurrencyID']) ? (int) $row['companyReportingCurrencyID'] : null
        );

        $row['landingCost_TransCur'] = $row['GRVcostPerUnitSupTransCur'];
        $row['landingCost_LocalCur'] = $row['GRVcostPerUnitLocalCur'];
        $row['landingCost_RptCur'] = $row['GRVcostPerUnitComRptCur'];

        $row['discountAmount'] = $this->decimalPrecisionService->roundAmountToCurrencyPrecision(
            (float) ($row['discountAmount'] ?? 0) * $scale,
            $currencyId
        );

        $vatTrans = (float) ($row['VATAmount'] ?? 0) * $scale;
        $row['VATAmount'] = Helper::roundValue($vatTrans);
        $transCur = isset($row['supplierItemCurrencyID']) ? (int) $row['supplierItemCurrencyID'] : (int) $currencyId;
        $cc = Helper::currencyConversion($companySystemId, $transCur, $transCur, $row['VATAmount']);
        $row['VATAmountLocal'] = Helper::roundValue($cc['localAmount'] ?? 0);
        $row['VATAmountRpt'] = Helper::roundValue($cc['reportingAmount'] ?? 0);

        $row['netAmount'] = $enteredLineTrans;
    }
}
