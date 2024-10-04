<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StockCountRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="stockCountAutoID",
 *          description="stockCountAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockCountAutoRefferedbackID",
 *          description="stockCountAutoRefferedbackID",
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
 *          property="companyID",
 *          description="companyID",
 *          type="string"
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
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyFinancePeriodID",
 *          description="companyFinancePeriodID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="FYBiggin",
 *          description="FYBiggin",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="FYEnd",
 *          description="FYEnd",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockCountCode",
 *          description="stockCountCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="refNo",
 *          description="refNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="stockCountDate",
 *          description="stockCountDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="location",
 *          description="location",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="stockCountType",
 *          description="1 - qty adjustment, 2 - WAC adjustment",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpSystemID",
 *          description="confirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpID",
 *          description="confirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByName",
 *          description="confirmedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedDate",
 *          description="confirmedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="approved",
 *          description="approved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedDate",
 *          description="approvedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserID",
 *          description="approvedByUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserSystemID",
 *          description="approvedByUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
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
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPCid",
 *          description="createdPCid",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class StockCountRefferedBack extends Model
{

    public $table = 'erp_stockcount_refferedback';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = "stockCountAutoRefferedbackID";


    public $fillable = [
        'stockCountAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'serialNo',
        'stockCountCode',
        'refNo',
        'stockCountDate',
        'location',
        'comment',
        'stockCountType',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'refferedBackYN',
        'timesReferred',
        'createdDateTime',
        'createdUserGroup',
        'createdPCid',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp',
        'RollLevForApp_curr',
        'counter',
        'detailStatus'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'stockCountAutoID' => 'integer',
        'stockCountAutoRefferedbackID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'FYBiggin' => 'datetime',
        'FYEnd' => 'datetime',
        'serialNo' => 'integer',
        'stockCountCode' => 'string',
        'refNo' => 'string',
        'stockCountDate' => 'datetime',
        'location' => 'integer',
        'comment' => 'string',
        'stockCountType' => 'integer',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'confirmedDate' => 'datetime',
        'approved' => 'integer',
        'approvedDate' => 'datetime',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'createdDateTime' => 'datetime',
        'createdUserGroup' => 'string',
        'createdPCid' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'timestamp' => 'datetime',
        'RollLevForApp_curr' => 'integer'
    ];

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

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }


    public function segment_by()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function warehouse_by()
    {
        return $this->belongsTo('App\Models\WarehouseMaster','location','wareHouseSystemCode');
    }

    public function details()
    {
        return $this->hasMany('App\Models\StockCountDetail','stockCountAutoID','stockCountAutoID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','stockCountAutoID');
    }
    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function finance_period_by()
    {
        return $this->belongsTo('App\Models\CompanyFinancePeriod', 'companyFinancePeriodID', 'companyFinancePeriodID');
    }

    public function finance_year_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }
}
