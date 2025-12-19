<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="FixedAssetInsuranceDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="insuranceDetailsID",
 *          description="insuranceDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="faID",
 *          description="faID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="insuredYN",
 *          description="insuredYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="policy",
 *          description="policy",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="policyNumber",
 *          description="policyNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="insuredValue",
 *          description="insuredValue",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="insurerName",
 *          description="insurerName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="locationID",
 *          description="locationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="buildingNumber",
 *          description="buildingNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="openClosedArea",
 *          description="openClosedArea",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="containerNumber",
 *          description="containerNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="movingItem",
 *          description="movingItem",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdByUserID",
 *          description="createdByUserID",
 *          type="string"
 *      )
 * )
 */
class FixedAssetInsuranceDetail extends Model
{

    public $table = 'erp_fa_insurancedetails';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'insuranceDetailsID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'faID',
        'insuredYN',
        'policy',
        'policyNumber',
        'dateOfInsurance',
        'dateOfExpiry',
        'insuredValue',
        'insurerName',
        'locationID',
        'buildingNumber',
        'openClosedArea',
        'containerNumber',
        'movingItem',
        'createdUserSystemID',
        'createdByUserID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'insuranceDetailsID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'faID' => 'integer',
        'insuredYN' => 'integer',
        'policy' => 'integer',
        'policyNumber' => 'string',
        'insuredValue' => 'float',
        'insurerName' => 'string',
        'locationID' => 'integer',
        'buildingNumber' => 'string',
        'openClosedArea' => 'integer',
        'containerNumber' => 'string',
        'movingItem' => 'integer',
        'createdUserSystemID' => 'integer',
        'createdByUserID' => 'string'
    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfAsset($query, $faID)
    {
        return $query->where('faID',  $faID);
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function policy_by()
    {
        return $this->belongsTo('App\Models\InsurancePolicyType', 'policy', 'insurancePolicyTypesID');
    }

    public function location_by()
    {
        return $this->belongsTo('App\Models\Location', 'locationID', 'locationID');
    }

    
}
