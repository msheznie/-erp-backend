<?php

namespace App\Services;

use App\helper\Helper;
use App\Models\Unit;

/**
 * Centralises decimal precision and tolerance-based comparison for PR, PO, and GRV.
 * Uses Unit Master (decimalPrecision, displayRoundOff) for quantities and
 * Currency Master (DecimalPlaces) for amounts. No precision logic in helper files.
 */
class DecimalPrecisionService
{
    /** Default decimal places for quantity when unit is missing */
    const DEFAULT_QUANTITY_PRECISION = 2;

    /** Default decimal places for amount when currency is missing */
    const DEFAULT_AMOUNT_PRECISION = 2;

    const MAX_QUANTITY_INPUT_PRECISION = 10;

    /**
     * Round quantity to Unit Master decimalPrecision.
     *
     * @param float $qty
     * @param int|object|null $unitID UnitID or Unit model (or null for default precision)
     * @return float
     */
    public function roundQuantityToUnitPrecision(float $qty, $unitID): float
    {
        $precision = $this->getUnitDecimalPrecision($unitID);
        return round($qty, $precision);
    }

    /**
     * Round amount to Currency Master DecimalPlaces.
     *
     * @param float $amount
     * @param int|null $currencyID
     * @return float
     */
    public function roundAmountToCurrencyPrecision(float $amount, $currencyID): float
    {
        $precision = $currencyID !== null
            ? (int) Helper::getCurrencyDecimalPlace($currencyID)
            : self::DEFAULT_AMOUNT_PRECISION;
        return round($amount, $precision);
    }

    /**
     * Compare two quantities for "fully received" / "fully ordered" using unit precision (tolerance).
     *
     * @param float $qty1
     * @param float $qty2
     * @param int|object|null $unitID
     * @return bool
     */
    public function quantitiesEqualWithinUnitPrecision(float $qty1, float $qty2, $unitID): bool
    {
        $precision = $this->getUnitDecimalPrecision($unitID);
        $r1 = round($qty1, $precision);
        $r2 = round($qty2, $precision);
        return abs($r1 - $r2) < pow(10, -$precision - 1);
    }

    /**
     * Compare aggregate quantities (e.g. SUM(detail) vs received) with small epsilon for float noise.
     *
     * @param float $detailQtySum
     * @param float $receivedQtySum
     * @param float $epsilon
     * @return bool
     */
    public function aggregateQuantitiesEqual(float $detailQtySum, float $receivedQtySum, float $epsilon = 1e-6): bool
    {
        return abs($detailQtySum - $receivedQtySum) < $epsilon;
    }

    /**
     * Get decimal precision for a unit (for storage/calculation). Default 2.
     *
     * @param int|object|null $unitID UnitID, or Unit model, or null
     * @return int
     */
    public function getUnitDecimalPrecision($unitID): int
    {
        if ($unitID === null) {
            return self::DEFAULT_QUANTITY_PRECISION;
        }
        if (is_object($unitID) && isset($unitID->decimalPrecision)) {
            $p = (int) $unitID->decimalPrecision;
            return $p >= 0 ? $p : self::DEFAULT_QUANTITY_PRECISION;
        }
        $unit = Unit::find($unitID);
        if (!$unit || $unit->decimalPrecision === null) {
            return self::DEFAULT_QUANTITY_PRECISION;
        }
        $p = (int) $unit->decimalPrecision;
        return $p >= 0 ? $p : self::DEFAULT_QUANTITY_PRECISION;
    }

    /**
     * Get allowed input precision for a unit, capped at MAX_QUANTITY_INPUT_PRECISION.
     *
     * @param int|object|null $unitID
     * @return int
     */
    public function getUnitInputPrecision($unitID): int
    {
        $p = $this->getUnitDecimalPrecision($unitID);
        $p = $p < 0 ? self::DEFAULT_QUANTITY_PRECISION : $p;
        return min($p, self::MAX_QUANTITY_INPUT_PRECISION);
    }

    /**
     * Get display round-off for a unit (for display only). Falls back to decimalPrecision then default.
     *
     * @param int|object|null $unitID
     * @return int
     */
    public function getUnitDisplayRoundOff($unitID): int
    {
        if ($unitID === null) {
            return self::DEFAULT_QUANTITY_PRECISION;
        }
        if (is_object($unitID)) {
            $d = isset($unitID->displayRoundOff) ? (int) $unitID->displayRoundOff : null;
            if ($d !== null && $d >= 0) {
                return $d;
            }
            $d = isset($unitID->decimalPrecision) ? (int) $unitID->decimalPrecision : null;
            return ($d !== null && $d >= 0) ? $d : self::DEFAULT_QUANTITY_PRECISION;
        }
        $unit = Unit::find($unitID);
        if (!$unit) {
            return self::DEFAULT_QUANTITY_PRECISION;
        }
        if ($unit->displayRoundOff !== null && $unit->displayRoundOff >= 0) {
            return (int) $unit->displayRoundOff;
        }
        if ($unit->decimalPrecision !== null && $unit->decimalPrecision >= 0) {
            return (int) $unit->decimalPrecision;
        }
        return self::DEFAULT_QUANTITY_PRECISION;
    }

    /**
     * Validate that a numeric value has <= allowedDecimals fractional digits.
     * Accepts string/float/int inputs. Scientific notation is normalised.
     *
     * @param mixed $value
     * @param int $allowedDecimals
     * @return bool
     */
    public function hasValidScale($value, int $allowedDecimals): bool
    {
        if ($allowedDecimals < 0) {
            return false;
        }
        if (!is_numeric($value)) {
            return false;
        }

        $s = is_string($value) ? trim($value) : (string) $value;
        if (stripos($s, 'e') !== false) {
            $s = rtrim(rtrim(sprintf('%.14F', (float) $value), '0'), '.');
        }

        $pos = strpos($s, '.');
        if ($pos === false) {
            return true;
        }

        $fraction = substr($s, $pos + 1);
        return strlen($fraction) <= $allowedDecimals;
    }

    /**
     * Round a quantity for UI/print display using unit displayRoundOff.
     *
     * @param float $qty
     * @param int|object|null $unitID
     * @return float
     */
    public function roundQuantityForDisplay(float $qty, $unitID): float
    {
        $precision = $this->getUnitDisplayRoundOff($unitID);
        return round($qty, $precision);
    }
}
