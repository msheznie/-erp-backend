<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SMENationality",
 *      required={""},
 *      @SWG\Property(
 *          property="NId",
 *          description="NId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Nationality",
 *          description="Nationality",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="NationalityAr",
 *          description="NationalityAr",
 *          type="string"
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
 *          property="Erp_companyID",
 *          description="Erp_companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="countryID",
 *          description="countryID",
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
class SMENationality extends Model
{

    public $table = 'srp_nationality';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'Nationality',
        'NationalityAr',
        'SchMasterID',
        'BranchID',
        'Erp_companyID',
        'countryID',
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
        'NId' => 'integer',
        'Nationality' => 'string',
        'NationalityAr' => 'string',
        'SchMasterID' => 'integer',
        'BranchID' => 'integer',
        'Erp_companyID' => 'integer',
        'countryID' => 'integer',
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
        'Timestamp' => 'required'
    ];

    
}
