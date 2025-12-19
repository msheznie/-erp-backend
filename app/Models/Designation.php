<?php
/**
 * =============================================
 * -- File Name : Designation.php
 * -- Project Name : ERP
 * -- Module Name : Designation
 * -- Author : Mohamed Fayas
 * -- Create date : 26- July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Designation",
 *      required={""},
 *      @SWG\Property(
 *          property="designationID",
 *          description="designationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="designation",
 *          description="designation",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="designation_O",
 *          description="designation_O",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="localName",
 *          description="localName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="jobCode",
 *          description="jobCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="jobDecipline",
 *          description="jobDecipline",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="businessFunction",
 *          description="businessFunction",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="appraisalTemplateID",
 *          description="appraisalTemplateID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPCid",
 *          description="createdPCid",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
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
 *      )
 * )
 */
class Designation extends Model
{

    public $table = 'hrms_designation';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'designationID';


    public $fillable = [
        'designation',
        'designation_O',
        'localName',
        'jobCode',
        'jobDecipline',
        'businessFunction',
        'appraisalTemplateID',
        'createdPCid',
        'createdUserID',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'designationID' => 'integer',
        'designation' => 'string',
        'designation_O' => 'string',
        'localName' => 'string',
        'jobCode' => 'string',
        'jobDecipline' => 'integer',
        'businessFunction' => 'integer',
        'appraisalTemplateID' => 'integer',
        'createdPCid' => 'string',
        'createdUserID' => 'string',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
