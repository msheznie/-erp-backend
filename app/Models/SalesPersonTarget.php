<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SalesPersonTarget",
 *      required={""},
 *      @SWG\Property(
 *          property="targetID",
 *          description="targetID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="salesPersonID",
 *          description="salesPersonID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="datefrom",
 *          description="datefrom",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="dateTo",
 *          description="dateTo",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="currencyID",
 *          description="currencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="percentage",
 *          description="percentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="fromTargetAmount",
 *          description="fromTargetAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="toTargetAmount",
 *          description="toTargetAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
 *      )
 * )
 */
class SalesPersonTarget extends Model
{

    public $table = 'erp_salespersontarget';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'TIMESTAMP';

    protected $primaryKey = 'targetID';

    public $fillable = [
        'salesPersonID',
        'datefrom',
        'dateTo',
        'currencyID',
        'percentage',
        'fromTargetAmount',
        'toTargetAmount',
        'companySystemID',
        'companyID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'TIMESTAMP'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'targetID' => 'integer',
        'salesPersonID' => 'integer',
        'datefrom' => 'date',
        'dateTo' => 'date',
        'currencyID' => 'integer',
        'fromTargetAmount' => 'float',
        'toTargetAmount' => 'float',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
