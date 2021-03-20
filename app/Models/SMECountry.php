<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SMECountry",
 *      required={""},
 *      @SWG\Property(
 *          property="countryID",
 *          description="countryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="countryShortCode",
 *          description="countryShortCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="CountryDes",
 *          description="CountryDes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="CountryTelCode",
 *          description="CountryTelCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="countryMasterID",
 *          description="countryMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="SchMasterId",
 *          description="SchMasterId",
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
class SMECountry extends Model
{

    public $table = 'srp_countrymaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'countryShortCode',
        'CountryDes',
        'CountryTelCode',
        'countryMasterID',
        'SchMasterId',
        'BranchID',
        'Erp_companyID',
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
        'countryID' => 'integer',
        'countryShortCode' => 'string',
        'CountryDes' => 'string',
        'CountryTelCode' => 'string',
        'countryMasterID' => 'integer',
        'SchMasterId' => 'integer',
        'BranchID' => 'integer',
        'Erp_companyID' => 'integer',
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
        'countryShortCode' => 'required',
        'CountryDes' => 'required'
    ];

    
}
