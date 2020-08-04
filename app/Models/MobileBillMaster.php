<?php
/**
 * =============================================
 * -- File Name : MobileBillMaster.php
 * -- Project Name : ERP
 * -- Module Name : MobileBillMaster
 * -- Author : Mohamed Rilwan
 * -- Create date : 12- July 2020
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MobileBillMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="mobilebillMasterID",
 *          description="mobilebillMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="billPeriod",
 *          description="billPeriod",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mobilebillmasterCode",
 *          description="mobilebillmasterCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
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
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Description",
 *          description="Description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createDate",
 *          description="createDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedDate",
 *          description="confirmedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="confirmedby",
 *          description="confirmedby",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmployeeSystemID",
 *          description="confirmedByEmployeeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedby",
 *          description="approvedby",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedbyEmployeeSystemID",
 *          description="approvedbyEmployeeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ApprovedYN",
 *          description="ApprovedYN",
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
 *          property="createUserID",
 *          description="createUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createPCID",
 *          description="createPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedpc",
 *          description="modifiedpc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class MobileBillMaster extends Model
{

    public $table = 'hrms_mobilebillmaster';
    
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'mobilebillMasterID';


    public $fillable = [
        'billPeriod',
        'mobilebillmasterCode',
        'serialNo',
        'documentSystemID',
        'documentID',
        'companyID',
        'Description',
        'createDate',
        'confirmedYN',
        'confirmedDate',
        'confirmedby',
        'confirmedByEmployeeSystemID',
        'approvedby',
        'approvedbyEmployeeSystemID',
        'ApprovedYN',
        'approvedDate',
        'createUserID',
        'createPCID',
        'modifiedpc',
        'modifiedUser',
        'modifiedUserSystemID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'mobilebillMasterID' => 'integer',
        'billPeriod' => 'integer',
        'mobilebillmasterCode' => 'string',
        'serialNo' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companyID' => 'string',
        'Description' => 'string',
        'createDate' => 'datetime',
        'confirmedYN' => 'integer',
        'confirmedDate' => 'datetime',
        'confirmedby' => 'string',
        'confirmedByEmployeeSystemID' => 'integer',
        'approvedby' => 'string',
        'approvedbyEmployeeSystemID' => 'integer',
        'ApprovedYN' => 'integer',
        'approvedDate' => 'datetime',
        'createUserID' => 'string',
        'createPCID' => 'string',
        'modifiedpc' => 'string',
        'modifiedUser' => 'string',
        'modifiedUserSystemID' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function period(){
        return $this->belongsTo('App\Models\PeriodMaster', 'billPeriod','periodMasterID');
    }

    public function summary(){
        return $this->hasMany('App\Models\MobileBillSummary', 'mobileMasterID','mobilebillMasterID');
    }

    public function confirmed_by(){
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmployeeSystemID','employeeSystemID');
    }

    public function approved_by(){
        return $this->belongsTo('App\Models\Employee', 'approvedbyEmployeeSystemID','employeeSystemID');
    }

    public function detail(){
        return $this->hasMany('App\Models\MobileDetail', 'mobilebillMasterID','mobilebillMasterID');
    }

    public function employee_mobile(){
        return $this->hasMany('App\Models\EmployeeMobileBillMaster', 'mobilebillMasterID','mobilebillMasterID');
    }


    
}
