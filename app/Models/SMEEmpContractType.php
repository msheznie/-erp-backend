<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SMEEmpContractType",
 *      required={""},
 *      @SWG\Property(
 *          property="EmpContractTypeID",
 *          description="EmpContractTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Description",
 *          description="Description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="typeID",
 *          description="srp_erp_systememployeetype => employeeTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="probation_period",
 *          description="probation_period",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="period",
 *          description="in month",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="is_open_contract",
 *          description="is_open_contract",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="SchMasterID",
 *          description="SchMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="BranchID",
 *          description="BranchID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Erp_CompanyID",
 *          description="Erp_CompanyID",
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
 *          property="Timestamp",
 *          description="Timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="ModifiedPC",
 *          description="ModifiedPC",
 *          type="string"
 *      )
 * )
 */
class SMEEmpContractType extends Model
{

    public $table = 'srp_empcontracttypes';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'Description',
        'typeID',
        'probation_period',
        'period',
        'is_open_contract',
        'SchMasterID',
        'BranchID',
        'Erp_CompanyID',
        'CreatedUserName',
        'CreatedDate',
        'CreatedPC',
        'ModifiedUserName',
        'Timestamp',
        'ModifiedPC'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'EmpContractTypeID' => 'integer',
        'Description' => 'string',
        'typeID' => 'integer',
        'probation_period' => 'integer',
        'period' => 'integer',
        'is_open_contract' => 'integer',
        'SchMasterID' => 'integer',
        'BranchID' => 'integer',
        'Erp_CompanyID' => 'integer',
        'CreatedUserName' => 'string',
        'CreatedDate' => 'datetime',
        'CreatedPC' => 'string',
        'ModifiedUserName' => 'string',
        'Timestamp' => 'datetime',
        'ModifiedPC' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Description' => 'required',
        'Timestamp' => 'required'
    ];

    function emp_contract(){
        return $this->hasMany(HREmpContractHistory::class, 'contactTypeID', 'EmpContractTypeID');
    }
    
}
