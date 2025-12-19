<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DocumentEmailNotificationDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="employeeSystemID",
 *          description="employeeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="sendYN",
 *          description="sendYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="emailNotificationID",
 *          description="emailNotificationID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class DocumentEmailNotificationDetail extends Model
{

    public $table = 'erp_documentemailnotificationdetail';

    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'id';

    public $fillable = [
        'companySystemID',
        'companyID',
        'employeeSystemID',
        'empID',
        'sendYN',
        'emailNotificationID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'employeeSystemID' => 'integer',
        'empID' => 'string',
        'sendYN' => 'integer',
        'emailNotificationID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function erpDocumentemailnotificationmaster()
    {
        return $this->belongsTo(\App\Models\ErpDocumentemailnotificationmaster::class);
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function policyCategory()
    {
        return $this->belongsTo('App\Models\DocumentEmailNotificationMaster', 'emailNotificationID', 'emailNotificationID');
    }

    public function employee_by()
    {
        return $this->belongsTo('App\Models\Employee', 'employeeSystemID', 'employeeSystemID');
    }
}
