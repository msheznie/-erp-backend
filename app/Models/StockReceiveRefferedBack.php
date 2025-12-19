<?php
/**
 * =============================================
 * -- File Name : StockReceiveRefferedBack.php
 * -- Project Name : ERP
 * -- Module Name : Stock Receive Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 23 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StockReceiveRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="stockReceiveRefferedID",
 *          description="stockReceiveRefferedID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockReceiveAutoID",
 *          description="stockReceiveAutoID",
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
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockReceiveCode",
 *          description="stockReceiveCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="refNo",
 *          description="refNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyFromSystemID",
 *          description="companyFromSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyFrom",
 *          description="companyFrom",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyToSystemID",
 *          description="companyToSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyTo",
 *          description="companyTo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="locationTo",
 *          description="locationTo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="locationFrom",
 *          description="locationFrom",
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
 *          property="approved",
 *          description="approved",
 *          type="integer",
 *          format="int32"
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
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="interCompanyTransferYN",
 *          description="interCompanyTransferYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
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
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class StockReceiveRefferedBack extends Model
{

    public $table = 'erp_stockreceiverefferedback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'stockReceiveRefferedID';


    public $fillable = [
        'stockReceiveAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'documentSystemID',
        'documentID',
        'serialNo',
        'stockReceiveCode',
        'refNo',
        'receivedDate',
        'comment',
        'companyFromSystemID',
        'companyFrom',
        'companyToSystemID',
        'companyTo',
        'locationTo',
        'locationFrom',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'postedDate',
        'timesReferred',
        'interCompanyTransferYN',
        'RollLevForApp_curr',
        'createdDateTime',
        'createdUserGroup',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp',
        'refferedBackYN',
         'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'stockReceiveRefferedID' => 'integer',
        'stockReceiveAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'stockReceiveCode' => 'string',
        'refNo' => 'string',
        'comment' => 'string',
        'companyFromSystemID' => 'integer',
        'companyFrom' => 'string',
        'companyToSystemID' => 'integer',
        'companyTo' => 'string',
        'locationTo' => 'integer',
        'locationFrom' => 'integer',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'approved' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'timesReferred' => 'integer',
        'interCompanyTransferYN' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'createdUserGroup' => 'string',
        'createdPCID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'refferedBackYN' => 'integer'
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

    public function location_to_by()
    {
        return $this->belongsTo('App\Models\WarehouseMaster', 'locationTo', 'wareHouseSystemCode');
    }

    public function location_from_by()
    {
        return $this->belongsTo('App\Models\WarehouseMaster', 'locationFrom', 'wareHouseSystemCode');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'stockReceiveAutoID');
    }

    public function details()
    {
        return $this->hasMany('App\Models\StockReceiveDetails', 'stockReceiveAutoID', 'stockReceiveAutoID');
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
