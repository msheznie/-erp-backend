<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HRMSJvDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="accruvalDetID",
 *          description="accruvalDetID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accMasterID",
 *          description="accMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="salaryProcessMasterID",
 *          description="salaryProcessMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accrualNarration",
 *          description="accrualNarration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLine",
 *          description="serviceLine",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="GlCode",
 *          description="GlCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="accrualAmount",
 *          description="accrualAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="accrualCurrency",
 *          description="accrualCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localAmount",
 *          description="localAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localCurrency",
 *          description="localCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rptAmount",
 *          description="rptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="rptCurrency",
 *          description="rptCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="jvMasterAutoID",
 *          description="jvMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class HRMSJvDetails extends Model
{

    public $table = 'hrms_jvdetails';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'accruvalDetID';

    public $fillable = [
        'accMasterID',
        'salaryProcessMasterID',
        'accrualNarration',
        'accrualDateAsOF',
        'companyID',
        'serviceLine',
        'departureDate',
        'callOfDate',
        'GlCode',
        'accrualAmount',
        'accrualCurrency',
        'localAmount',
        'localCurrency',
        'rptAmount',
        'rptCurrency',
        'jvMasterAutoID',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'accruvalDetID' => 'integer',
        'accMasterID' => 'integer',
        'salaryProcessMasterID' => 'integer',
        'accrualNarration' => 'string',
        'companyID' => 'string',
        'serviceLine' => 'string',
        'GlCode' => 'string',
        'accrualAmount' => 'float',
        'accrualCurrency' => 'integer',
        'localAmount' => 'float',
        'localCurrency' => 'integer',
        'rptAmount' => 'float',
        'rptCurrency' => 'integer',
        'jvMasterAutoID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
