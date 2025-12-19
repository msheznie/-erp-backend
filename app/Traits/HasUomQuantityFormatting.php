<?php

namespace App\Traits;

use App\Models\Unit;

trait HasUomQuantityFormatting
{
    /**
     * Format quantity for saving based on UOM decimal precision
     *
     * @param float $quantity
     * @param int|null $unitOfMeasureId
     * @return float
     */
    public function formatQuantityForSaving($quantity, $unitOfMeasureId = null)
    {
        if (empty($quantity)) {
            return $quantity;
        }

        // First, try to use the loaded UOM relationship if available
        if ($this->relationLoaded('uom') && $this->uom && isset($this->uom->decimalPrecision)) {
                return round($quantity, $this->uom->decimalPrecision);
        }

        // Fallback to loading UOM by ID
        $uomId = $unitOfMeasureId ?? $this->getUnitOfMeasureId();
        
        if (empty($uomId)) {
            return $quantity;
        }

        try {
            $uom = Unit::find($uomId);
            return round($quantity, $uom && isset($uom->decimalPrecision) ? $uom->decimalPrecision : 5);
        } catch (\Exception $e) {
            // Log error if needed, but don't break the flow
        }

        return $quantity;
    }

    /**
     * Format quantity for display based on UOM display round off
     *
     * @param float $quantity
     * @param int|null $unitOfMeasureId
     * @return float
     */
    public function formatQuantityForDisplay($quantity, $unitOfMeasureId = null)
    {
        if (empty($quantity)) {
            return $quantity;
        }

        // First, try to use the loaded UOM relationship if available
        if ($this->relationLoaded('uom') && $this->uom && isset($this->uom->displayRoundOff)) {
                return round($quantity, $this->uom->displayRoundOff);
        }

        // Fallback to loading UOM by ID
        $uomId = $unitOfMeasureId ?? $this->getUnitOfMeasureId();
        
        if (empty($uomId)) {
            return $quantity;
        }

        try {
            $uom = Unit::find($uomId);
            return round($quantity, $uom && isset($uom->displayRoundOff) ? $uom->displayRoundOff : 5);
        } catch (\Exception $e) {
            // Log error if needed, but don't break the flow
        }

        return $quantity;
    }

    /**
     * Get the unit of measure ID from the model
     * This method should be overridden in models that use different field names
     *
     * @return int|null
     */
    protected function getUnitOfMeasureId()
    {
        // Default field name - can be overridden in models
        return $this->unitOfMeasure ?? null;
    }

    /**
     * Mutator for quantity fields that automatically formats based on UOM
     * Usage: Call this from your model's mutator methods
     *
     * @param string $attribute
     * @param mixed $value
     * @param int|null $unitOfMeasureId
     */
    protected function setQuantityAttribute($attribute, $value, $unitOfMeasureId = null)
    {
        $this->attributes[$attribute] = empty($value) ? $value : $this->formatQuantityForSaving($value, $unitOfMeasureId);
    }

    /**
     * Accessor for quantity fields that automatically formats for display
     * Usage: Call this from your model's accessor methods
     *
     * @param mixed $value
     * @param int|null $unitOfMeasureId
     * @return mixed
     */
    protected function getQuantityAttribute($value, $unitOfMeasureId = null)
    {
        return empty($value) ? $value : $this->formatQuantityForDisplay($value, $unitOfMeasureId);
    }

    /**
     * Format multiple quantities at once for saving
     *
     * @param array $quantities Array of ['field_name' => value] pairs
     * @param int|null $unitOfMeasureId
     * @return array
     */
    public function formatQuantitiesForSaving(array $quantities, $unitOfMeasureId = null)
    {
        $formatted = [];
        foreach ($quantities as $field => $value) {
            $formatted[$field] = empty($value) ? $value : $this->formatQuantityForSaving($value, $unitOfMeasureId);
        }
        return $formatted;
    }

    /**
     * Format multiple quantities at once for display
     *
     * @param array $quantities Array of ['field_name' => value] pairs
     * @param int|null $unitOfMeasureId
     * @return array
     */
    public function formatQuantitiesForDisplay(array $quantities, $unitOfMeasureId = null)
    {
        $formatted = [];
        foreach ($quantities as $field => $value) {
            $formatted[$field] = empty($value) ? $value : $this->formatQuantityForDisplay($value, $unitOfMeasureId);
        }
        return $formatted;
    }
}
