<?php
/**
 * =============================================
 * -- File Name : ItemIssueMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Item Issue Master
 * -- Author : Mohamed Fayas
 * -- Create date : 20- June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use App\helper\Helper;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ItemIssueMaster",
 *      required={""},
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
 *      )
 * )
 */
class ItemIssueMaster extends Model
{
    use Compoships;
    public $table = 'erp_itemissuemaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'itemIssueAutoID';


    public $fillable = [
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
        'contractID',
        'contractUIID',
        'jobNo',
        'workOrderNo',
        'purchaseOrderNo',
        'networkNo',
        'itemDeliveredOnSiteDate',
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
        'directReqByID',
        'directReqByName',
        'product',
        'volume',
        'strength',
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
        'timestamp',
        'customerSystemID',
        'approvedDate',
        'RollLevForApp_curr',
        'approvedByUserID',
        'approvedByUserSystemID',
        'refferedBackYN',
        'timesReferred',
        'mfqJobID',
        'mfqJobNo',
        'postedDate',
        'counter'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
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
        'contractID' => 'string',
        'contractUIID' => 'integer',
        'jobNo' => 'integer',
        'workOrderNo' => 'string',
        'purchaseOrderNo' => 'string',
        'networkNo' => 'string',
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
        'directReqByID' => 'string',
        'directReqByName' => 'string',
        'product' => 'string',
        'volume' => 'string',
        'strength' => 'string',
        'createdUserGroup' => 'string',
        'createdPCid' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'contRefNo' => 'string',
        'is_closed' => 'integer',
        'customerSystemID' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
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

    public function setIssueDateAttribute($value)
    {
        $this->attributes['issueDate'] = Helper::dateAddTime($value);
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'itemIssueAutoID')->where('documentSystemID',8);
    }

    
    public function scopeDetailJoin($q)
    {
        return $q->join('erp_itemissuedetails','erp_itemissuedetails.itemIssueAutoID','erp_itemissuemaster.itemIssueAutoID');
    }

    public function scopeCustomerJoin($q,$as = 'customermaster', $column = 'customerID' , $columnAs = 'CustomerName')
    {
        return $q->leftJoin('customermaster as '.$as,$as.'.customerCodeSystem','erp_itemissuemaster.'.$column)
        ->addSelect($as.".CustomerName as ".$columnAs);
    }

    
    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_itemissuemaster.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_itemissuemaster.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }
        
    public function scopeWareHouseJoin($q,$as = 'warehousemaster', $column = 'wareHouseSystemCode' , $columnAs = 'wareHouseDescription')
    {
        return $q->leftJoin('warehousemaster as '.$as,$as.'.wareHouseSystemCode','erp_itemissuemaster.'.$column)
        ->addSelect($as.".wareHouseDescription as ".$columnAs);
    }

}
