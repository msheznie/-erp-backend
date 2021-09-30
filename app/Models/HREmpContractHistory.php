<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HREmpContractHistory",
 *      required={""},
 *      @SWG\Property(
 *          property="contractID",
 *          description="contractID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contactTypeID",
 *          description="contactTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contractStartDate",
 *          description="contractStartDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="contractEndDate",
 *          description="contractEndDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="contractRefNo",
 *          description="contractRefNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isCurrent",
 *          description="isCurrent",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="previousContractID",
 *          description="previousContractID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="CreatedUserName",
 *          description="CreatedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="CreatedDate",
 *          description="CreatedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="CreatedPC",
 *          description="CreatedPC",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ModifiedUserName",
 *          description="ModifiedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ModifiedPC",
 *          description="ModifiedPC",
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
class HREmpContractHistory extends Model
{

    public $table = 'srp_erp_empcontracthistory';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'empID',
        'contactTypeID',
        'companyID',
        'contractStartDate',
        'contractEndDate',
        'contractRefNo',
        'isCurrent',
        'previousContractID',
        'CreatedUserName',
        'CreatedDate',
        'CreatedPC',
        'ModifiedUserName',
        'ModifiedPC',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'contractID' => 'integer',
        'empID' => 'integer',
        'contactTypeID' => 'integer',
        'companyID' => 'integer',
        'contractStartDate' => 'date',
        'contractEndDate' => 'date',
        'contractRefNo' => 'string',
        'isCurrent' => 'integer',
        'previousContractID' => 'integer',
        'CreatedUserName' => 'string',
        'CreatedDate' => 'datetime',
        'CreatedPC' => 'string',
        'ModifiedUserName' => 'string',
        'ModifiedPC' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'empID' => 'required',
        'companyID' => 'required',
        'contractStartDate' => 'required'
    ];

    function contract_type(){
        return $this->belongsTo(SMEEmpContractType::class, 'contactTypeID', 'EmpContractTypeID');
    }

    function employee(){
        return $this->belongsTo(SrpEmployeeDetails::class, 'empID', 'EIdNo');
    }
    
}
