<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DocumentManagement",
 *      required={""},
 *      @SWG\Property(
 *          property="documentManagementID",
 *          description="documentManagementID",
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
 *          property="bigginingSerialNumber",
 *          description="bigginingSerialNumber",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="year",
 *          description="year",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeYearBigginingDate",
 *          description="financeYearBigginingDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="financeYearEndDate",
 *          description="financeYearEndDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="numberOfSerialNoDigits",
 *          description="numberOfSerialNoDigits",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="docRefNo",
 *          description="docRefNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timeStamp",
 *          description="timeStamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class DocumentManagement extends Model
{

    public $table = 'erp_documentmanagement';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'documentManagementID';


    public $fillable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'bigginingSerialNumber',
        'year',
        'companyFinanceYearID',
        'financeYearBigginingDate',
        'financeYearEndDate',
        'numberOfSerialNoDigits',
        'docRefNo',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'documentManagementID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'bigginingSerialNumber' => 'integer',
        'year' => 'string',
        'companyFinanceYearID' => 'integer',
        'financeYearBigginingDate' => 'datetime',
        'financeYearEndDate' => 'datetime',
        'numberOfSerialNoDigits' => 'integer',
        'docRefNo' => 'string',
        'timeStamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
       // 'documentManagementID' => 'required'
    ];

    
}
