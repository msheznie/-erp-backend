<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="EmployeeDesignation",
 *      required={""},
 *      @SWG\Property(
 *          property="EmpDesignationID",
 *          description="EmpDesignationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="EmpID",
 *          description="EmpID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DesignationID",
 *          description="DesignationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="startDate",
 *          description="startDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="endDate",
 *          description="endDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="PrincipalCategoryID",
 *          description="PrincipalCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="SectionID",
 *          description="SectionID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DepartmentID",
 *          description="DepartmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isMajor",
 *          description="isMajor",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="SubjectID",
 *          description="SubjectID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ClassID",
 *          description="ClassID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="GroupID",
 *          description="GroupID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Erp_companyID",
 *          description="Erp_companyID",
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
 *          property="AcademicYearID",
 *          description="AcademicYearID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DateFrom",
 *          description="DateFrom",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="DateTo",
 *          description="DateTo",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
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
 *      ),
 *      @SWG\Property(
 *          property="DesignationTypeID",
 *          description="DesignationTypeID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class EmployeeDesignation extends Model
{

    public $table = 'srp_employeedesignation';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'EmpID',
        'DesignationID',
        'startDate',
        'endDate',
        'PrincipalCategoryID',
        'SectionID',
        'DepartmentID',
        'isMajor',
        'SubjectID',
        'ClassID',
        'GroupID',
        'Erp_companyID',
        'SchMasterID',
        'BranchID',
        'AcademicYearID',
        'DateFrom',
        'DateTo',
        'isActive',
        'CreatedUserName',
        'CreatedDate',
        'CreatedPC',
        'ModifiedUserName',
        'Timestamp',
        'ModifiedPC',
        'DesignationTypeID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'EmpDesignationID' => 'integer',
        'EmpID' => 'integer',
        'DesignationID' => 'integer',
        'startDate' => 'date',
        'endDate' => 'date',
        'PrincipalCategoryID' => 'integer',
        'SectionID' => 'integer',
        'DepartmentID' => 'integer',
        'isMajor' => 'integer',
        'SubjectID' => 'integer',
        'ClassID' => 'integer',
        'GroupID' => 'string',
        'Erp_companyID' => 'integer',
        'SchMasterID' => 'integer',
        'BranchID' => 'integer',
        'AcademicYearID' => 'integer',
        'DateFrom' => 'date',
        'DateTo' => 'date',
        'isActive' => 'integer',
        'CreatedUserName' => 'string',
        'CreatedDate' => 'datetime',
        'CreatedPC' => 'string',
        'ModifiedUserName' => 'string',
        'Timestamp' => 'datetime',
        'ModifiedPC' => 'string',
        'DesignationTypeID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function designation(){
        return $this->hasOne('App\Models\HrmsDesignation', 'DesignationID','DesignationID');
    }

    
}
