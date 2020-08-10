<?php
/**
 * =============================================
 * -- File Name : AuditTrail.php
 * -- Project Name : ERP
 * -- Module Name :  Audit Trail
 * -- Author : Fayas
 * -- Create date : 22 - October 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AuditTrail",
 *      required={""},
 *      @SWG\Property(
 *          property="auditTrailAutoID",
 *          description="auditTrailAutoID",
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
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="valueFrom",
 *          description="valueFrom",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="valueTo",
 *          description="valueTo",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="valueFromSystemID",
 *          description="valueFromSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="valueFromText",
 *          description="valueFromText",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="valueToSystemID",
 *          description="valueToSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="valueToText",
 *          description="valueToText",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      )
 * )
 */
class AuditTrail extends Model
{

    public $table = 'erp_audittrail';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'modifiedDate';

    protected $primaryKey = 'auditTrailAutoID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'valueFrom',
        'valueTo',
        'valueFromSystemID',
        'valueFromText',
        'valueToSystemID',
        'valueToText',
        'description',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDate',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'auditTrailAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'valueFrom' => 'float',
        'valueTo' => 'float',
        'valueFromSystemID' => 'integer',
        'valueFromText' => 'string',
        'valueToSystemID' => 'integer',
        'valueToText' => 'string',
        'description' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUserID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function modified_by(){
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    
}
