<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CashFlowTemplate",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="1-indirect method, 2-other",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="presentationType",
 *          description="presentationType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="showNumbersIn",
 *          description="showNumbersIn",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="showDecimalPlaceYN",
 *          description="showDecimalPlaceYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="showZeroGlYN",
 *          description="showZeroGlYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
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
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class CashFlowTemplate extends Model
{

    public $table = 'cash_flow_templates';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'description',
        'type',
        'companySystemID',
        'isActive',
        'presentationType',
        'showNumbersIn',
        'showDecimalPlaceYN',
        'showZeroGlYN',
        'createdPCID',
        'createdUserSystemID',
        'modifiedPCID',
        'modifiedUserSystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'description' => 'string',
        'type' => 'integer',
        'companySystemID' => 'integer',
        'isActive' => 'integer',
        'presentationType' => 'integer',
        'showNumbersIn' => 'integer',
        'showDecimalPlaceYN' => 'integer',
        'showZeroGlYN' => 'integer',
        'createdPCID' => 'string',
        'createdUserSystemID' => 'integer',
        'modifiedPCID' => 'string',
        'modifiedUserSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function scopeOfCompany($query, $type)
    {
        return $query->where('companySystemID',  $type);
    }
    
}
