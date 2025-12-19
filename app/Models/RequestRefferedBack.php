<?php
/**
 * =============================================
 * -- File Name : RequestRefferedBack.php
 * -- Project Name : ERP
 * -- Module Name : Request Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 06- December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="RequestRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="RequestRefferedBackID",
 *          description="RequestRefferedBackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="RequestID",
 *          description="RequestID",
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
 *          property="departmentSystemID",
 *          description="departmentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="departmentID",
 *          description="departmentID",
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
 *          property="companyJobID",
 *          description="companyJobID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="jobDescription",
 *          description="jobDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNumber",
 *          description="serialNumber",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="RequestCode",
 *          description="RequestCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="location",
 *          description="location",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="priority",
 *          description="priority",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deliveryLocation",
 *          description="deliveryLocation",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ConfirmedYN",
 *          description="ConfirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ConfirmedBySystemID",
 *          description="ConfirmedBySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ConfirmedBy",
 *          description="ConfirmedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedEmpName",
 *          description="confirmedEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="quantityOnOrder",
 *          description="quantityOnOrder",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="quantityInHand",
 *          description="quantityInHand",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="selectedForIssue",
 *          description="selectedForIssue",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approved",
 *          description="approved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ClosedYN",
 *          description="ClosedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="issueTrackID",
 *          description="issueTrackID",
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
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
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
 *          property="modifiedPc",
 *          description="modifiedPc",
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
 *      )
 * )
 */
class RequestRefferedBack extends Model
{

    public $table = 'erp_request_refferedback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'RequestRefferedBackID';



    public $fillable = [
        'RequestID',
        'companySystemID',
        'companyID',
        'departmentSystemID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'companyJobID',
        'jobDescription',
        'serialNumber',
        'RequestCode',
        'comments',
        'location',
        'priority',
        'deliveryLocation',
        'RequestedDate',
        'ConfirmedYN',
        'ConfirmedBySystemID',
        'ConfirmedBy',
        'confirmedEmpName',
        'ConfirmedDate',
        'isActive',
        'quantityOnOrder',
        'quantityInHand',
        'selectedForIssue',
        'approved',
        'ClosedYN',
        'issueTrackID',
        'timeStamp',
        'RollLevForApp_curr',
        'approvedDate',
        'approvedByUserSystemID',
        'refferedBackYN',
        'timesReferred',
        'createdUserGroup',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'counter',
        'isDelegation',
        'excelRowCount',
        'successDetailsCount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'RequestRefferedBackID' => 'integer',
        'RequestID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'departmentSystemID' => 'integer',
        'departmentID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companyJobID' => 'integer',
        'jobDescription' => 'string',
        'serialNumber' => 'integer',
        'RequestCode' => 'string',
        'comments' => 'string',
        'location' => 'integer',
        'priority' => 'integer',
        'deliveryLocation' => 'integer',
        'ConfirmedYN' => 'integer',
        'ConfirmedBySystemID' => 'integer',
        'ConfirmedBy' => 'string',
        'confirmedEmpName' => 'string',
        'isActive' => 'integer',
        'quantityOnOrder' => 'float',
        'quantityInHand' => 'float',
        'selectedForIssue' => 'integer',
        'approved' => 'integer',
        'ClosedYN' => 'integer',
        'issueTrackID' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'approvedByUserSystemID' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function created_by(){
        return $this->belongsTo('App\Models\Employee','createdUserSystemID','employeeSystemID');
    }

    public function priority_by(){
        return $this->belongsTo('App\Models\Priority','priority','priorityID');
    }
    public function location_by(){
        return $this->belongsTo('App\Models\Location','location','locationID');
    }

    public function warehouse_by(){
        return $this->belongsTo('App\Models\WarehouseMaster','location','wareHouseSystemCode');
    }

    public function segment_by(){
        return $this->belongsTo('App\Models\SegmentMaster','serviceLineSystemID','serviceLineSystemID');
    }

    public function confirmed_by(){
        return $this->belongsTo('App\Models\Employee','ConfirmedBySystemID','employeeSystemID');
    }

    public function modified_by(){
        return $this->belongsTo('App\Models\Employee','modifiedUserSystemID','employeeSystemID');
    }

    public function details(){
        return $this->hasMany('App\Models\MaterielRequestDetails','RequestID','RequestID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','RequestID');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }
}
