<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ErpProjectMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="projectCode",
 *          description="projectCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="projectCurrencyID",
 *          description="projectCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyID",
 *          description="companyLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyRptCurrencyID",
 *          description="companyRptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="estimatedAmount",
 *          description="estimatedAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="estimatedLocalAmount",
 *          description="estimatedLocalAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="estimatedRptAmount",
 *          description="estimatedRptAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="start_date",
 *          description="start_date",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="end_date",
 *          description="end_date",
 *          type="string",
 *          format="date"
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
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class ErpProjectMaster extends Model
{

    public $table = 'erp_projectmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';




    public $fillable = [
        'projectCode',
        'description',
        'companyID',
        'companySystemID',
        'serviceLineSystemID',
        'serviceLineCode',
        'projectCurrencyID',
        'companyLocalCurrencyID',
        'companyRptCurrencyID',
        'estimatedAmount',
        'estimatedLocalAmount',
        'estimatedRptAmount',
        'start_date',
        'end_date',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'projectCode' => 'string',
        'description' => 'string',
        'companyID' => 'string',
        'companySystemID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'projectCurrencyID' => 'integer',
        'companyLocalCurrencyID' => 'integer',
        'companyRptCurrencyID' => 'integer',
        'estimatedAmount' => 'float',
        'estimatedLocalAmount' => 'float',
        'estimatedRptAmount' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'integer',
        'createdUserID' => 'integer',
        'createdDateTime' => 'datetime',
        'createdUserName' => 'string',
        'modifiedPCID' => 'integer',
        'modifiedUserID' => 'integer',
        'modifiedDateTime' => 'datetime',
        'modifiedUserName' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }
    public function currency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'projectCurrencyID', 'currencyID');
    }

    public function service_line(){
        return $this->belongsTo('App\Models\ServiceLine', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function glEstimatedAmount(){
        return $this->belongsTo('App\Models\ProjectGlDetail', 'id', 'projectID');
    }

    public function gl_details(){
        return $this->hasMany('App\Models\ProjectGlDetail', 'projectID', 'id');
    }
}
