<?php
/**
 * =============================================
 * -- File Name : StockAdjustmentReasonTranslation.php
 * -- Project Name : ERP
 * -- Module Name : Stock Adjustment Reason Translation
 * -- Author : System
 * -- Create date : 12 - September 2025
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StockAdjustmentReasonTranslation",
 *      required={"reasonID", "languageCode", "reason"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="reasonID",
 *          description="reasonID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="languageCode",
 *          description="languageCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="reason",
 *          description="reason",
 *          type="string"
 *      )
 * )
 */
class StockAdjustmentReasonTranslation extends Model
{
    public $table = 'stockadjustment_reasons_translation';
    
    public $timestamps = true;

    public $fillable = [
        'reasonID',
        'languageCode',
        'reason'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'reasonID' => 'integer',
        'languageCode' => 'string',
        'reason' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'reasonID' => 'required|integer|exists:stockadjustment_reasons,id',
        'languageCode' => 'required|string|max:10',
        'reason' => 'required|string|max:255'
    ];

    /**
     * Get the stock adjustment reason that owns the translation.
     */
    public function stockAdjustmentReason()
    {
        return $this->belongsTo(StockAdjustmentReason::class, 'reasonID', 'id');
    }
}
