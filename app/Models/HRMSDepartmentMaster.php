<?php
/**
 * =============================================
 * -- File Name : HRMSDepartmentMaster.php
 * -- Project Name : ERP
 * -- Module Name :  HRMS Department Master
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - November 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HRMSDepartmentMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="DepartmentID",
 *          description="DepartmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DepartmentDescription",
 *          description="DepartmentDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ServiceLineCode",
 *          description="ServiceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="CompanyID",
 *          description="CompanyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="showInCombo",
 *          description="showInCombo",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class HRMSDepartmentMaster extends Model
{

    public $table = 'hrms_departmentmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'DepartmentID';


    public $fillable = [
        'DepartmentDescription',
        'isActive',
        'ServiceLineCode',
        'CompanyID',
        'showInCombo',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'DepartmentID' => 'integer',
        'DepartmentDescription' => 'string',
        'isActive' => 'integer',
        'ServiceLineCode' => 'string',
        'CompanyID' => 'string',
        'showInCombo' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
