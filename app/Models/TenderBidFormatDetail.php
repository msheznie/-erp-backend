<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent as Model;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @SWG\Definition(
 *      definition="TenderBidFormatDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tender_id",
 *          description="tender_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="label",
 *          description="label",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="field_type",
 *          description="field_type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="is_disabled",
 *          description="is_disabled",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class TenderBidFormatDetail extends Model
{
    use SoftDeletes;
    public $table = 'tender_bid_format_detail';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'tender_id',
        'label',
        'field_type',
        'is_disabled',
        'boq_applicable',
        'created_by',
        'updated_by',
        'deleted_by',
        'formula_string',
        'finalTotalYn'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'label' => 'string',
        'field_type' => 'integer',
        'is_disabled' => 'integer',
        'boq_applicable' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'formula_string' => 'string',
        'finalTotalYn' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function tender_field_type()
    {
        return $this->hasOne('App\Models\TenderFieldType', 'id', 'field_type');
    }

    public static function checkTenderBidFormatFormulaExists($price_bid_format_id, $field_type, $finalTotalYn){
        return self::where('tender_id', $price_bid_format_id)
            ->where('field_type',$field_type)
            ->where('finalTotalYn', $finalTotalYn)
            ->where('formula_string',"")
            ->count();
    }
    public static function getPricingBidFormatDetails($price_bid_format_id){
        return self::where('tender_id', $price_bid_format_id)
            ->select('id','field_type','tender_id','label','is_disabled','boq_applicable','formula_string')
            ->get();
    }
}
