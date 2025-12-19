<?php
/**
 * =============================================
 * -- File Name : ItemIssueMasterRefferedBack.php
 * -- Project Name : ERP
 * -- Module Name :  Item Issue Master Referred Back
 * -- Author : Mohamed Fayas
 * -- Create date : 03- December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ItemIssueMasterRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="itemIssueAutoRefferedbackID",
 *          description="itemIssueAutoRefferedbackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemIssueAutoID",
 *          description="itemIssueAutoID",
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
 *          property="itemIssueCode",
 *          description="itemIssueCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="issueType",
 *          description="issueType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseFrom",
 *          description="wareHouseFrom",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseFromCode",
 *          description="wareHouseFromCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseFromDes",
 *          description="wareHouseFromDes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contractUIID",
 *          description="contractUIID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contractID",
 *          description="contractID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="jobNo",
 *          description="jobNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="workOrderNo",
 *          description="workOrderNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="purchaseOrderNo",
 *          description="purchaseOrderNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="networkNo",
 *          description="networkNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerSystemID",
 *          description="customerSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="issueRefNo",
 *          description="issueRefNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="reqDocID",
 *          description="reqDocID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="reqByID",
 *          description="reqByID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="reqByName",
 *          description="reqByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="reqComment",
 *          description="reqComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wellLocationFieldID",
 *          description="wellLocationFieldID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fieldShortCode",
 *          description="fieldShortCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fieldName",
 *          description="fieldName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wellNO",
 *          description="wellNO",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
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
 *          property="directReqByID",
 *          description="directReqByID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="directReqByName",
 *          description="directReqByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="product",
 *          description="product",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="volume",
 *          description="volume",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="strength",
 *          description="strength",
 *          type="string"
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
 *          property="contRefNo",
 *          description="contRefNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="is_closed",
 *          description="is_closed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ItemIssueMasterRefferedBack extends Model
{

    public $table = 'erp_itemissuemaster_refferedback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'itemIssueAutoRefferedbackID';


    public $fillable = [
        'itemIssueAutoID',
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
        'itemIssueCode',
        'issueType',
        'issueDate',
        'wareHouseFrom',
        'wareHouseFromCode',
        'wareHouseFromDes',
        'contractUIID',
        'contractID',
        'jobNo',
        'workOrderNo',
        'purchaseOrderNo',
        'networkNo',
        'itemDeliveredOnSiteDate',
        'customerSystemID',
        'customerID',
        'issueRefNo',
        'reqDocID',
        'reqByID',
        'reqByName',
        'reqDate',
        'reqComment',
        'wellLocationFieldID',
        'fieldShortCode',
        'fieldName',
        'wellNO',
        'comment',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'directReqByID',
        'directReqByName',
        'product',
        'volume',
        'strength',
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
        'contRefNo',
        'is_closed',
        'RollLevForApp_curr',
        'timestamp',
        'mfqJobID',
        'mfqJobNo',
        'counter',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'itemIssueAutoRefferedbackID' => 'integer',
        'itemIssueAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'itemIssueCode' => 'string',
        'issueType' => 'integer',
        'wareHouseFrom' => 'integer',
        'wareHouseFromCode' => 'string',
        'wareHouseFromDes' => 'string',
        'contractUIID' => 'integer',
        'contractID' => 'string',
        'jobNo' => 'integer',
        'workOrderNo' => 'string',
        'purchaseOrderNo' => 'string',
        'networkNo' => 'string',
        'customerSystemID' => 'integer',
        'customerID' => 'string',
        'issueRefNo' => 'string',
        'reqDocID' => 'integer',
        'reqByID' => 'string',
        'reqByName' => 'string',
        'reqComment' => 'string',
        'wellLocationFieldID' => 'integer',
        'fieldShortCode' => 'string',
        'fieldName' => 'string',
        'wellNO' => 'string',
        'comment' => 'string',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'approved' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'directReqByID' => 'string',
        'directReqByName' => 'string',
        'product' => 'string',
        'volume' => 'string',
        'strength' => 'string',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'createdUserGroup' => 'string',
        'createdPCid' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'contRefNo' => 'string',
        'is_closed' => 'integer',
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

    public function customer_by()
    {
        return $this->belongsTo('App\Models\CustomerMaster', 'customerSystemID', 'customerCodeSystem');
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
        return $this->belongsTo('App\Models\WarehouseMaster','wareHouseFrom','wareHouseSystemCode');
    }

    public function details()
    {
        return $this->hasMany('App\Models\ItemIssueDetails','itemIssueAutoID','itemIssueAutoID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','itemIssueAutoID');
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
