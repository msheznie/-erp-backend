<?php
/**
 * =============================================
 * -- File Name : SalaryProcessEmploymentTypes.php
 * -- Project Name : ERP
 * -- Module Name : Salary Process Employment Types
 * -- Author : Mohamed Fayas
 * -- Create date : 07 - November 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SalaryProcessEmploymentTypes",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="salaryProcessID",
 *          description="salaryProcessID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empType",
 *          description="empType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="periodID",
 *          description="periodID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      )
 * )
 */
class SalaryProcessEmploymentTypes extends Model
{

    public $table = 'hrms_salaryprocess_employment_types';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';



    public $fillable = [
        'salaryProcessID',
        'empType',
        'periodID',
        'companySystemID',
        'companyID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'salaryProcessID' => 'integer',
        'empType' => 'integer',
        'periodID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function salary_process(){
        return $this->belongsTo('App\Models\SalaryProcessMaster','salaryProcessID','salaryProcessMasterID');
    }
}
